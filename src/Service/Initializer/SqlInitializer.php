<?php

namespace Paysera\Bundle\DatabaseInitBundle\Service\Initializer;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Symfony\Component\Finder\Finder;

class SqlInitializer implements DatabaseInitializerInterface
{
    private $connection;
    private $sqlDirectory;

    /**
     * @param Connection $connection
     * @param string|null $sqlDirectory
     */
    public function __construct(
        Connection $connection,
        $sqlDirectory
    ) {
        $this->connection = $connection;
        $this->sqlDirectory = $sqlDirectory;
    }

    public function initialize()
    {
        if ($this->sqlDirectory === null) {
            return;
        }

        $finder = Finder::create()
            ->files()
            ->name('*.sql')
            ->in($this->sqlDirectory)
            ->sortByName()
        ;

        $this->connection->beginTransaction();
        try {
            /** @var \SplFileInfo $item */
            foreach ($finder as $item) {
                foreach (file($item->getRealPath()) as $row) {
                    $this->connection->query($row);
                }
            }
        } catch (DBALException $exception) {
            $this->connection->rollBack();
            return;
        }
        $this->connection->commit();
    }
}
