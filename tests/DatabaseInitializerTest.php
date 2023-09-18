<?php
declare(strict_types=1);

namespace Paysera\Tests;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\NotSupported;
use Exception;
use Paysera\Bundle\DatabaseInitBundle\Service\DatabaseInitializer;
use Paysera\Tests\Entity\Dummy;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Paysera\Bundle\DatabaseInitBundle\PayseraDatabaseInitBundle;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Symfony\Bundle\MonologBundle\MonologBundle;
use Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle;

class DatabaseInitializerTest extends BundleTestCase
{
    private DatabaseInitializer $databaseInitializer;
    private Connection $connection;
    private EntityManager $entityManager;
    private Application $application;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        static::bootKernel([
            'base_dir' => __DIR__ . '/symfony',
            'bundles' => [
                new PayseraDatabaseInitBundle(),
                new DoctrineBundle(),
                new MonologBundle(),
                new DoctrineMigrationsBundle(),
            ]
        ]);

        $container = static::$kernel->getContainer();

        $this->databaseInitializer = $container->get('paysera_database_init.database_initializer');
        $this->connection = $container->get('database_connection');
        $this->entityManager = $container->get('doctrine.orm.entity_manager');

        $this->application = new Application(static::$kernel);
        $this->application->setAutoExit(false);
        $this->application->setCatchExceptions(false);
        $this->application->run(new ArrayInput([
            'command' => 'doctrine:migrations:migrate',
            '-q' => null,
        ]));
    }

    /**
     * @throws Exception
     */
    protected function tearDown(): void
    {
        $this->application->run(new ArrayInput([
            'command' => 'doctrine:schema:drop',
            '--full-database' => true,
            '--force' => true,
        ]));
    }

    /**
     * @throws NotSupported
     * @throws Exception
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    public function testDatabaseInitializer(): void
    {
        $reports = $this->databaseInitializer->initialize();

        $this->assertCount(2, $reports);

        $sqlReport = $reports[0];
        $fixtureReport = $reports[1];

        $this->assertCount(3, $sqlReport->getMessages());
        $this->assertCount(1, $fixtureReport->getMessages());

        $countPlain = $this->connection->executeQuery(/** @lang text */'SELECT count(*) FROM table_1;')->fetchOne();
        $this->assertEquals(2, $countPlain);

        $countManaged = $this->entityManager->getRepository(Dummy::class)->findAll();
        $this->assertCount(2, $countManaged);
    }
}
