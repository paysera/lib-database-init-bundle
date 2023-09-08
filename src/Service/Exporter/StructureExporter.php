<?php
declare(strict_types=1);

namespace Paysera\Bundle\DatabaseInitBundle\Service\Exporter;

use InvalidArgumentException;
use Paysera\Bundle\DatabaseInitBundle\Service\SqlDumperInterface;
use Symfony\Component\Filesystem\Exception\IOException;

class StructureExporter implements DatabaseExporterInterface
{
    /**
     * @var SqlDumperInterface
     */
    private $dumper;
    /**
     * @var string
     */
    private $filepath;

    public function __construct(SqlDumperInterface $dumper, string $filepath)
    {
        $this->dumper = $dumper;
        $this->filepath = $filepath;
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

        $data = $this->dumper->dumpStructure();
        $result = file_put_contents(
            sprintf('%s%s%s.sql', $this->filepath, DIRECTORY_SEPARATOR, $name),
            $data
        );

        if ($result === false) {
            throw new IOException('Writing to file failed');
        }
    }
}
