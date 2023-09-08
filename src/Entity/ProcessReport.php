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
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * @param ProcessMessage[] $messages
     */
    public function setMessages(array $messages): self
    {
        $this->messages = $messages;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }
}
