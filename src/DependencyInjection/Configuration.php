<?php

namespace Paysera\Bundle\DatabaseInitBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('paysera_database_init');

        $rootNode
            ->children()
                ->arrayNode('directories')
                    ->children()
                        ->arrayNode('sql')
                            ->useAttributeAsKey('name')
                            ->prototype('scalar')->end()
                        ->end()
                        ->arrayNode('fixtures')
                            ->useAttributeAsKey('name')
                            ->prototype('scalar')->end()
                        ->end()
                        ->scalarNode('structure')
                            ->isRequired()
                        ->end()
                    ->end()
                    ->end()
                ->arrayNode('exports')
                    ->canBeUnset()
                    ->useAttributeAsKey('key')
                    ->prototype('array')
                    ->children()
                        ->scalarNode('name')->end()
                        ->arrayNode('tables')
                            ->prototype('scalar')->end()
                            ->defaultValue([])
                        ->end()
                        ->scalarNode('invert_tables_from')->end()
                        ->scalarNode('directory')->end()
                ->end()
            ->end()
            ->end();
        ;

        return $treeBuilder;
    }
}
