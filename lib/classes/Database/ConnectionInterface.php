<?php

namespace ArrayIterator\Database;

use ArrayIterator\Database\Adapter\AbstractAdapter;
use Exception;

/**
 * Interface ConnectionInterface
 * @package ArrayIterator\Database
 */
interface ConnectionInterface extends QueryPrepareInterface
{
    const DEFAULT_PORT = 3306;
    const DEFAULT_HOST = 'localhost';

    /**
     * ConnectionInterface constructor.
     * @param string $host
     * @param string $username
     * @param string $passwd
     * @param string $dbname
     * @param int $port
     */
    public function __construct(
        $host = self::DEFAULT_HOST,
        $username = null,
        $passwd = null,
        $dbname = null,
        $port = self::DEFAULT_PORT
    );

    public function connect();

    /**
     * @return string
     */
    public function getDriver();

    /**
     * @return bool
     */
    public function ping();

    /**
     * @param string $str
     * @return string
     */
    public function escape($str);

    /**
     * @return Exception|null
     */
    public function getConnectError();

    /**
     * @return AbstractAdapter|null
     */
    public function getConnection();
}
