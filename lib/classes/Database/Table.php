<?php
namespace ArrayIterator\Database;

/**
 * Class Table
 * @package ArrayIterator\Database
 */
class Table
{
    protected $tableName;

    /**
     * @var string[]
     */
    protected $primaryKey = [];

    /**
     * @var string[]
     */
    protected $unique = [];

    /**
     * @var Column[]
     */
    protected $columns = [];
    /**
     * @var string|null
     */
    protected $autoIncrement;
    /**
     * Table constructor.
     * @param string $tableName
     * @param Column[] $columns
     */
    public function __construct(string $tableName, array $columns)
    {
        $this->tableName = $tableName;
        $columnsSort = [];
        $columnData = [];

        foreach ($columns as $key => $column) {
            unset($columns[$key]);
            $tableNamed = $column->getTableName();
            if ($tableName !== $tableNamed) {
                continue;
            }
            if (!$this->autoIncrement && $column->isAutoIncrement()) {
                $this->autoIncrement = $column->getColumnName();
            }
            if ($column->isPrimaryKey()) {
                $this->primaryKey[] = $column->getColumnName();
            } elseif ($column->isUnique()) {
                $this->unique[] = $column->getColumnName();
            }

            $columnName = $column->getColumnName();
            $columnsSort[$columnName] = $column->getOrdinalPosition();
            $columnData[$columnName]  = $column;
        }

        asort($columnsSort);

        foreach ($columnsSort as $key => $v) {
            $this->columns[$key] = $columnData[$key];
            unset($columnData[$key], $columnsSort[$key]);
        }
    }

    /**
     * @return string|null
     */
    public function getAutoIncrement(): string
    {
        return $this->autoIncrement;
    }

    /**
     * @return array
     */
    public function getUniqueSelector() : array
    {
        return array_unique(array_merge($this->primaryKey, $this->unique));
    }

    /**
     * @return string[]
     */
    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }

    /**
     * @return string[]
     */
    public function getUnique(): array
    {
        return $this->unique;
    }

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * @return Column[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @return array
     */
    public function getListColumn() : array
    {
        return array_keys($this->columns);
    }

    /**
     * @param string $columnName
     * @return Column|null
     */
    public function getColumn(string $columnName)
    {
        return $this->columns[$columnName]
            ??($this->columns[strtolower($columnName)] ?? null);
    }
}
