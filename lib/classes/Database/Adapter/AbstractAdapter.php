<?php
namespace ArrayIterator\Database\Adapter;

use ArrayIterator\Database\AbstractResult;
use ArrayIterator\Database\AdapterConnectionInterface;
use ArrayIterator\Database\PdoResult;
use ArrayIterator\Database\PrepareStatement;
use ArrayIterator\Exception\DatabaseConnectionException;
use Exception;
use PDOException;
use PDOStatement;

/**
 * Class AbstractAdapter
 * @package ArrayIterator\Database\Adapter
 */
abstract class AbstractAdapter implements AdapterConnectionInterface
{
    protected $host = self::DEFAULT_HOST;
    protected $username;
    protected $password;
    protected $port = self::DEFAULT_PORT;
    protected $dbname;
    /**
     * @var \PDOException|\Exception
     */
    protected $connect_error;

    /**
     * @var \PDO
     */
    protected $connection = null;

    /**
     * AbstractAdapter constructor.
     * @param string $host
     * @param null $username
     * @param null $passwd
     * @param null $dbname
     * @param int $port
     */
    public function __construct(
        $host = self::DEFAULT_HOST,
        $username = null,
        $passwd = null,
        $dbname = null,
        $port = self::DEFAULT_PORT
    ) {
        $this->host = $host;
        if (!$this->host) {
            $this->host = self::DEFAULT_HOST;
        }
        $this->username = $username;
        $this->password = $passwd;
        if (!$this->password) {
            $this->password = '';
        }
        $this->dbname = $dbname;
        $this->port = $port;
        if (!$this->port) {
            $this->port = self::DEFAULT_PORT;
        }
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @return null
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return int
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * @return string|null
     */
    public function getDbName()
    {
        return $this->dbname;
    }

    /**
     * @return Exception|PDOException|null
     */
    public function getConnectError()
    {
        return $this->connect_error;
    }

    /**
     * Validate Connection Params
     */
    protected function beforeConnect()
    {
        if (!$this->host) {
            $this->host = self::DEFAULT_HOST;
        }

        if (!is_string($this->host)) {
            throw new DatabaseConnectionException(
                'Database host could not be empty'
            );
        }

        if (!is_string($this->username) || trim($this->username) === '') {
            throw new DatabaseConnectionException(
                'Database user could not be empty'
            );
        }

        if (!is_string($this->password)) {
            throw new DatabaseConnectionException(
                'Database password must be as a string'
            );
        }
        if (!is_string($this->dbname) || trim($this->dbname) === '') {
            throw new DatabaseConnectionException(
                'Database user could not be empty'
            );
        }
        if (!is_int($this->port)) {
            throw new DatabaseConnectionException(
                'Database port is invalid'
            );
        }
    }

    /**
     * @param string $sql
     * @return false|PrepareStatement
     */
    public function prepare(string $sql)
    {
        return $this->getConnection()->prepare($sql);
    }

    /**
     * @param string $sql
     * @return AbstractResult|PdoResult|false|PDOStatement
     */
    public function query(string $sql)
    {
        return $this->getConnection()->query($sql);
    }

    public function hasConnection()
    {
        return (bool) $this->connection;
    }

    /**
     * @return \PDO|null
     */
    public function getConnection() : \PDO
    {
        if (!$this->connection) {
            $this->connect();
        }

        return $this->connection;
    }

    public function __call($name, array $arguments)
    {
        return call_user_func_array([$this->getConnection(), $name], $arguments);
    }
}
