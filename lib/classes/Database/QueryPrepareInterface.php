<?php

namespace ArrayIterator\Database;

/**
 * Interface QueryPrepareInterface
 * @package ArrayIterator\Database
 */
interface QueryPrepareInterface
{
    /**
     * @param string $sql
     * @return AbstractResult|PdoResult|false
     */
    public function query(string $sql);

    /**
     * @param string $sql
     * @return AbstractResult|PdoResult|false
     */
    public function unbufferedQuery(string $sql);

    /**
     * @param string $sql
     * @return PrepareStatement|false
     */
    public function prepare(string $sql);

    /**
     * @param string $sql
     * @return PrepareStatement|false
     */
    public function unbufferedPrepare(string $sql);

    public function rollbackBuffer();
}