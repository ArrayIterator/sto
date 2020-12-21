<?php

use ArrayIterator\Database\AbstractResult;
use ArrayIterator\Database\AdapterConnectionInterface;
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
 * @return AbstractResult|false
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
 * @return AbstractResult|false
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
 * @return PrepareStatement|false
 */
function database_unbuffered_prepare(string $query)
{
    return database()->unbufferedPrepare($query);
}

/**
 * @param string $query
 * @return false|int
 */
function database_execute(string $query)
{
    return database()->exec($query);
}
