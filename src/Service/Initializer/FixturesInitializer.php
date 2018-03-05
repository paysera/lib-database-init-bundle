<?php

namespace Paysera\Bundle\DatabaseInitBundle\Service\Initializer;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;

class FixturesInitializer implements DatabaseInitializerInterface
{
    private $loader;
    private $executor;
    private $fixturesDirectory;

    /**
     * @param Loader $loader
     * @param ORMExecutor $executor
     * @param string|null $fixturesDirectory
     */
    public function __construct(
        Loader $loader,
        ORMExecutor $executor,
        $fixturesDirectory
    ) {
        $this->loader = $loader;
        $this->executor = $executor;
        $this->fixturesDirectory = $fixturesDirectory;
    }

    public function initialize()
    {
        if ($this->fixturesDirectory === null) {
            return;
        }

        $this->loader->loadFromDirectory($this->fixturesDirectory);
        $fixtures = $this->loader->getFixtures();
        if (count($fixtures) > 0) {
            $this->executor->execute($fixtures);
        }
    }
}
