<?php

namespace ArrayIterator\Database;

use Exception;
use PDO;

// end here cause I don't want throw error
if (!defined('ROOT_DIR')) {
    return;
}

/**
 * Interface AdapterConnectionInterface
 * @package ArrayIterator\Database
 */
interface AdapterConnectionInterface extends QueryPrepareInterface
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

    /**
     * @return bool
     */
    public function connect(): bool;

    /**
     * @return string
     */
    public function getDriver(): string;

    /**
     * @return bool
     */
    public function ping(): bool;

    /**
     * @param string $str
     * @return string|false
     */
    public function escape(string $str);

    /**
     * @return Exception|null
     * @noinspection PhpMissingReturnTypeInspection
     */
    public function getConnectError();

    /**
     * @return PDO|null
     */
    public function getConnection(): PDO;
}
