<?php

namespace Paysera\Bundle\DatabaseInitBundle\Service;

use Paysera\Bundle\DatabaseInitBundle\Entity\InitializationReport;
use Paysera\Bundle\DatabaseInitBundle\Service\Initializer\DatabaseInitializerInterface;
use SplPriorityQueue;

class DatabaseInitializer
{
    /**
     * @var SplPriorityQueue|DatabaseInitializerInterface[]
     */
    private $initializers;

    public function __construct()
    {
        $this->initializers = new SplPriorityQueue();
    }

    /**
     * @param DatabaseInitializerInterface $initializer
     * @param int $priority
     */
    public function addInitializer(DatabaseInitializerInterface $initializer, $priority)
    {
        $this->initializers->insert($initializer, $priority);
    }

    /**
     * @param string|null $initializerName
     * @param string|null $setName
     * @return InitializationReport[]
     */
    public function initialize($initializerName = null, $setName = null)
    {
        $reports = [];
        foreach ($this->initializers as $initializer) {
            if ($initializerName !== null) {
                if ($initializer->getName() === $initializerName) {
                    $reports[] = $initializer->initialize($setName);
                }
            } else {
                $reports[] = $initializer->initialize($setName);
            }
        }

        return array_filter($reports);
    }
}
