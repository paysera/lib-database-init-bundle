<?php

namespace Paysera\Bundle\DatabaseInitBundle\Command;

use Paysera\Bundle\DatabaseInitBundle\Entity\InitializationMessage;
use Paysera\Bundle\DatabaseInitBundle\Service\DatabaseInitializer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
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
            ->addArgument('initializer', InputArgument::OPTIONAL, 'Specific single initializer to run')
            ->addArgument('set', InputArgument::OPTIONAL, 'Specific named set to run')
            ->setDescription('Initialize Database')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $reports = $this->initializer->initialize(
            $input->getArgument('initializer'),
            $input->getArgument('set')
        );

        $totalFailed = 0;
        $totalSucceeded = 0;
        foreach ($reports as $report) {
            foreach ($report->getMessages() as $message) {
                $text = null;
                if ($message->getType() === InitializationMessage::TYPE_INFO) {
                    $totalFailed++;
                    if ($output->getVerbosity() > OutputInterface::VERBOSITY_NORMAL) {
                        $text = sprintf(
                            '<info>%s</info>: <comment>%s</comment>',
                            $report->getInitializer(),
                            $message->getMessage()
                        );
                    }
                } elseif ($message->getType() === InitializationMessage::TYPE_SUCCESS) {
                    $totalSucceeded++;
                    if ($output->getVerbosity() > OutputInterface::VERBOSITY_NORMAL) {
                        $text = sprintf(
                            '<info>%s</info>: %s',
                            $report->getInitializer(),
                            $message->getMessage()
                        );
                    }
                }

                if ($message->getType() === InitializationMessage::TYPE_ERROR) {
                    $totalFailed++;
                    $text = sprintf(
                        '<info>%s</info>: <error>%s</error>',
                        $report->getInitializer(),
                        $message->getMessage()
                    );
                }
                $output->writeln($text);
            }
        }
        $output->writeln(sprintf('Total succeeded: <info>%s</info>', $totalSucceeded));
        $output->writeln(sprintf('Total failed: <info>%s</info>', $totalFailed));
    }
}
