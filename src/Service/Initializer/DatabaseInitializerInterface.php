<?php

namespace Paysera\Bundle\DatabaseInitBundle\Service\Initializer;

use Paysera\Bundle\DatabaseInitBundle\Entity\InitializationReport;

interface DatabaseInitializerInterface
{
    /**
     * @param string|null $setName
     * @return InitializationReport|null
     */
    public function initialize($setName);

    /**
     * @return string
     */
    public function getName();
}
