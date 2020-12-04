<?php

namespace ArrayIterator;

use ArrayIterator\Database\Adapter\AbstractAdapter;
use ArrayIterator\Database\Adapter\PDO;
use ArrayIterator\Database\ConnectionInterface;
use ArrayIterator\Database\Tables;
use DateTimeZone;

/**
 * Class Database
 * @package ArrayIterator
 * @mixin AbstractAdapter|PDO
 */
class Database
{
    /***
     * @var bool|null
     */
    protected static $is_pdo_supported = null;

    /**
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * @var Tables
     */
    protected static $tablesRecord = [];

    /**
     * Database constructor.
     * @param string $host
     * @param string $user
     * @param string $password
     * @param string $database_name
     * @param int $port
     */
    public function __construct(
        $host = null,
        $user = null,
        $password = null,
        $database_name = null,
        $port = null
    ) {
        if (self::$is_pdo_supported === null
        ) {
            self::$is_pdo_supported = class_exists('PDO')
                && in_array('mysql', \PDO::getAvailableDrivers());
        }
        if (!self::$is_pdo_supported) {
            throw new \RuntimeException(
                'Driver pdo_mysql not supported'
            );
        }
        if (is_numeric($port)) {
            $port = abs(intval($port));
        }

        $this->connection = new PDO(
            $host,
            $user,
            $password,
            $database_name,
            $port
        );
    }

    /**
     * @return Tables|null
     */
    public function getTables()
    {
        $dbName = $this->connection->getDbname();
        if (!$dbName) {
            throw new \RuntimeException(
                'Database configuration does not have a database name'
            );
        }

        if (isset(self::$tablesRecord[$dbName])) {
            return self::$tablesRecord[$dbName];
        }
        return self::$tablesRecord[$dbName] = new Tables($this);
    }

    /**
     * @return ConnectionInterface
     */
    public function getConnection() : ConnectionInterface
    {
        return $this->connection;
    }

    /**
     * @return bool
     */
    public function isConnected() : bool
    {
        try {
            return $this->connection->connect();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @return DateTimeZone
     */
    public function getTimezone() : DateTimeZone
    {
        $res = $this->query("
            SELECT (case WHEN
	        UPPER(@@session.time_zone) = 'SYSTEM' THEN @@system_time_zone
                ELSE @@session.time_zone
            END) as tz
        ");
        $tz = $res->fetchAssoc()['tz']??'+00:00';
        $res->closeCursor();
        return new DateTimeZone($tz);
    }

    /**
     * @param string $name
     * @param array $args
     * @return mixed
     */
    public function __call(string $name, array $args)
    {
        return call_user_func_array([$this->connection, $name], $args);
    }
}
