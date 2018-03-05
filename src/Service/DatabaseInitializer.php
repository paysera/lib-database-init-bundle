<?php

namespace Paysera\Bundle\DatabaseInitBundle\Service;

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

    public function initialize()
    {
        foreach ($this->initializers as $initializer) {
            $initializer->initialize();
        }
    }
}
