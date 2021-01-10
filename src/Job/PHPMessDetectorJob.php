<?php

declare(strict_types=1);

namespace Z3\PHP\Development\Job;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Z3\PHP\Development\Contracts\JobInterface;

class PHPMessDetectorJob implements JobInterface
{
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    public function run(InputInterface $input, OutputInterface $output): int
    {
        error_reporting(E_ALL ^ E_DEPRECATED);

        $processCommand = [
            'vendor/z3/php-development/binary/php-md',
            'packages',
            'text',
            'cleancode,codesize,controversial,design,naming,unusedcode'
        ];
        $process = new Process($processCommand);
        $process->run();
        if (!$process->isSuccessful()) {
            $output->write($process->getOutput());
            return 100;
        }
        return 0;
    }
}
