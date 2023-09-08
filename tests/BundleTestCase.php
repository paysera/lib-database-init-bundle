<?php

namespace Paysera\Tests;

use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BundleTestCase extends WebTestCase
{
    protected static function getKernelClass(): string
    {
        require_once __DIR__ . '/TestAppKernel.php';

        return TestAppKernel::class;
    }

    protected static function createKernel(array $options = []): TestAppKernel
    {
        $class = self::getKernelClass();

        if (!isset($options['base_dir'])) {
            throw new InvalidArgumentException('The option "base_dir" must be set.');
        }

        /** @var TestAppKernel $kernel */
        $kernel = new $class(
            $options['base_dir'],
            $options['config_file'] ?? 'config.yml',
            $options['bundles'] ?? [],
            $options['test_case'] ?? null
        );

        return $kernel;
    }
}
