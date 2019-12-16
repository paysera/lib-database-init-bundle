<?php

namespace Paysera\Bundle\DatabaseInitBundle\Entity;

class ProcessMessage
{
    const TYPE_ERROR = 'error';
    const TYPE_INFO = 'info';
    const TYPE_SUCCESS = 'success';

    /**
     * @var string
     */
    private $message;

    /**
     * @var string
     */
    private $type;

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     *
     * @return $this
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }
}
