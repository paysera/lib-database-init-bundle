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
     * @return InitializationReport[]
     */
    public function initialize()
    {
        $reports = [];
        foreach ($this->initializers as $initializer) {
            $reports[] = $initializer->initialize();
        }

        return array_filter($reports);
    }
}
