<?php
declare(strict_types=1);

namespace Paysera\Bundle\DatabaseInitBundle\Service;

interface SqlDumperInterface
{
    /**
     * @param string[] $tables
     * @return string
     */
    public function dumpStructure(array $tables = []): string;
    
    /**
     * @param string[] $tables
     * @param string[] $excludeTables
     * @return string
     */
    public function dumpData(array $tables = [], array $excludeTables = []): string;
}
