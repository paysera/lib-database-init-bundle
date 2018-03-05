<?php

namespace Paysera\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BundleTestCase extends WebTestCase
{
    protected static function getKernelClass()
    {
        require_once __DIR__ . '/TestAppKernel.php';

        return TestAppKernel::class;
    }

    protected static function createKernel(array $options = [])
    {
        $class = self::getKernelClass();

        if (!isset($options['base_dir'])) {
            throw new \InvalidArgumentException('The option "base_dir" must be set.');
        }

        /** @var TestAppKernel $kernel */
        $kernel = new $class(
            $options['base_dir'],
            isset($options['config_file']) ? $options['config_file'] : 'config.yml',
            isset($options['bundles']) ? $options['bundles'] : [],
            isset($options['test_case']) ? $options['test_case'] : null
        );

        return $kernel;
    }
}
