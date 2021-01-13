<?php

declare(strict_types=1);

namespace Z3\PHP\Development\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Z3\PHP\Development\Contracts\JobInterface;

class JobWrapperCommand extends Command
{

    /**
     * @var string
     */
    private $name = '';

    /**
     * @var string
     */
    private $description = '';

    /**
     * @var JobInterface
     * @phpstan-ignore-next-line
     */
    private $job = '';

    /**
     * @var InputArgument[]
     */
    private $arguments = [];

    /**
     * JobWrapperCommand constructor.
     * @param string $name
     * @param string $description
     * @param JobInterface $job
     * @param InputArgument[] $arguments
     */
    public function __construct(string $name, string $description, JobInterface $job, array $arguments)
    {
        $this->name = $name;
        $this->description = $description;
        $this->job = $job;
        $this->arguments = $arguments;
        parent::__construct();
    }

    /**
     * @phpstan-ignore-next-line
     */
    protected function configure()
    {
        $this
            ->setName($this->name)
            ->setDescription($this->description);

        foreach ($this->arguments as $argument) {
            $this->getDefinition()->addArgument($argument);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $exitCode = 1;
        try {
            $exitCode = $this->job->run($input, $output);
        } catch (\Throwable $exception) {
            $output->writeln($exception->getMessage());
            return $exitCode;
        }
        return $exitCode;
    }
}
