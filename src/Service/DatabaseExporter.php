<?php
declare(strict_types=1);

namespace Paysera\Bundle\DatabaseInitBundle\Service;

use Paysera\Bundle\DatabaseInitBundle\Entity\ProcessMessage;
use Paysera\Bundle\DatabaseInitBundle\Entity\ProcessReport;
use Paysera\Bundle\DatabaseInitBundle\Service\Exporter\DatabaseExporterInterface;

class DatabaseExporter
{
    /**
     * @var DatabaseExporterInterface[]
     */
    private $exporters;
    
    public function __construct()
    {
        $this->exporters = [];
    }
    
    public function addExporter(DatabaseExporterInterface $exporter, string $name)
    {
        $this->exporters[$name] = $exporter;
    }
    
    public function export(string $name = null): ProcessReport
    {
        $messages = [];
        foreach ($this->exporters as $exporterName => $exporter) {
            if ($name === null || $exporterName === $name) {
                $exporter->export($exporterName);
                
                $message = new ProcessMessage();
                $messages[] = $message
                    ->setType(ProcessMessage::TYPE_SUCCESS)
                    ->setMessage($exporterName)
                ;
            }
        }
        
        $report = new ProcessReport();
        return $report->setMessages($messages);
    }
}
