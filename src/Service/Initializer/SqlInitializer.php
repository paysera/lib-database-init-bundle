<?php

namespace Paysera\Bundle\DatabaseInitBundle\Service\Initializer;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Exception\DriverException;
use Doctrine\DBAL\Exception\TableExistsException;
use Paysera\Bundle\DatabaseInitBundle\Entity\InitializationMessage;
use Paysera\Bundle\DatabaseInitBundle\Entity\InitializationReport;
use Psr\Log\LoggerInterface;
use Symfony\Component\Finder\Finder;

class SqlInitializer implements DatabaseInitializerInterface
{
    private $connection;
    private $logger;
    private $sqlDirectory;

    /**
     * @param Connection $connection
     * @param LoggerInterface $logger
     * @param string|null $sqlDirectory
     */
    public function __construct(
        Connection $connection,
        LoggerInterface $logger,
        $sqlDirectory
    ) {
        $this->connection = $connection;
        $this->logger = $logger;
        $this->sqlDirectory = $sqlDirectory;
    }

    public function initialize()
    {
        if ($this->sqlDirectory === null) {
            return null;
        }

        $finder = Finder::create()
            ->files()
            ->name('*.sql')
            ->in($this->sqlDirectory)
            ->sortByName()
        ;

        $messages = [];

        /** @var \SplFileInfo $item */
        foreach ($finder as $item) {
            $contents = file_get_contents($item->getRealPath());
            foreach (explode(";\n", $contents) as $query) {
                $query = trim($query);
                if (strlen($query) === 0) {
                    continue;
                }
                try {
                    $this->connection->query($query);
                    $messages[] = $this->buildSuccessMessage($query);
                } catch (TableExistsException $exception) {
                    $messages[] = $this->processDuplicateTable($exception);
                } catch (DriverException $exception) {
                    $messages[] = $this->processDuplicateIndex($exception);
                } catch (DBALException $exception) {
                    $this->logger->warning('Got Database exception while executing SQL', [$exception]);
                    $messages[] = $this->processBaseException($exception);
                }
            }
        }

        $report = new InitializationReport();
        return $report
            ->setMessages($messages)
            ->setInitializer('SQL')
        ;
    }

    private function buildSuccessMessage($query)
    {
        $message = new InitializationMessage();
        return $message
            ->setType(InitializationMessage::TYPE_SUCCESS)
            ->setMessage($query)
        ;
    }

    private function processDuplicateTable(TableExistsException $exception)
    {
        $message = new InitializationMessage();

        if (preg_match('#table \'(\w+)\' already exists#i', $exception->getMessage(), $matches) !== false) {
            return $message
                ->setType(InitializationMessage::TYPE_INFO)
                ->setMessage(sprintf('Duplicate table "%s"', $matches[1]))
            ;
        }

        return $this->processBaseException($exception);
    }

    private function processDuplicateIndex(DriverException $exception)
    {
        $message = new InitializationMessage();

        if (preg_match('#duplicate key name \'(\w+)\'#i', $exception->getMessage(), $matches) !== false) {
            return $message
                ->setType(InitializationMessage::TYPE_INFO)
                ->setMessage(sprintf('Duplicate index "%s"', $matches[1]))
            ;
        }

        return $this->processBaseException($exception);
    }

    private function processBaseException(DBALException $exception)
    {
        $message = new InitializationMessage();

        return $message
            ->setType(InitializationMessage::TYPE_ERROR)
            ->setMessage($exception->getMessage())
        ;
    }
}
