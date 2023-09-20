<?php
declare(strict_types=1);

namespace Paysera\Bundle\DatabaseInitBundle\Service\Exporter;

use InvalidArgumentException;
use Paysera\Bundle\DatabaseInitBundle\Service\SqlDumperInterface;
use Symfony\Component\Filesystem\Exception\IOException;

class DataExporter implements DatabaseExporterInterface
{
    /**
     * @var string[]
     */
    private array $tables;

    /**
     * @var string[]
     */
    private array $excludeTables;

    private SqlDumperInterface $dumper;

    private string $filepath;

    public function __construct(
        SqlDumperInterface $dumper,
        array $tables,
        string $filepath,
        array $excludeTables
    ) {
        $this->dumper = $dumper;
        $this->filepath = $filepath;

        $this->tables = [];
        foreach ($tables as $table) {
            $this->addTable($table);
        }

        $this->excludeTables = [];
        foreach ($excludeTables as $excludedTable) {
            $this->addExcludedTable($excludedTable);
        }
    }

    /**
     * @param string $name
     * @throws IOException
     * @throws InvalidArgumentException
     */
    public function export(string $name): void
    {
        if (!is_dir($this->filepath)) {
            throw new InvalidArgumentException('Directory does not exist');
        }

        $data = $this->dumper->dumpData($this->tables, $this->excludeTables);
        $result = file_put_contents(
            sprintf('%s%s%s.sql', $this->filepath, DIRECTORY_SEPARATOR, $name),
            $data
        );

        if ($result === false) {
            throw new IOException('Writing to file failed');
        }
    }

    private function addTable(string $table): void
    {
        $this->tables[] = $table;
    }

    private function addExcludedTable(string $excludedTable): void
    {
        $this->excludeTables[] = $excludedTable;
    }
}
