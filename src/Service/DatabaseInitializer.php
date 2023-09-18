<?php
declare(strict_types=1);

namespace Paysera\Bundle\DatabaseInitBundle\Service;

use Paysera\Bundle\DatabaseInitBundle\Entity\ProcessReport;
use Paysera\Bundle\DatabaseInitBundle\Service\Initializer\DatabaseInitializerInterface;

class DatabaseInitializer
{
    /**
     * @var DatabaseInitializerInterface[]
     */
    private array $initializers;

    public function __construct()
    {
        $this->initializers = [];
    }

    public function addInitializer(DatabaseInitializerInterface $initializer, string $name)
    {
        $this->initializers[$name] = $initializer;
    }

    /**
     * @return ProcessReport[]
     */
    public function initialize(string $initializerName = null, string $setName = null): array
    {
        $reports = [];
        foreach ($this->initializers as $name => $initializer) {
            if ($initializerName !== null) {
                if ($name === $initializerName) {
                    $reports[] = $initializer->initialize($name, $setName);
                }
            } else {
                $reports[] = $initializer->initialize($name, $setName);
            }
        }

        return array_filter($reports);
    }
}
