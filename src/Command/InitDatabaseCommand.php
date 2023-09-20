<?php

namespace Paysera\Bundle\DatabaseInitBundle\Command;

use Paysera\Bundle\DatabaseInitBundle\Entity\ProcessMessage;
use Paysera\Bundle\DatabaseInitBundle\Service\DatabaseInitializer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitDatabaseCommand extends Command
{
    private DatabaseInitializer $initializer;

    public function __construct(DatabaseInitializer $initializer)
    {
        parent::__construct();
        $this->initializer = $initializer;
    }

    protected function configure(): void
    {
        $this
            ->setName('paysera:db-init:init')
            ->addArgument('initializer', InputArgument::OPTIONAL, 'Specific single initializer to run')
            ->addArgument('set', InputArgument::OPTIONAL, 'Specific named set to run')
            ->setDescription('Initialize Database')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
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
                if ($message->getType() === ProcessMessage::TYPE_INFO) {
                    $totalFailed++;
                    if ($output->getVerbosity() > OutputInterface::VERBOSITY_NORMAL) {
                        $text = sprintf(
                            '<info>%s</info>: <comment>%s</comment>',
                            $report->getName(),
                            $message->getMessage()
                        );
                    }
                } elseif ($message->getType() === ProcessMessage::TYPE_SUCCESS) {
                    $totalSucceeded++;
                    if ($output->getVerbosity() > OutputInterface::VERBOSITY_NORMAL) {
                        $text = sprintf(
                            '<info>%s</info>: %s',
                            $report->getName(),
                            $message->getMessage()
                        );
                    }
                }

                if ($message->getType() === ProcessMessage::TYPE_ERROR) {
                    $totalFailed++;
                    $text = sprintf(
                        '<info>%s</info>: <error>%s</error>',
                        $report->getName(),
                        $message->getMessage()
                    );
                }
                $output->writeln($text);
            }
        }
        $output->writeln(sprintf('Total succeeded: <info>%s</info>', $totalSucceeded));
        $output->writeln(sprintf('Total failed: <info>%s</info>', $totalFailed));

        return Command::SUCCESS;
    }
}
