<?php

namespace Paysera\Bundle\DatabaseInitBundle\Entity;

class InitializationReport
{
    /**
     * @var InitializationMessage[]
     */
    private array $messages;

    /**
     * @var string
     */
    private string $initializer;

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
        $this->messages = [];

        foreach ($messages as $message) {
            $this->addMessage($message);
        }

        return $this;
    }

    public function addMessage(InitializationMessage $message): void
    {
        $this->messages[] = $message;
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
