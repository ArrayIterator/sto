<?php

namespace ArrayIterator\Database\Adapter;

use ArrayIterator\Database\AbstractResult;
use ArrayIterator\Database\PdoResult;
use ArrayIterator\Database\PrepareStatement;
use ArrayIterator\Exception\DatabaseConnectionException;
use Exception;
use PDO as CorePdo;
use PDOException;
use PDOStatement;

// end here cause I don't want throw error
if (!defined('ROOT_DIR')) {
    return;
}

/**
 * Class PDO
 * @package ArrayIterator\Database\Adapter
 * @mixin CorePdo
 */
class PDO extends AbstractAdapter
{
    // make sure use utf8 & utc timezone
    const INIT_COMMAND = "SET NAMES utf8, TIME_ZONE = '+00:00';";

    /**
     * @return bool
     * @throws Exception
     */
    public function connect(): bool
    {
        if ($this->connection) {
            $this->connect_error = null;
            return true;
        }

        if ($this->connect_error) {
            return false;
        }

        try {
            $this->beforeConnect();
        } catch (Exception $e) {
            $this->connect_error = $e;
            throw $e;
        }

        try {
            $this->connection = new CorePdo(
                sprintf('mysql:host=%s;dbname=%s;', $this->host, $this->dbname),
                $this->username,
                $this->password,
                [
                    CorePdo::ATTR_DEFAULT_FETCH_MODE => CorePdo::FETCH_CLASS,
                    CorePDO::ATTR_ERRMODE => CorePDO::ERRMODE_EXCEPTION,
                    CorePDO::ATTR_EMULATE_PREPARES => true,
                    CorePDO::MYSQL_ATTR_INIT_COMMAND => self::INIT_COMMAND,
                    CorePdo::ATTR_CURSOR => CorePdo::CURSOR_SCROLL,
                    CorePdo::ATTR_STATEMENT_CLASS => [PrepareStatement::class, ['stdClass']],
                    CorePdO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
                ]
            );
        } catch (PDOException $e) {
            $this->connect_error = $e;
            throw new DatabaseConnectionException(
                $e->getMessage(),
                E_ERROR,
                $e,
                [
                    'code' => $e->errorInfo[1] ?? 0,
                    'state' => $e->errorInfo[0] ?? null,
                    'message' => $e->errorInfo[2] ?? '',
                ]
            );
        }

        return true;
    }

    public function getDriver(): string
    {
        return 'pdo_mysql';
    }

    /**
     * @return bool
     */
    public function ping(): bool
    {
        try {
            if (!$this->connection) {
                return false;
            }
            $stmt = $this->connection->query('SELECT 1');
            if ($stmt) {
                $stmt->closeCursor();
            }
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * @param string $sql
     * @return false|AbstractResult
     */
    public function query(string $sql)
    {
        $this->rollbackBuffer();
        $connection = $this->getConnection();
        if (!$connection) {
            return false;
        }

        $this->setAttribute(CorePdo::ATTR_CURSOR, CorePdo::CURSOR_SCROLL);
        $sql = $connection->query($sql);
        if ($sql instanceof PDOStatement) {
            $sql = new PdoResult($sql);
        }

        return $sql;
    }

    /**
     * @param string $sql
     * @return AbstractResult|PdoResult|false
     */
    public function unbufferedQuery(string $sql)
    {
        $connection = $this->getConnection();
        if ($connection->getAttribute(CorePdo::MYSQL_ATTR_USE_BUFFERED_QUERY)) {
            $connection->setAttribute(CorePdo::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
        }

        return $this->query($sql);
    }

    public function escape(string $str)
    {
        $connection = $this->getConnection();
        if (!$connection) {
            return false;
        }
        return $connection->quote($str);
    }

    /**
     * @param string $sql
     * @return false|PrepareStatement
     */
    public function prepare(string $sql)
    {
        $this->rollbackBuffer();
        $connection = $this->getConnection();
        if (!$connection) {
            return false;
        }

        return $connection->prepare($sql, [
            CorePdo::ATTR_CURSOR => CorePdo::CURSOR_SCROLL,
            CorePdo::MYSQL_ATTR_USE_BUFFERED_QUERY => true
        ]);
    }

    public function rollbackBuffer()
    {
        if ($this->connection
            && !$this->connection->getAttribute(CorePdo::MYSQL_ATTR_USE_BUFFERED_QUERY)
        ) {
            $this->connection->setAttribute(CorePdo::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
        }
    }

    /**
     * @param string $sql
     * @return bool|PDOStatement
     */
    public function unbufferedPrepare(string $sql)
    {
        $connection = $this->getConnection();
        return $connection->prepare(
            $sql,
            [
                CorePdo::MYSQL_ATTR_USE_BUFFERED_QUERY => false,
                CorePdo::ATTR_CURSOR => CorePdo::CURSOR_SCROLL,
            ]
        );
    }
}
