<?php

namespace ArrayIterator\Exception;

use RuntimeException;
use Throwable;

/**
 * Class DatabaseConnectionException
 * @package ArrayIterator\Exception
 */
class DatabaseConnectionException extends RuntimeException
{
    public $error_list = [];

    public function __construct($message = "", $code = 0, Throwable $previous = null, array $errorList = [])
    {
        parent::__construct($message, $code, $previous);
        $this->error_list = $errorList;
    }

    /**
     * @return array
     */
    public function getErrorList() : array
    {
        return $this->error_list;
    }
}
