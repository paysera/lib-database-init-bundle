<?php
declare(strict_types=1);

namespace Paysera\Tests;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle;
use Exception;
use Paysera\Bundle\DatabaseInitBundle\PayseraDatabaseInitBundle;
use Paysera\Bundle\DatabaseInitBundle\Service\DatabaseExporter;
use Paysera\Bundle\DatabaseInitBundle\Service\DatabaseInitializer;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\MonologBundle\MonologBundle;
use Symfony\Component\Console\Input\ArrayInput;

class DatabaseExporterTest extends BundleTestCase
{
    private DatabaseExporter $databaseExporter;
    private DatabaseInitializer $databaseInitializer;
    private string $structureDirectory;
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

        $this->databaseExporter = $container->get('paysera_database_init.database_exporter');
        $this->databaseInitializer = $container->get('paysera_database_init.database_initializer');
        $this->structureDirectory = $container->getParameter('paysera_database_init.directory.structure');

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

    public function testDatabaseExporter(): void
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

            try {
                $expectedFilepath = $this->structureDirectory . DIRECTORY_SEPARATOR . sprintf('expected_%s.sql', $name);
                $this->assertEquals(
                    $this->fixContent(file_get_contents($filepath)),
                    $this->fixContent(file_get_contents($expectedFilepath))
                );
            } finally {
                unlink($filepath);
            }
        }
    }

    private function fixContent(string $fileContent): string
    {
        $result = str_replace('VALUES (', 'VALUES(', $fileContent);

        $result = preg_replace(
            '#\'\d+-\d+-\d+ \d+:\d+:\d+\',\d+#',
            '\'2023-09-08 00:00:00\',1',
            $result
        );

        return str_replace(["\r", "\n"], '', $result);
    }
}
