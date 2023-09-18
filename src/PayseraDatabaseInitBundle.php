<?php
declare(strict_types=1);

namespace Paysera\Bundle\DatabaseInitBundle;

use Paysera\Component\DependencyInjection\AddTaggedCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class PayseraDatabaseInitBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass((new AddTaggedCompilerPass(
            'paysera_database_init.database_initializer',
            'paysera_database_init.initializer',
            'addInitializer',
            ['type']
        ))->enablePriority());

        $container->addCompilerPass(new AddTaggedCompilerPass(
            'paysera_database_init.database_exporter',
            'paysera_database_init.exporter',
            'addExporter',
            ['type']
        ));
    }
}
