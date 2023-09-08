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
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * @param InitializationMessage[] $messages
     */
    public function setMessages(array $messages): self
    {
        $this->messages = $messages;
        return $this;
    }

    public function getInitializer(): string
    {
        return $this->initializer;
    }

    public function setInitializer(string $initializer): self
    {
        $this->initializer = $initializer;
        return $this;
    }
}
