<?php

declare(strict_types=1);

namespace Z3\PHP\Development;

use Symfony\Component\Console\Application;
use Z3\PHP\Development\Command\JobWrapperCommand;
use Z3\PHP\Development\Job\PHPStanJob;

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
