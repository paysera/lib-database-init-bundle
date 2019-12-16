<?php

namespace Paysera\Bundle\DatabaseInitBundle\Entity;

class ProcessReport
{
    /**
     * @var ProcessMessage[]
     */
    private $messages;

    /**
     * @var string
     */
    private $name;

    public function __construct()
    {
        $this->messages = [];
    }

    /**
     * @return ProcessMessage[]
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * @param ProcessMessage[] $messages
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
}
