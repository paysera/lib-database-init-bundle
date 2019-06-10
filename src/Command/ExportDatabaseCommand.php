<?php
declare(strict_types=1);

namespace Paysera\Bundle\DatabaseInitBundle\Command;

use Paysera\Bundle\DatabaseInitBundle\Entity\ProcessMessage;
use Paysera\Bundle\DatabaseInitBundle\Service\DatabaseExporter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExportDatabaseCommand extends Command
{
    private $exporter;

    public function __construct(DatabaseExporter $exporter)
    {
        parent::__construct();
        $this->exporter = $exporter;
    }

    protected function configure()
    {
        $this
            ->setName('paysera:db-init:export')
            ->addArgument('exporter', InputArgument::OPTIONAL, 'Specific single exporter to run')
            ->setDescription('Export Database')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $report = $this->exporter->export(
            $input->getArgument('exporter')
        );

        $totalFailed = 0;
        $totalSucceeded = 0;
    
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
            } elseif ($message->getType() === ProcessMessage::TYPE_ERROR) {
                $totalFailed++;
                $text = sprintf(
                    '<info>%s</info>: <error>%s</error>',
                    $report->getName(),
                    $message->getMessage()
                );
            }
            $output->writeln($text);
        }
        $output->writeln(sprintf('Total succeeded: <info>%s</info>', $totalSucceeded));
        $output->writeln(sprintf('Total failed: <info>%s</info>', $totalFailed));
    }
}
