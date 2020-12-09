<?php

namespace ArrayIterator\Database;

use PDO;
use PDOStatement;

/**
 * Class PdoResult
 * @package ArrayIterator\Database
 * @mixin PrepareStatement
 */
class PdoResult extends AbstractResult
{
    protected $statement;
    protected $ctorArgs = [];

    public function __construct(PDOStatement $statement)
    {
        $this->statement = $statement;
    }

    /**
     * @return PDOStatement
     */
    public function getStatement(): PDOStatement
    {
        return $this->statement;
    }

    public function free()
    {
        $this->closeCursor();
    }

    public function seek($offset)
    {
        return $this->statement->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_ABS, $offset);
    }

    /**
     * @param int|null $resultType
     * @return array
     */
    public function fetchAll(int $resultType = PDO::FETCH_ASSOC) : array
    {
        return $this->statement->fetchAll($resultType);
    }

    public function fetch(int $resultType = null)
    {
        return $this->statement->fetch($resultType);
    }

    /**
     * @param int $resultType
     * @return mixed
     */
    public function fetchArray(int $resultType = PDO::FETCH_BOTH)
    {
        return $this->statement->fetch($resultType);
    }

    public function fetchAssoc()
    {
        return $this->statement->fetch(PDO::FETCH_ASSOC);
    }

    public function fetchObject($class_name = 'stdClass', array $params = null)
    {
        $params = $params === null ? [] : $params;
        return $this->statement->fetchObject($class_name, $params);
    }

    public function fetchRow()
    {
        return $this->statement->fetch(PDO::FETCH_NUM);
    }

    public function freeResult()
    {
        $this->closeCursor();
    }

    public function closeCursor()
    {
        $this->statement->closeCursor();
    }

    public function close()
    {
        $this->closeCursor();
    }

    public function __destruct()
    {
        $this->closeCursor();
    }

    /**
     * @param $name
     * @param array $args
     * @return mixed
     */
    public function __call($name, array $args)
    {
        return call_user_func_array([$this->statement, $name], $args);
    }
}
