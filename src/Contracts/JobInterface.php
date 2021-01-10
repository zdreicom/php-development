<?php

declare(strict_types=1);

namespace Z3\PHP\Development\Contracts;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

interface JobInterface
{
    public function run(InputInterface $input, OutputInterface $output): int;
}
