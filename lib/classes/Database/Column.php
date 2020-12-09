<?php

namespace ArrayIterator\Database;

/**
 * Class Column
 * @package ArrayIterator\Database
 */
class Column
{
    protected $tableName;
    protected $columnName;
    protected $ordinalPosition;
    protected $defaultValue;
    protected $isNullable;
    protected $maxLength = null;
    protected $type;
    protected $privileges = [];
    protected $extra;
    protected $columnKey;
    protected $isPrimaryKey = false;
    protected $isUnique;
    protected $isIndex;
    protected $tables;
    protected $autoIncrement;

    /**
     * Column constructor.
     * @param Tables $tables
     * @param string $tableName
     * @param string $columnName
     * @param int $position
     * @param $default_value
     * @param bool $isNullable
     * @param string $type
     * @param int|null $max_length
     * @param array $privileges
     * @param string|null $extra
     * @param string|null $column_key
     */
    public function __construct(
        Tables $tables,
        string $tableName,
        string $columnName,
        int $position,
        $default_value,
        bool $isNullable,
        string $type,
        int $max_length = null,
        array $privileges = [],
        string $extra = null,
        string $column_key = null
    ) {
        $this->tables = $tables;
        $this->tableName = $tableName;
        $this->columnName = $columnName;
        $this->ordinalPosition = $position;
        $this->defaultValue = $default_value;
        $this->isNullable = $isNullable;
        $this->maxLength = $max_length;
        $this->type = $type;
        $this->privileges = $privileges;
        $this->columnKey = $column_key;
        $this->extra = $extra;
        $this->autoIncrement = is_string($this->extra) && preg_match('~auto_increment~i', $this->extra);
        $this->isPrimaryKey = $this->columnKey === 'PRI' || $this->autoIncrement;
        $this->isUnique = $this->isPrimaryKey || $this->columnKey === 'UNI';
        $this->isIndex = $this->isUnique || $this->columnKey === 'MUL';
    }

    /**
     * @return Tables
     */
    public function getTables(): Tables
    {
        return $this->tables;
    }

    /**
     * @return string
     */
    public function getTableName() : string
    {
        return $this->tableName;
    }

    /**
     * @return string
     */
    public function getColumnName(): string
    {
        return $this->columnName;
    }

    /**
     * @return int
     */
    public function getOrdinalPosition(): int
    {
        return $this->ordinalPosition;
    }

    /**
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * @return bool
     */
    public function isNullable(): bool
    {
        return $this->isNullable;
    }

    /**
     * @return int|null
     * @noinspection PhpMissingReturnTypeInspection
     */
    public function getMaxLength()
    {
        return $this->maxLength;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public function getPrivileges(): array
    {
        return $this->privileges;
    }

    /**
     * @return string|null
     * @noinspection PhpMissingReturnTypeInspection
     */
    public function getExtra()
    {
        return $this->extra;
    }

    /**
     * @return string|null
     * @noinspection PhpMissingReturnTypeInspection
     */
    public function getColumnKey()
    {
        return $this->columnKey;
    }

    /**
     * @return bool
     */
    public function isPrimaryKey() : bool
    {
        return (bool) $this->isPrimaryKey;
    }

    /**
     * @return bool
     */
    public function isUnique(): bool
    {
        return $this->isUnique;
    }

    /**
     * @return bool
     */
    public function isIndex(): bool
    {
        return $this->isIndex;
    }

    /**
     * @return bool
     */
    public function isAutoIncrement(): bool
    {
        return $this->autoIncrement;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'table_name' => $this->tableName,
            'column_name' => $this->columnName,
            'position' => $this->ordinalPosition,
            'default_value' => $this->defaultValue,
            'nullable' => $this->isNullable,
            'type' => $this->type,
            'max_length' => $this->maxLength,
            'column_key' => $this->columnKey,
            'extra' => $this->extra,
            'privileges' => $this->privileges,
        ];
    }
}
