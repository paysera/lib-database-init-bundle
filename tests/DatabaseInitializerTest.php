<?php

namespace Paysera\Tests;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Paysera\Bundle\DatabaseInitBundle\Service\DatabaseInitializer;
use Paysera\Tests\Entity\Dummy;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;

class DatabaseInitializerTest extends BundleTestCase
{
    /**
     * @var DatabaseInitializer
     */
    private $databaseInitializer;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var EntityManager
     */
    private $entityManager;

    protected function setUp()
    {
        static::bootKernel([
            'base_dir' => __DIR__ . '/symfony',
            'bundles' => [
                new \Paysera\Bundle\DatabaseInitBundle\PayseraDatabaseInitBundle(),
                new \Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
                new \Symfony\Bundle\MonologBundle\MonologBundle(),
                new \Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
            ]
        ]);

        $container = static::$kernel->getContainer();

        $this->databaseInitializer = $container->get('paysera_database_init.database_initializer');
        $this->connection = $container->get('database_connection');
        $this->entityManager = $container->get('doctrine.orm.entity_manager');

        $application = new Application(static::$kernel);
        $application->setAutoExit(false);
        $application->setCatchExceptions(false);
        $application->run(new ArrayInput([
            'command' => 'doctrine:migrations:migrate',
            '-q' => null,
        ]));
    }

    public function testDatabaseInitializer()
    {
        $reports = $this->databaseInitializer->initialize();

        $this->assertCount(2, $reports);

        $sqlReport = $reports[0];
        $fixtureReport = $reports[1];

        $this->assertCount(3, $sqlReport->getMessages());
        $this->assertCount(1, $fixtureReport->getMessages());

        $countPlain = $this->connection->query('SELECT count(*) FROM table_1;')->fetchColumn();
        $this->assertEquals(2, $countPlain);

        $countManaged = $this->entityManager->getRepository(Dummy::class)->findAll();
        $this->assertEquals(2, count($countManaged));
    }
}
