<?php
declare(strict_types=1);

namespace Paysera\Tests;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Paysera\Bundle\DatabaseInitBundle\PayseraDatabaseInitBundle;
use Paysera\Bundle\DatabaseInitBundle\Service\DatabaseExporter;
use Paysera\Bundle\DatabaseInitBundle\Service\DatabaseInitializer;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\MonologBundle\MonologBundle;
use Symfony\Component\Console\Input\ArrayInput;

class DatabaseExporterTest extends BundleTestCase
{
    /**
     * @var DatabaseExporter
     */
    private $databaseExporter;
    
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
    
    /**
     * @var string
     */
    private $structureDirectory;
    
    /**
     * @var Application
     */
    private $application;
    
    protected function setUp()
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
        
        $this->databaseExporter = $container->get('paysera_database_init.database_exporter');
        $this->databaseInitializer = $container->get('paysera_database_init.database_initializer');
        $this->connection = $container->get('database_connection');
        $this->entityManager = $container->get('doctrine.orm.entity_manager');
        $this->structureDirectory = $container->getParameter('paysera_database_init.directory.structure');
        
        $this->application = new Application(static::$kernel);
        $this->application->setAutoExit(false);
        $this->application->setCatchExceptions(false);
        $this->application->run(new ArrayInput([
            'command' => 'doctrine:migrations:migrate',
            '-q' => null,
        ]));
    }
    
    protected function tearDown()
    {
        $this->application->run(new ArrayInput([
            'command' => 'doctrine:schema:drop',
            '--full-database' => true,
            '--force' => true,
        ]));
    }
    
    public function testDatabaseExporter()
    {
        $this->databaseInitializer->initialize();
        
        $reports = $this->databaseExporter->export()->getMessages();
        $this->assertCount(3, $reports);
        
        $exportFiles = [
            'structure' => $this->structureDirectory . DIRECTORY_SEPARATOR . 'structure.sql',
            'configuration' => $this->structureDirectory . DIRECTORY_SEPARATOR . 'configuration.sql',
            'data' => $this->structureDirectory . DIRECTORY_SEPARATOR . 'data.sql',
        ];
        
        foreach ($exportFiles as $name => $filepath) {
            if (!file_exists($filepath)) {
                $this->fail(sprintf('%s file is not exported', $name));
            }
            
            $expectedFilepath = $this->structureDirectory . DIRECTORY_SEPARATOR . sprintf('expected_%s.sql', $name);
            $this->assertEquals(
                str_replace(["\r", "\n"], '', file_get_contents($filepath)),
                str_replace(["\r", "\n"], '', file_get_contents($expectedFilepath))
            );
            
            unlink($filepath);
        }
    }
}
