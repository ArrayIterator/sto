<?php

namespace ArrayIterator\Model;

use ArrayAccess;
use ArrayIterator\Application;
use ArrayIterator\Database;
use ArrayIterator\Database\AbstractResult;
use ArrayIterator\Database\PdoResult;
use ArrayIterator\Database\PrepareStatement;
use ArrayIterator\Database\QueryPrepareInterface;
use BadMethodCallException;
use Exception;
use RuntimeException;

/**
 * Class Model
 * @package ArrayIterator\Model
 */
abstract class Model implements QueryPrepareInterface, ArrayAccess
{
    /**
     * @var int
     */
    protected $loop = 0;
    /**
     * @var int
     */
    protected $siteId = null;
    protected $normalized = false;
    protected $prefixDefault = 'sto_';
    private static $tablesModelRecord = [];
    private static $tablesModelRecordPrimary = [];
    protected $fromStatement = false;
    protected $tableName;
    protected $primaryKey;
    protected $data = [];
    protected $userData = [];
    protected $availableColumns;

    /**
     * @var Database
     */
    protected $database;

    /**
     * Model constructor.
     * @param Database $database
     * @param int $siteId
     */
    public function __construct(Database $database, int $siteId = 1)
    {
        $this->database = $database;
        if ($this->siteId === null) {
            $this->setModelSiteId($siteId);
        }

        $this->checkMetaData();
    }

    /**
     * @return string|null
     * @noinspection PhpMissingReturnTypeInspection
     */
    public function getAutoIncrementColumn()
    {
        return $this
            ->database
            ->getTables()
            ->getTableDefinition($this->getTableName())
            ->getAutoIncrement();
    }

    /**
     * @return int|null
     * @noinspection PhpMissingReturnTypeInspection
     */
    public function getSiteId()
    {
        $id = $this->data['site_id'] ?? null;
        return is_numeric($id) ? abs(intval($id)) : null;
    }

    /**
     * @return int|null
     * @noinspection PhpMissingReturnTypeInspection
     */
    public function getId()
    {
        $id = $this->data['id'] ?? null;
        return is_numeric($id) ? abs(intval($id)) : null;
    }

    /**
     * @return array
     */
    protected function getStatementArguments(): array
    {
        return [$this->database, $this->siteId];
    }

    /**
     * @return int|mixed
     * @noinspection PhpMissingReturnTypeInspection
     */
    public function getModelSiteId()
    {
        return $this->siteId;
    }

    public function setModelSiteId($siteId)
    {
        $siteId = is_int($siteId) || is_numeric($siteId) ? (int)$siteId : $siteId;
        $this->siteId = $siteId;
    }


    /**
     * @param array $data
     * @param Database|null $database
     * @return Model
     */
    public static function create(array $data, Database $database = null): Model
    {
        $database = $database ?: Application::getInstance()->getDatabase();
        $model = new static($database);
        $columns = $model->getAvailableColumns();
        foreach ($data as $key => $item) {
            if (in_array($key, $columns)) {
                $model[$key] = $item;
            }
        }
        return $model;
    }

    /**
     * @param string $sql
     * @return AbstractResult|PdoResult|false
     */
    public function query(string $sql)
    {
        $pdoRes = $this->database->query($sql);
        $st = $pdoRes->getStatement();
        if ($st instanceof PrepareStatement) {
            $st->setFetchClass(
                static::class,
                $this->getStatementArguments()
            );
        }
        return $pdoRes;
    }

    public function unbufferedQuery(string $sql)
    {
        $pdoRes = $this->database->unbufferedQuery($sql);
        $st = $pdoRes->getStatement();
        if ($st instanceof PrepareStatement) {
            $st->setFetchClass(
                static::class,
                $this->getStatementArguments()
            );
        }
        return $pdoRes;
    }

    public function rollbackBuffer()
    {
        $this->database->rollbackBuffer();
    }

    /**
     * @param string $sql
     * @return PrepareStatement|false
     */
    public function prepare(string $sql)
    {
        $stmt = $this->database->prepare($sql);
        $stmt instanceof PrepareStatement && $stmt->setFetchClass(
            static::class,
            $this->getStatementArguments()
        );
        return $stmt;
    }

    /**
     * @param string $sql
     * @return bool|PrepareStatement
     */
    public function unbufferedPrepare(string $sql)
    {
        $stmt = $this->database->unbufferedPrepare($sql);
        $stmt instanceof PrepareStatement && $stmt->setFetchClass(
            static::class,
            $this->getStatementArguments()
        );
        return $stmt;
    }

    protected function checkMetaData()
    {
        if ($this->tableName && $this->primaryKey) {
            return;
        }

        $classNameOri = get_class($this);
        if (!$this->tableName) {
            $tableList = $this->database->getTables()->getListTable();
            if (isset(self::$tablesModelRecord[$classNameOri])) {
                $this->tableName = self::$tablesModelRecord[$classNameOri] ?: null;
            } else {
                self::$tablesModelRecord[$classNameOri] = false;
                $className = preg_replace('#^(?:.+[\\\])?(?:Model)?([^\\\]+)$#i', '$1', $classNameOri);
                $className = strtolower($className);
                if (in_array($className, $tableList)) {
                    $this->tableName = $className;
                } elseif (in_array(sprintf('%s%s', $this->prefixDefault, $className), $tableList)) {
                    $this->tableName = sprintf('%s%s', $this->prefixDefault, $className);
                } else {
                    $lowerClassName = strtolower($className);
                    foreach ($tableList as $item) {
                        if (strtolower($item) === $lowerClassName) {
                            $this->tableName = $item;
                            break;
                        }
                    }

                    if (!$this->tableName) {
                        $lowerClassName = str_replace('_', '', $lowerClassName);
                        foreach ($tableList as $item) {
                            if (str_replace('_', '', strtolower($item)) === $lowerClassName) {
                                $this->tableName = $item;
                                break;
                            }
                        }
                    }

                    if (!$this->tableName) {
                        $lowerClassName = sprintf('%s%s', $this->prefixDefault, $className);
                        foreach ($tableList as $item) {
                            if (strtolower($item) === $lowerClassName) {
                                $this->tableName = $item;
                                break;
                            }
                        }
                    }

                    if (!$this->tableName) {
                        $lowerClassName = str_replace('_', '', $className);
                        $lowerClassName = sprintf('%s%s', $this->prefixDefault, $lowerClassName);
                        foreach ($tableList as $item) {
                            if (str_replace('_', '', strtolower($item)) === $lowerClassName) {
                                $this->tableName = $item;
                                break;
                            }
                        }
                    }
                }

                if ($this->tableName) {
                    self::$tablesModelRecord[$className] = $this->tableName;
                } else {
                    throw new RuntimeException(
                        sprintf('Model Object %s has no declared table name', $classNameOri)
                    );
                }
            }
            self::$tablesModelRecord[$classNameOri] = $this->tableName;
        }
        if (!$this->primaryKey) {
            if (isset(self::$tablesModelRecordPrimary[$classNameOri])) {
                $this->primaryKey = self::$tablesModelRecordPrimary[$classNameOri];
                $this->primaryKey = $this->primaryKey ?: null;
            } else {
                $table = $this->database->getTables()->getTableDefinition($this->tableName);
                $this->primaryKey = $table->getUniqueSelector();
                self::$tablesModelRecordPrimary[$classNameOri] = $this->primaryKey;
            }
        }
    }

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * @return string|array
     */
    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function getUserData(): array
    {
        return $this->userData;
    }

    /**
     * @return array
     */
    public function getAvailableColumns(): array
    {
        if ($this->availableColumns === null) {
            $this->availableColumns = $this->database
                    ->getTables()
                    ->getTableDefinition($this->getTableName()) ?? null;
            $this->availableColumns = $this->availableColumns
                ? $this->availableColumns->getListColumn()
                : [];
        }
        return $this->availableColumns;
    }

    /**
     *
     */
    public function hasColumnSiteId(): bool
    {
        return in_array('site_id', $this->getAvailableColumns());
    }

    /**
     * @return Database
     */
    public function getDatabase(): Database
    {
        return $this->database;
    }

    protected function getPrimaryKeysSelectors(): array
    {
        return $this
            ->database
            ->getTables()
            ->getTableDefinition($this->getTableName())
            ->getUniqueSelector();
    }

    public function offsetExists($offset): bool
    {
        return $this->__isset($offset);
    }

    public function offsetGet($offset)
    {
        return $this->__get($offset);
    }

    public function offsetSet($offset, $value)
    {
        $this->__set($offset, $value);
    }

    public function offsetUnset($offset)
    {
        $this->__unset($offset);
    }

    /**
     * @return static|false
     */
    public function get()
    {
        if (!empty($this->userData)) {
            $currentSelector = null;
            $value = null;
            foreach ($this->getPrimaryKeysSelectors() as $selector) {
                if (array_key_exists($selector, $this->userData)) {
                    $currentSelector = $selector;
                    $value = $this->userData[$selector];
                    break;
                }
            }

            if ($currentSelector && $value !== null) {
                $res = $this->findOneBy($value, $currentSelector);
                if (!$res) {
                    return false;
                }

                return $res->fetchClose() ?: false;
            }
        }

        if ($this->fromStatement) {
            return $this;
        }

        return false;
    }

    /**
     * @param $value
     * @param string $selector
     * @param int|null $siteId
     * @return PrepareStatement|false
     */
    public function findOne($value, string $selector, int $siteId = null)
    {
        $tableName = $this->getTableName();
        $sql = sprintf('SELECT * FROM %s WHERE %s=?', $tableName, $selector);
        $siteId = $siteId === null ? $this->getModelSiteId() : $siteId;
        if (is_int($siteId)
            && $this->hasColumnSiteId()
            && $this->getAutoIncrementColumn() !== $selector
        ) {
            $sql .= sprintf(' AND site_id=%d', $siteId);
        }

        $stmt = $this->prepare($sql);
        if ($stmt->execute([$value])) {
            return $stmt;
        }

        return false;
    }

    /**
     * @param $value
     * @param int|null $limit
     * @param int|null $offset
     * @param string|null $selector
     * @param int|null $siteId
     * @return PrepareStatement|false
     */
    public function find(
        $value,
        int $limit = null,
        int $offset = null,
        string $selector = null,
        int $siteId = null
    ) {
        $tableName = $this->getTableName();
        if ($selector === null) {
            $primaryKeys = $this->getPrimaryKey();
            $tableDefinition = $this
                ->getDatabase()
                ->getTables()
                ->getTableDefinition($tableName);
            if (empty($primaryKeys)) {
                $primaryKeys = $tableDefinition->getPrimaryKey();
                foreach ($tableDefinition->getUnique() as $item) {
                    $primaryKeys[] = $item;
                }
            }

            if (!is_array($primaryKeys)) {
                $primaryKeys = (array)$primaryKeys;
            }

            // * "boolean"
            // * "integer"
            // * "double" (for historical reasons "double" is
            // * returned in case of a float, and not simply
            // * "float")
            // * "string"
            // * "array"
            // * "object"
            // * "resource"
            // * "NULL"
            // * "unknown type"
            if ($primaryKeys) {
                $keys = [];
                foreach ($primaryKeys as $item) {
                    $type = $tableDefinition->getColumn($item)->getType();
                    if ($type === 'TINYINT' || $type === 'BOOLEAN') {
                        continue;
                    }

                    $keys[$item] = strpos($type, 'INT') ? 'INTEGER' : (
                    strpos($type, 'FLOAT') ? 'FLOAT' : 'STRING'
                    );
                }

                $type = strtoupper(gettype($value));

                foreach ($keys as $item => $t) {
                    if ($t === $type) {
                        $selector = $item;
                        break;
                    }
                }
                if (!$selector) {
                    reset($keys);
                    $selector = key($keys);
                }
            }
        }

        if (!$selector) {
            $this->loop = 0;
            return false;
        }
        $method = sprintf('findBy%s', $selector);
        if ($this->loop < 2 && method_exists($this, $method)) {
            $this->loop++;
            return $this->$method($value, $limit, $offset, $siteId);
        }
        $method = sprintf('findBy%s', str_replace('_', '', $selector));
        if ($this->loop < 2 && method_exists($this, $method)) {
            $this->loop++;
            return $this->$method($value, $limit, $offset, $siteId);
        }

        $this->loop = 0;
        $sql = sprintf(
            'SELECT * FROM %s WHERE %s=?',
            $tableName,
            $selector
        );

        $siteId = $siteId === null ? $this->getModelSiteId() : $siteId;
        if (is_numeric($siteId) && is_int(abs($siteId)) && $this->hasColumnSiteId()
            && $this->getAutoIncrementColumn() !== $selector
        ) {
            $sql = sprintf(' AND site_id=%d', abs($siteId));
        }

        if (is_int($offset)) {
            $sql .= sprintf(
                "%s OFFSET %d",
                (
                !is_int($limit)
                    ? ' LIMIT -1' : sprintf('LIMIT %d', $limit)
                ),
                $offset
            );
        } elseif (is_int($limit)) {
            $sql .= sprintf(" LIMIT %d", $limit);
        }

        $arg = $this->prepare($sql);
        $arg->setFetchClass(static::class, [$this->getDatabase()]);
        $arg->execute([$value]);
        return $arg;
    }

    /**
     * @param $value
     * @param int|null $limit
     * @param int|null $offset
     * @param string|null $selector
     * @param int|null $siteId
     * @param Database|null $database
     * @return PrepareStatement|false
     */
    public static function findBy(
        $value,
        int $limit = null,
        int $offset = null,
        string $selector = null,
        int $siteId = null,
        Database $database = null
    ) {
        $current = new static($database ?: Application::getInstance()->getDatabase());
        return $current->find($value, $limit, $offset, $selector, $siteId);
    }

    /**
     * @param $value
     * @param string|null $selector
     * @param int|null $siteId
     * @return PrepareStatement|false
     */
    public static function findOneBy($value, string $selector = null, int $siteId = null)
    {
        return static::findBy($value, 1, null, $selector, $siteId);
    }

    /**
     * @return bool|null
     */
    public function isFromStatement(): bool
    {
        return $this->fromStatement;
    }

    public function __set($name, $value)
    {
        if (!$this->fromStatement) {
            $this->fromStatement = !($this->database) || count($this->data) === count($this->getAvailableColumns());
        }
        if ($this->fromStatement && !$this->database) {
            if ($name === 'site_id') {
                $this->setModelSiteId($value);
            }
            $this->data[$name] = $this->sanitizeValue($name, $value);
            return;
        }

        if ($this->database && $this->fromStatement && !$this->normalized) {
            $this->normalizeData();
        }

        $columns = $this->getAvailableColumns();
        if (in_array($name, $columns)
            || in_array(strtolower($name), $columns)
        ) {
            $method = "set{$name}";
            if (method_exists($this, $method)) {
                $this->$method($value);
                return;
            }
        } else {
            $lower = array_map('strtolower', $columns);
            $currentName = str_replace('_', '', strtolower($name));
            foreach ($lower as $key => $item) {
                if (str_replace('_', '', $key) === $currentName) {
                    if (method_exists($this, $currentName)) {
                        $this->$currentName($value);
                        return;
                    }
                    $this->data[$key] = $this->sanitizeValue($key, $value);
                    break;
                }
            }
        }

        if (isset($method) && method_exists($this, $method)) {
            $this->$method($value);
            return;
        }
        $this->userData[$name] = $value;
    }

    /**
     * @param mixed $name
     * @return mixed|null
     */
    public function __get($name)
    {
        if ($this->database && $this->fromStatement && !$this->normalized) {
            $this->normalizeData();
        }
        return array_key_exists($name, $this->data)
            ? $this->data[$name]
            : null;
    }

    public function __unset($name)
    {
        unset($this->userData[$name]);
    }

    public function __isset($name) : bool
    {
        return array_key_exists($name, $this->data);
    }

    public function __call(string $name, array $value)
    {
        if ($this->database && $this->fromStatement && !$this->normalized) {
            $this->normalizeData();
        }

        preg_match('~get[_]*(.+)$~i', $name, $match);
        $method = $match[1] ?? null;
        $mode = 'GET';
        if (empty($method)) {
            preg_match('~set[_]*(.+)$~i', $name, $match);
            $mode = 'SET';
            $method = $match[1] ?? null;
        }
        if (!$method) {
            throw new BadMethodCallException(
                sprintf('Call to undefined method %s:%s', get_class($this), $name)
            );
        }

        $availableColumns = $this->getAvailableColumns();
        if ($mode === 'SET') {
            $method_2 = "set" . $method;
            if (method_exists($this, $method_2)) {
                return $this->$method_2(...$value);
            }
            $method_2 = "set_" . $method;
            if (method_exists($this, $method_2)) {
                return $this->$method_2(...$value);
            }

            $method_2 = "set" . str_replace('_', '', $method);
            if (method_exists($this, $method_2)) {
                return $this->$method_2(...$value);
            }
            $method_3 = "set_" . str_replace('_', '', $method);
            if (method_exists($this, $method_3)) {
                return $this->$method_3(...$value);
            }

            if (in_array($method, $availableColumns)) {
                $this->userData[$method] = reset($value);
                return null;
            }

            $lower = strtolower($method);
            $newLower = str_replace('_', '', $lower);
            foreach ($availableColumns as $key) {
                if (strtolower($key) === $lower) {
                    $this->userData[$key] = reset($value);
                    return null;
                }
            }
            foreach ($availableColumns as $key) {
                if (str_replace('_', '', strtolower($key)) === $newLower) {
                    $this->userData[$key] = reset($value);
                    return null;
                }
            }
        } else {
            $method_2 = "get" . $method;
            if (method_exists($this, $method_2)) {
                return $this->$method_2(...$value);
            }
            $method_2 = "get_" . $method;
            if (method_exists($this, $method_2)) {
                return $this->$method_2(...$value);
            }

            $method_2 = "get" . str_replace('_', '', $method);
            if (method_exists($this, $method_2)) {
                return $this->$method_2(...$value);
            }
            $method_3 = "get_" . str_replace('_', '', $method);
            if (method_exists($this, $method_3)) {
                return $this->$method_3(...$value);
            }

            if (in_array($method, $availableColumns)) {
                if (isset($this->data[$method])) {
                    return $this->data[$method];
                }
            }

            $lower = strtolower($method);
            $newLower = str_replace('_', '', $lower);
            foreach ($this->data as $key => $item) {
                if (strtolower($key) === $lower) {
                    return $item;
                }
            }

            foreach ($this->data as $key => $item) {
                if (str_replace('_', '', strtolower($key)) === $newLower) {
                    return $item;
                }
            }
        }

        throw new BadMethodCallException(
            sprintf('Call to undefined method %s:%s', get_class($this), $name)
        );
    }

    /** @noinspection PhpUnusedParameterInspection */
    protected function normalizeColumn(string $column, $value)
    {
        return $value;
    }

    protected function normalizeData()
    {
        if ($this->normalized || !$this->database || !$this->fromStatement) {
            return;
        }

        $this->normalized = true;
        $definition = $this->database->getTables()->getTableDefinition($this->getTableName());
        if (!$definition) {
            return;
        }

        foreach ($this->data as $key => $datum) {
            if (!is_numeric($datum)
                || !($column = $definition->getColumn($key))
                || !($column = $column->getType())
            ) {
                continue;
            }

            $datum
                = $datum
                = $this->normalizeColumn($key, $datum);
            if ($key === 'site_id' && is_numeric($datum)) {
                $datum = abs(intval($datum));
                $this->data[$key] = $datum;
                continue;
            }
            if (strpos($column, 'TINYINT') !== false) {
                continue;
            }
            if (strpos($column, 'INT') !== false && is_numeric($datum)) {
                $this->data[$key] = abs(intval($datum));
                continue;
            }
            if (strpos($column, 'FLOAT') !== false && is_numeric($datum)) {
                $this->data[$key] = (float)($datum);
                continue;
            }
        }
    }

    public function toArray() : array
    {
        return $this->data;
    }

    /** @noinspection PhpUnusedParameterInspection */
    protected function sanitizeDatabaseValue($column, $value)
    {
        return $value;
    }

    protected function sanitizeDatabaseWhereValue($column, $value)
    {
        if ($column === 'id' || $column === 'site_id') {
            $value = is_numeric($value)
                ? abs($value)
                : $value;
        }

        return $value;
    }

    protected function sanitizeValue($column, $value)
    {
        if ($column === 'id' || $column === 'site_id') {
            $value = is_numeric($value)
                ? abs($value)
                : $value;
        }

        return $value;
    }

    public function save(array $where = [])
    {
        $columns = array_flip($this->getAvailableColumns());
        $primary = array_flip($this->getPrimaryKeysSelectors());
        $userColumn = array_keys($this->userData);
        if (empty($userColumn)) {
            return 0;
        }

        $definition = $this->database->getTables()->getTableDefinition($this->getTableName());
        $increment = $definition->getAutoIncrement();
        unset($columns[$increment]);

        $status = 'insert';
        $selector = null;
        $userData = $this->userData;
        $currentData = array_intersect_key($userData, $columns);
        if (empty($currentData)) {
            return 0;
        }

        $data = $this;
        if ($this->fromStatement) {
            $status = 'update';
            $selector = $increment;
            if (!$selector) {
                $primary = array_intersect_key($userData, $userColumn);
                foreach ($primary as $item) {
                    if ($item) {
                        $selector = $item;
                        break;
                    }
                }
            }

            foreach ($currentData as $key => $item) {
                if ($this->data[$key] === $item) {
                    unset($currentData[$key]);
                }
            }
        } elseif ($increment && isset($this->userData[$increment])) {
            try {
                $status = 'update';
                $selector = $increment;
                $data = $this->findBy($this->userData[$increment], null, null, $increment)->first();
                if (!$data) {
                    $status = 'insert';
                    $selector = null;
                } else {
                    foreach ($currentData as $key => $item) {
                        if ($data[$key] === $item) {
                            unset($currentData[$key]);
                        }
                    }
                }
            } catch (Exception $e) {

            }
        }
        if (empty($currentData)) {
            return 0;
        }

        if ($status === 'insert') {
            $sql = sprintf(
                'INSERT INTO %s(%s) VALUES(%s)',
                $this->getTableName(),
                implode(', ', $currentData),
                substr(str_repeat('?, ', count($currentData)), 0, -1)
            );

            $stmt = $this->prepare($sql);
            $stmt->execute($currentData);
            $metaSelector = null;
            foreach ($currentData as $key => $item) {
                if (isset($primary[$key])) {
                    return $this->findOneBy($item, $metaSelector)->fetchClose();
                }
            }

            if ($increment) {
                $last = $this->database->lastInsertId($increment);
                $metaSelector = $increment;
            } else {
                if ($selector) {
                    $last = $this->database->lastInsertId($selector);
                    $metaSelector = $selector;
                }
                if (empty($last)) {
                    $primary = $this->getPrimaryKeysSelectors();
                    reset($primary);
                    $key = key($primary);
                    $metaSelector = $key;
                    $last = $this->database->lastInsertId($key);
                }
            }
            if ($last) {
                return $this->findOneBy($last, $metaSelector)->fetchClose();
            }
            return true;
        }

        $value = $data->data[$selector] ?? ($userData[$selector] ?? null);
        $siteId = $this->database['site_id'] ?? ($userData['site_id'] ?? null);
        if ($siteId === null) {
            $id = $this->getModelSiteId();
            if (is_int($id)) {
                $siteId = $this->getModelSiteId();
            }
        }
        if (!$value) {
            return false;
        }

        $args = [];
        $sql = sprintf('UPDATE %s %s', $this->getTableName(), 'SET ');
        foreach ($currentData as $key => $item) {
            $sql .= sprintf('%s=?, ', $key);
            $args[] = $this->sanitizeDatabaseValue($key, $item);
        }

        $sql = rtrim($sql, ', ');
        $sql .= sprintf(' WHERE %s=?', $selector);
        $args[] = $value;
        if (!$increment && $siteId) {
            $sql = sprintf(' AND site_id=%d', $siteId);
        }
        if ($where) {
            foreach ($where as $field => $item) {
                $sql = sprintf(' AND WHERE %s=?', $field);
                $args[] = $this->sanitizeDatabaseWhereValue($field, $item);
            }
        }

        $stmt = $this->prepare($sql);
        if (!$stmt->execute($args)) {
            return false;
        }

        return $this->findOneBy($value, $selector)->fetchClose();
    }

    /**
     * @return bool
     */
    public function delete() : bool
    {
        $autoIncrement = $this->getAutoIncrementColumn();
        $selector = null;
        $value = null;
        if ($autoIncrement) {
            if (isset($this->data[$autoIncrement])
                && is_numeric($this->data[$autoIncrement])
            ) {
                $selector = $autoIncrement;
                $value = $this->data[$autoIncrement];
            } elseif (isset($this->userData[$autoIncrement])
                && is_numeric($this->userData[$autoIncrement])
            ) {
                $selector = $autoIncrement;
                $value = $this->userData[$autoIncrement];
            }
            if ($selector && is_numeric($value)) {
                $stmt = $this->prepare(
                    sprintf(
                        'DELETE FROM %s WHERE %s=?',
                        $this->getTableName(),
                        $selector
                    )
                );
                $result = $stmt->execute([$value]);
                $stmt->closeCursor();
                return $result;
            }
        }

        $siteId = null;
        $values = [];
        // $availableColumns = array_flip($this->getAvailableColumns());
        foreach ($this->getPrimaryKeysSelectors() as $item) {
            $val = $this->fromStatement
                ? ($this->data[$item] ?? null)
                : ($this->userData[$item] ?? null);
            if (is_numeric($val) || is_string($val)) {
                $values[$item] = $val;
            }
        }

        if (empty($values)) {
            return false;
        }
        $sql = sprintf('DELETE FROM %s WHERE ', $this->getTableName());
        $args = [];
        $c = 0;
        foreach ($values as $key => $item) {
            $h = ':a_' . $key;
            if ($c++ > 0) {
                $sql .= ' AND ';
            }
            $sql .= sprintf(' %s=%s ', $key, $h);
            $args[$h] = $item;
        }

        $stmt = $this->prepare($sql);
        $res = $stmt->execute($args);
        $stmt->closeCursor();
        return $res;
    }
}
