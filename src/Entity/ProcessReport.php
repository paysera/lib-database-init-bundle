<?php

namespace Paysera\Bundle\DatabaseInitBundle\Entity;

class ProcessReport
{
    /**
     * @var ProcessMessage[]
     */
    private array $messages;

    private string $name;

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
        $this->messages = [];

        foreach ($messages as $message) {
            $this->addMessage($message);
        }

        return $this;
    }

    public function addMessage(ProcessMessage $message): void
    {
        $this->messages[] = $message;
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
