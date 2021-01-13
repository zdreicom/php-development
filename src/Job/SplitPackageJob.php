<?php

declare(strict_types=1);

namespace Z3\PHP\Development\Job;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Z3\PHP\Development\Contracts\JobInterface;

class SplitPackageJob implements JobInterface
{
    protected const PATH_TO_SPLIT = 'vendor/z3/php-development/lib/splitsh-lite';

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    public function run(InputInterface $input, OutputInterface $output): int
    {
        $packagePath = $input->getArgument('package-path');
        $packageName = $input->getArgument('package-name');
        $remoteRepository = $input->getArgument('remote-repository');

        if (\is_string($packagePath) === false) {
            $output->writeln('package-path must be an string');
            return 1;
        }
        if (\is_string($packageName) === false) {
            $output->writeln('package-name must be an string');
            return 1;
        }
        if (\is_string($remoteRepository) === false) {
            $output->writeln('remote-repository must be an string');
            return 1;
        }

        $this->addRemote($packageName, $remoteRepository);
        $hash = $this->buildHash($packagePath);
        $this->pushRemote($packageName, $hash);
        return 0;
    }

    protected function addRemote(string $packagePath, string $remoteRepository): void
    {
        $processCommand = [
            'git',
            'remote',
            'add',
            $packagePath,
            $remoteRepository
        ];
        $process = new Process($processCommand);
        $process->run();
    }

    protected function buildHash(string $packagePath): string
    {
        $processCommand = [
            self::PATH_TO_SPLIT,
            '--prefix=' . $packagePath
        ];
        $process = new Process($processCommand);
        $process->run();
        $output = $process->getOutput();
        return \substr($output, 0, 40);
    }

    protected function pushRemote(string $packageName, string $hash): void
    {
        $processCommand = [
            'git',
            'push',
            $packageName,
            $hash . ':refs/heads/master',
            '-f'
        ];
        $process = new Process($processCommand);
        $process->mustRun();
    }
}
