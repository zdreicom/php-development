<?php

declare(strict_types=1);

namespace Z3\PHP\Development;

use Symfony\Component\Console\Application;
use Z3\PHP\Development\Command\JobWrapperCommand;
use Z3\PHP\Development\Job\PHPFixJob;
use Z3\PHP\Development\Job\PHPMessDetectorJob;
use Z3\PHP\Development\Job\PHPStanJob;
use Z3\PHP\Development\Job\PHPStyleJob;

class EntryPoint
{
    protected Application $application;

    /**
     * @throws \Exception
     */
    public function run(): void
    {
        $this->application = new Application();
        $this->addJob(
            'run:stan',
            '',
            PHPStanJob::class
        );
        $this->addJob(
            'run:style',
            '',
            PHPStyleJob::class
        );
        $this->addJob(
            'run:fix',
            '',
            PHPFixJob::class
        );
        $this->addJob(
            'run:md',
            '',
            PHPMessDetectorJob::class
        );
        $this->application->run();
    }

    /**
     * @param string $name
     * @param string $description
     * @param string $jobClassName
     */
    protected function addJob(string $name, string $description, string $jobClassName): void
    {
        $command = new JobWrapperCommand($name, $description, new $jobClassName);
        $this->application->add($command);
    }
}
