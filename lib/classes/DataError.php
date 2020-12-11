<?php

namespace ArrayIterator;

/**
 * Class DataError
 * @package ArrayIterator
 */
class DataError
{
    /**
     * @var array|string[]
     */
    protected $messages = [];
    /**
     * @var string
     */
    protected $type;
    /**
     * @var mixed
     */
    protected $code;

    /**
     * DataError constructor.
     * @param string $type
     * @param $message
     * @param $code
     */
    public function __construct(string $type = '', $message = '', $code = null)
    {
        $this->type = $type;
        $this->messages = (array)$message;
        $this->code = $code;
    }

    /**
     * @return array
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * @return string|false|mixed
     */
    public function getMessage()
    {
        return reset($this->messages);
    }

    public function getCode()
    {
        return $this->code;
    }
}