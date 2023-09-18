<?php

namespace Paysera\Bundle\DatabaseInitBundle\Entity;

class InitializationMessage
{
    const TYPE_ERROR = 'error';
    const TYPE_INFO = 'info';
    const TYPE_SUCCESS = 'success';

    private string $message;

    private string $type;

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }
}
