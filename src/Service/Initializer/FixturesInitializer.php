<?php

namespace Paysera\Bundle\DatabaseInitBundle\Service\Initializer;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Paysera\Bundle\DatabaseInitBundle\Entity\InitializationMessage;
use Paysera\Bundle\DatabaseInitBundle\Entity\InitializationReport;

class FixturesInitializer implements DatabaseInitializerInterface
{
    private $loader;
    private $executor;
    private $fixturesDirectories;

    public function __construct(
        Loader $loader,
        ORMExecutor $executor,
        array $fixturesDirectories
    ) {
        $this->loader = $loader;
        $this->executor = $executor;
        $this->fixturesDirectories = $fixturesDirectories;
    }

    public function getName()
    {
        return 'fixtures';
    }

    public function initialize($setName)
    {

        if (count($this->fixturesDirectories) === 0) {
            return null;
        }

        $messages = [];

        $directories = array_values($this->fixturesDirectories);
        if ($setName !== null && isset($this->fixturesDirectories[$setName])) {
            $directories = [$this->fixturesDirectories[$setName]];
        }

        $fixtures = [];
        foreach ($directories as $directory) {
            $this->loader->loadFromDirectory($directory);
            $fixtures = array_merge($fixtures, $this->loader->getFixtures());
        }

        if (count($fixtures) > 0) {
            $this->executor->execute($fixtures);

            foreach ($fixtures as $fixture) {
                $message = new InitializationMessage();
                $messages[] = $message
                    ->setType(InitializationMessage::TYPE_SUCCESS)
                    ->setMessage(get_class($fixture))
                ;
            }
        }

        $report = new InitializationReport();
        return $report
            ->setMessages($messages)
            ->setInitializer($this->getName())
        ;
    }
}
