<?php

namespace Paysera\Bundle\DatabaseInitBundle\Service\Initializer;

use Paysera\Bundle\DatabaseInitBundle\Entity\InitializationReport;

interface DatabaseInitializerInterface
{
    /**
     * @return InitializationReport|null
     */
    public function initialize();

    /**
     * @return string
     */
    public function getName();
}
