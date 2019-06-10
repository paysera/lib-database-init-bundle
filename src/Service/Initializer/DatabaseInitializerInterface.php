<?php
declare(strict_types=1);

namespace Paysera\Bundle\DatabaseInitBundle\Service\Initializer;

use Paysera\Bundle\DatabaseInitBundle\Entity\ProcessReport;

interface DatabaseInitializerInterface
{
    /**
     * @param string $initializerName
     * @param string|null $setName
     * @return ProcessReport|null
     */
    public function initialize(string $initializerName, string $setName = null);
}
