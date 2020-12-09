<?php

namespace ArrayIterator\Exception;

use Exception;
use Throwable;

/**
 * Class MethodNotAllowedException
 * @package ArrayIterator\Exception
 */
class MethodNotAllowedException extends Exception
{
    protected $methods;

    public function __construct(array $allowedMethods = [], $message = "", $code = 0, Throwable $previous = null)
    {
        $this->methods = $allowedMethods;
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return array
     */
    public function getMethods(): array
    {
        return $this->methods;
    }
}
