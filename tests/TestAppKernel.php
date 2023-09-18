<?php

namespace Paysera\Tests;

use Exception;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;

class TestAppKernel extends Kernel
{
    private string $baseDir;
    private string $configFile;
    /**
     * @var BundleInterface[]
     */
    private array $additionalBundles;
    private ?string $testCase;

    public function __construct(string $baseDir, string $configFile, array $additionalBundles, ?string $testCase)
    {
        $this->baseDir = $baseDir;
        $this->configFile = $configFile;
        $this->additionalBundles = $additionalBundles;
        $this->testCase = $testCase;

        if ($testCase !== null && !is_dir($baseDir . '/' . $testCase)) {
            throw new InvalidArgumentException(sprintf('The test case "%s" does not exist.', $testCase));
        }

        if ($testCase === null) {
            $configFilePath = $baseDir . '/' . $configFile;
        } else {
            $configFilePath = $baseDir . '/' . $testCase . '/' . $configFile;
        }

        if (!is_file($configFilePath)) {
            throw new InvalidArgumentException(sprintf('The config file "%s" does not exist.', $configFile));
        }

        parent::__construct('test', true);
    }

    public function registerBundles(): array
    {
        return array_merge(
            [
                new FrameworkBundle(),
            ],
            $this->additionalBundles
        );
    }

    public function getCacheDir(): string
    {
        if ($this->testCase !== null) {
            return $this->baseDir . '/cache/' . $this->testCase . '/cache/' . $this->environment;
        }
        return $this->baseDir . '/cache/' . $this->environment;
    }

    public function getLogDir(): string
    {
        if ($this->testCase !== null) {
            return $this->baseDir . '/logs/' . $this->testCase . '/logs';
        }
        return $this->baseDir . '/logs';
    }

    /**
     * @throws Exception
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->baseDir . '/' . $this->configFile);
    }

    public function serialize(): string
    {
        return serialize([$this->baseDir, $this->configFile, $this->bundles, $this->testCase]);
    }

    public function unserialize($str)
    {
        $a = unserialize($str);
        $this->__construct($a[0], $a[1], $a[2], $a[3]);
    }

    protected function getKernelParameters(): array
    {
        $parameters = parent::getKernelParameters();
        $parameters['kernel.test_case'] = $this->testCase;

        return $parameters;
    }
}
