<?php

declare(strict_types=1);

namespace Z3\PHP\Development\Job;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Z3\PHP\Development\Contracts\JobInterface;

class BuildRunFilesJob implements JobInterface
{
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    public function run(InputInterface $input, OutputInterface $output): int
    {
        if(\is_dir('run') !== true) {
            \mkdir('run');
        }
        $files = [
            'composer-update.sh',
            'php-style.sh',
            'docker-shell.sh'
        ];
        foreach ($files as $file) {
            $this->copyFile($file);
        }
        return 0;
    }

    protected function copyFile(string $name)
    {
        $from = 'vendor/z3/php-development/run/' . $name;
        $to = 'run/' . $name;
        if(\file_exists($to) === true) {
            \unlink($to);
        }
        $content = \file_get_contents($from);
        \file_put_contents($to, $content);
        \chmod($to, 0750);
    }
}
