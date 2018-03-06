<?php

namespace Paysera\Bundle\DatabaseInitBundle\Entity;

class InitializationReport
{
    /**
     * @var InitializationMessage[]
     */
    private $messages;

    /**
     * @var string
     */
    private $initializer;

    public function __construct()
    {
        $this->messages = [];
    }

    /**
     * @return InitializationMessage[]
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * @param InitializationMessage[] $messages
     *
     * @return $this
     */
    public function setMessages($messages)
    {
        $this->messages = $messages;
        return $this;
    }

    /**
     * @return string
     */
    public function getInitializer()
    {
        return $this->initializer;
    }

    /**
     * @param string $initializer
     *
     * @return $this
     */
    public function setInitializer($initializer)
    {
        $this->initializer = $initializer;
        return $this;
    }
}
