<?php

namespace Paysera\Bundle\DatabaseInitBundle\Command;

use Paysera\Bundle\DatabaseInitBundle\Entity\InitializationMessage;
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
        $reports = $this->initializer->initialize();

        foreach ($reports as $report) {
            foreach ($report->getMessages() as $message) {
                $text = sprintf('<info>%s</info>: ', $report->getInitializer());
                if ($message->getType() === InitializationMessage::TYPE_INFO) {
                    $text .= $message->getMessage();
                } elseif ($message->getType() === InitializationMessage::TYPE_SUCCESS) {
                    $text .= sprintf('<comment>%s</comment>', $message->getMessage());
                } elseif ($message->getType() === InitializationMessage::TYPE_ERROR) {
                    $text .= sprintf('<error>%s</error>', $message->getMessage());
                }
                $output->writeln($text);
            }
        }
    }
}
