<?php
declare(strict_types=1);

namespace Paysera\Bundle\DatabaseInitBundle\Service\Initializer;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\DriverException;
use Doctrine\DBAL\Exception\TableExistsException;
use Exception;
use Paysera\Bundle\DatabaseInitBundle\Entity\ProcessMessage;
use Paysera\Bundle\DatabaseInitBundle\Entity\ProcessReport;
use Psr\Log\LoggerInterface;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

class SqlInitializer implements DatabaseInitializerInterface
{
    private $connection;
    private $logger;
    private $sqlDirectories;

    public function __construct(
        Connection $connection,
        LoggerInterface $logger,
        array $sqlDirectories
    ) {
        $this->connection = $connection;
        $this->logger = $logger;
        $this->sqlDirectories = $sqlDirectories;
    }

    public function initialize(string $initializerName, string $setName = null): ?ProcessReport
    {
        if (count($this->sqlDirectories) === 0) {
            return null;
        }

        $directories = array_values($this->sqlDirectories);
        if ($setName !== null && isset($this->sqlDirectories[$setName])) {
            $directories = [$this->sqlDirectories[$setName]];
        }

        $finder = Finder::create()
            ->files()
            ->name('*.sql')
            ->in($directories)
            ->sortByName()
        ;

        $messages = [];

        /** @var SplFileInfo $item */
        foreach ($finder as $item) {
            $contents = file_get_contents($item->getRealPath());
            $contents = preg_replace('#;\s+$#m', ';', $contents);

            foreach (explode(";\n", $contents) as $query) {
                $query = trim($query);
                if (strlen($query) === 0) {
                    continue;
                }
                try {
                    $this->connection->executeQuery($query);
                    $messages[] = $this->buildSuccessMessage($query);
                } catch (TableExistsException $exception) {
                    $messages[] = $this->processDuplicateTable($exception);
                } catch (DriverException $exception) {
                    $messages[] = $this->processDuplicateIndexColumnRecord($exception);
                } catch (Exception $exception) {
                    $this->logger->warning('Got Database exception while executing SQL', [$exception]);
                    $messages[] = $this->processBaseException($exception);
                }
            }
        }

        $report = new ProcessReport();
        return $report
            ->setMessages(array_filter($messages))
            ->setName($initializerName)
        ;
    }

    private function buildSuccessMessage($query): ProcessMessage
    {
        $message = new ProcessMessage();
        return $message
            ->setType(ProcessMessage::TYPE_SUCCESS)
            ->setMessage($query)
        ;
    }

    private function processDuplicateTable(TableExistsException $exception): ProcessMessage
    {
        $message = new ProcessMessage();

        if (
            preg_match('#table \'(\w+)\' already exists#i', $exception->getMessage(), $matches) !== false
            && isset($matches[1])
        ) {
            return $message
                ->setType(ProcessMessage::TYPE_INFO)
                ->setMessage(sprintf('Duplicate table "%s"', $matches[1]))
            ;
        }

        return $this->processBaseException($exception);
    }

    private function processDuplicateIndexColumnRecord(DriverException $exception): ?ProcessMessage
    {
        $message = new ProcessMessage();

        if (
            preg_match('#duplicate key name \'([\w-]+)\'#i', $exception->getMessage(), $matches) !== false
            && isset($matches[1])
        ) {
            return $message
                ->setType(ProcessMessage::TYPE_INFO)
                ->setMessage(sprintf('Duplicate index "%s"', $matches[1]))
            ;
        }
        if (
            preg_match('#duplicate column name \'([\w-]+)\'#i', $exception->getMessage(), $matches) !== false
            && isset($matches[1])
        ) {
            return $message
                ->setType(ProcessMessage::TYPE_INFO)
                ->setMessage(sprintf('Duplicate column "%s"', $matches[1]))
            ;
        }
        if (
            preg_match('#duplicate entry \'([\w-]+)\'#i', $exception->getMessage(), $matches) !== false
            && isset($matches[1])
        ) {
            return null;
        }

        return $this->processBaseException($exception);
    }

    private function processBaseException(Exception $exception): ProcessMessage
    {
        $message = new ProcessMessage();

        return $message
            ->setType(ProcessMessage::TYPE_ERROR)
            ->setMessage($exception->getMessage())
        ;
    }
}
