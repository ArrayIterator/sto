<?php

namespace ArrayIterator\Database;

use ArrayIterator\Model\Model;
use PDO;
use PDOStatement;

/**
 * Class PrepareStatement
 * @package ArrayIterator\Database
 */
class PrepareStatement extends PDOStatement
{
    public $queryString;
    protected $fetchClass;
    protected $ctorArgs = [];
    protected $closed = false;

    protected function __construct($fetch_class = 'stdClass')
    {
        $this->fetchClass = $fetch_class;
    }

    /**
     * @param $className
     * @param array $ctorargs
     */
    public function setFetchClass($className, array $ctorargs = [])
    {
        $this->fetchClass = $className;
        $this->ctorArgs = $ctorargs;
    }

    /**
     * @param null|int $fetch_style
     * @param int $cursor_orientation
     * @param int $cursor_offset
     * @return Model|array|object|mixed
     */
    public function fetch($fetch_style = null, $cursor_orientation = PDO::FETCH_ORI_NEXT, $cursor_offset = 0)
    {
        if ($fetch_style === PDO::FETCH_CLASS || $fetch_style === null) {
            $this->setFetchMode(PDO::FETCH_CLASS, $this->fetchClass, $this->ctorArgs);
        }

        return parent::fetch($fetch_style, $cursor_orientation, $cursor_offset);
    }

    /**
     * @param null|int $fetch_style
     * @param int $cursor_orientation
     * @param int $cursor_offset
     * @return mixed
     */
    public function fetchClose($fetch_style = null, $cursor_orientation = PDO::FETCH_ORI_NEXT, $cursor_offset = 0)
    {
        $data = $this->fetch($fetch_style, $cursor_orientation, $cursor_offset);
        $this->closeCursor();
        return $data;
    }

    public function close()
    {
        $this->closeCursor();
    }

    public function closeCursor()
    {
        $this->closed = true;
        return parent::closeCursor();
    }

    /**
     * @param $offset
     * @return Model|array|object|mixed
     */
    public function data($offset)
    {
        return $this->fetch(null, \PDO::FETCH_ORI_ABS, $offset);
    }

    /**
     * @return Model|array|object|mixed
     */
    public function first()
    {
        return $this->fetch(null, \PDO::FETCH_ORI_FIRST);
    }

    /**
     * @return array|Model|mixed|object
     */
    public function last()
    {
        return $this->fetch(null, \PDO::FETCH_ORI_ABS);
    }

    public function next()
    {
        return $this->fetch();
    }

    public function numRows(): int
    {
        return $this->rowCount();
    }

    public function freeResult()
    {
        $this->closeCursor();
    }

    public function reset()
    {
        $this->execute();
    }

    public function getResult()
    {
        return $this->fetch();
    }

    public function fetchAssoc()
    {
        return $this->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * @param array|mixed|null $input_parameters
     * @return bool
     */
    public function execute($input_parameters = null)
    {
        if (func_num_args() > 0 && !is_array($input_parameters)) {
            $input_parameters = (array)$input_parameters;
        }
        return parent::execute($input_parameters);
    }

    public function __destruct()
    {
        !$this->closed && $this->closeCursor();
    }
}
