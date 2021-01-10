<?php

declare(strict_types=1);

namespace Z3\PHP\Development\Job;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Z3\PHP\Development\Contracts\JobInterface;

class PHPStanJob implements JobInterface
{
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    public function run(InputInterface $input, OutputInterface $output): int
    {
        $processCommand = [
            'vendor/phpstan/phpstan/phpstan',
            'analyse',
            '-c',
            'vendor/z3/php-development/configuration/phpstan.neon',
            '--level',
            'max',
            'packages',
            'tests'
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
