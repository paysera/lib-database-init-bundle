<?php
declare(strict_types=1);

namespace Paysera\Bundle\DatabaseInitBundle\Service;

use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;
use Symfony\Component\Process\Process;

class MySqlDumper implements SqlDumperInterface
{
    private Connection $connection;
    private LoggerInterface $logger;

    public function __construct(Connection $connection, LoggerInterface $logger)
    {
        $this->connection = $connection;
        $this->logger = $logger;
    }

    /**
     * @param string[] $tables
     * @return string
     */
    public function dumpStructure(array $tables = []): string
    {
        $options = [
            '--no-data',
            '--skip-add-drop-table',
        ];

        return $this->dump($options, $tables);
    }

    /**
     * @param string[] $tables
     * @param string[] $excludeTables
     * @return string
     */
    public function dumpData(array $tables = [], array $excludeTables = []): string
    {
        $options = [
            '--no-create-info',
        ];

        foreach ($excludeTables as $excludeTable) {
            $options[] = sprintf('--ignore-table=%s.%s', $this->connection->getDatabase(), $excludeTable);
        }

        return implode("\n", [
            'SET FOREIGN_KEY_CHECKS=0;',
            $this->dump($options, $tables),
            'SET FOREIGN_KEY_CHECKS=1;',
        ]);
    }

    /**
     * @param string[] $options
     * @param string[] $tables
     * @return string
     */
    private function dump(array $options, array $tables = []): string
    {
        $options[] = '--compact';
        $connectionParams = $this->connection->getParams();

        $process = new Process(array_merge(
            [
                'mysqldump',
                '-h',
                $connectionParams['host'],
                '-P',
                $connectionParams['port'],
                '-u',
                $connectionParams['user'],
                '--password=' . $connectionParams['password'],
            ],
            $options,
            [
                $this->connection->getDatabase(),
            ],
            $tables
        ));

        $process->mustRun();

        $this->logger->info(
            sprintf(
                'Dump %s of %s in %s',
                implode(',', $tables),
                $this->connection->getDatabase(),
                $connectionParams['host']
            )
        );

        return $process->getOutput();
    }
}
