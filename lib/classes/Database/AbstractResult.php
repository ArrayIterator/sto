<?php

namespace ArrayIterator\Database;

use PDO;

/**
 * Class AbstractResult
 * @package ArrayIterator\Database
 */
abstract class AbstractResult
{
    abstract public function close();

    abstract public function free();

    abstract public function seek($offset);

    abstract public function fetchAll(int $resultType = PDO::FETCH_ASSOC);

    abstract public function fetchArray(int $resultType = PDO::FETCH_BOTH);

    abstract public function fetchAssoc();

    abstract public function fetchObject($class_name = 'stdClass', array $params = null);

    abstract public function fetchRow();

    abstract public function freeResult();
}