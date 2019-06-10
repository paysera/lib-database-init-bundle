<?php
declare(strict_types=1);

namespace Paysera\Bundle\DatabaseInitBundle\Service\Exporter;

use \InvalidArgumentException;
use Paysera\Bundle\DatabaseInitBundle\Service\SqlDumperInterface;
use Symfony\Component\Filesystem\Exception\IOException;

class DataExporter implements DatabaseExporterInterface
{
    /**
     * @var string[]
     */
    private $tables;
    
    /**
     * @var string[]
     */
    private $excludeTables;
    
    private $dumper;
    private $filepath;

    public function __construct(
        SqlDumperInterface $dumper,
        array $tables,
        string $filepath,
        array $excludeTables
    ) {
        $this->dumper = $dumper;
        $this->filepath = $filepath;
        $this->tables = $tables;
        $this->excludeTables = $excludeTables;
    }
    
    /**
     * @param string $name
     * @throws IOException
     * @throws InvalidArgumentException
     */
    public function export(string $name)
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
}
