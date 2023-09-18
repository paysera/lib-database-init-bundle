<?php

namespace Paysera\Bundle\DatabaseInitBundle\DependencyInjection;

use Exception;
use InvalidArgumentException;
use Paysera\Bundle\DatabaseInitBundle\Service\Exporter\DataExporter;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class PayseraDatabaseInitExtension extends Extension
{
    /**
     * {@inheritdoc}
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('paysera_database_init.directory.sql', $config['directories']['sql']);
        $container->setParameter('paysera_database_init.directory.fixtures', $config['directories']['fixtures']);
        $container->setParameter('paysera_database_init.directory.structure', $config['directories']['structure']);

        foreach ($config['exports'] as $key => $export) {
            $definition = new Definition(DataExporter::class);
            $definition->addTag('paysera_database_init.exporter', [
                'type' => $export['name']
            ]);
            $definition
                ->addArgument(new Reference('paysera_database_init.mysql_dumper'))
                ->addArgument($export['tables'])
                ->addArgument($export['directory'])
            ;
            $invertTablesFrom = [];
            if (isset($export['invert_tables_from'])) {
                if (!isset($config['exports'][$export['invert_tables_from']])) {
                    throw new InvalidArgumentException(
                        sprintf('%s export configuration does not exist', $export['invert_tables_from'])
                    );
                }
                $invertTablesFrom = $config['exports'][$export['invert_tables_from']]['tables'];
            }
            $definition->addArgument($invertTablesFrom);

            $container->setDefinition(sprintf('paysera_database_init.exporter.%s', $key), $definition);
        }

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');
    }
}
