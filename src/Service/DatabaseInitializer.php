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
    private $initializers;

    public function __construct()
    {
        $this->initializers = [];
    }
    
    /**
     * @param DatabaseInitializerInterface $initializer
     * @param string $name
     */
    public function addInitializer(DatabaseInitializerInterface $initializer, string $name)
    {
        $this->initializers[$name] = $initializer;
    }

    /**
     * @param string|null $initializerName
     * @param string|null $setName
     * @return ProcessReport[]
     */
    public function initialize($initializerName = null, $setName = null)
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
