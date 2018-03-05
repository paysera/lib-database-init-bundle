<?php

namespace Paysera\Bundle\DatabaseInitBundle\Command;

use Paysera\Bundle\DatabaseInitBundle\Service\DatabaseInitializer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitDatabaseCommand extends Command
{
    private $initializer;

    public function __construct(DatabaseInitializer $initializer)
    {
        parent::__construct();
        $this->initializer = $initializer;
    }

    protected function configure()
    {
        $this
            ->setName('paysera:db-init:init')
            ->setDescription('Initialize Database')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->initializer->initialize();
    }
}
