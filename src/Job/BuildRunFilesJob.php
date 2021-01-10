<?php

declare(strict_types=1);

namespace Z3\PHP\Development\Job;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
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
        if (\is_dir('run') !== true) {
            \mkdir('run');
        }
        $files = [
            'composer-update.sh',
            'docker-shell.sh',
            'phpfix.sh',
            'phpmd.sh',
            'phpstan.sh',
            'phpstyle.sh',
        ];
        foreach ($files as $file) {
            $this->copyFile($file);
        }
        return 0;
    }

    protected function copyFile(string $name): void
    {
        $copyFrom = 'vendor/z3/php-development/run/' . $name;
        $copyTo = 'run/' . $name;
        if (\file_exists($copyTo) === true) {
            \unlink($copyTo);
        }
        $content = \file_get_contents($copyFrom);
        \file_put_contents($copyTo, $content);
        \chmod($copyTo, 0750);
    }
}
