<?php
declare(strict_types=1);

namespace Paysera\Bundle\DatabaseInitBundle\Service\Exporter;

interface DatabaseExporterInterface
{
    public function export(string $name);
}
