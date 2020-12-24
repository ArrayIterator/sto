<?php

use ArrayIterator\Database\AdapterConnectionInterface;
use ArrayIterator\Database\PdoResult;
use ArrayIterator\Database\PrepareStatement;

/**
 * @return AdapterConnectionInterface
 */
function database_get_adapter(): AdapterConnectionInterface
{
    return database()->getAdapter();
}

/**
 * @return PDO|null
 */
function database_pdo_connection(): PDO
{
    return database_get_adapter()->getConnection();
}

/**
 * @param string $query
 * @return PdoResult|false
 */
function database_query(string $query)
{
    return database()->query($query);
}

/**
 * @param string $quote
 * @return string
 */
function database_quote(string $quote) : string
{
    return database()->quote($quote);
}

/**
 * @param string $query
 * @return PdoResult|false
 */
function database_unbuffered_query(string $query)
{
    return database()->unbufferedQuery($query);
}

/**
 * @param string $query
 * @return PrepareStatement|false
 */
function database_prepare(string $query)
{
    return database()->prepare($query);
}

/**
 * @param string $query
 * @param array|null $params
 * @return PrepareStatement|false
 */
function database_prepare_execute(string $query, array $params = null)
{
    $stmt = database()->prepare($query);
    if ($stmt) {
        if (!$stmt->execute($params)) {
            $stmt->closeCursor();
            return false;
        }
    }

    return $stmt;
}


/**
 * @param string $query
 * @return PrepareStatement|false
 */
function database_unbuffered_prepare(string $query)
{
    return database()->unbufferedPrepare($query);
}

/**
 * @param string $query
 * @param array|null $params
 * @return bool|PDOStatement
 */
function database_unbuffered_prepare_execute(string $query, array $params = null)
{
    $stmt = database()->unbufferedPrepare($query);
    if ($stmt) {
        if (!$stmt->execute($params)) {
            $stmt->closeCursor();
            return false;
        }
    }

    return $stmt;
}

/**
 * @param string $query
 * @return false|int
 */
function database_execute(string $query)
{
    return database()->exec($query);
}

/**
 * @param string $query
 * @param array|null $params
 * @return PdoResult|false
 */
function database_query_execute(string $query, array $params = null)
{
    $q = database()->query($query);
    if ($q) {
        if (!$q->execute($params)) {
            $q->closeCursor();
        }
    }
    return $q;
}

/**
 * @param string $query
 * @param array|null $params
 * @return PdoResult|false
 */
function database_unbuffered_query_execute(string $query, array $params = null)
{
    $q = database()->unbufferedQuery($query);
    if ($q) {
        if (!$q->execute($params)) {
            $q->closeCursor();
        }
    }

    return $q;
}

