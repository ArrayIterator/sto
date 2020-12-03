<?php
namespace ArrayIterator\Database;

use ArrayIterator\Database;

/**
 * Class Table
 * @package ArrayIterator\Database
 */
class Tables implements QueryPrepareInterface
{
    /**
     * @var Table[][]
     */
    protected static $tablesRecord = [];

    /**
     * @var Table[]
     */
    protected $tables;

    /**
     * @var string[]
     */
    protected $defaultTables = [
        "sto_answer",
        "sto_class",
        "sto_class_teacher",
        "sto_dictionary",
        "sto_language_code",
        "sto_options",
        "sto_post",
        "sto_question",
        "sto_question_meta",
        "sto_religion",
        "sto_student",
        "sto_student_logs",
        "sto_student_meta",
        "sto_student_online",
        "sto_supervisor",
        "sto_supervisor_meta",
        "sto_supervisor_online",
        "sto_supervisor_position",
        "sto_task",
        "sto_translations"
    ];

    /**
     * @var Database
     */
    protected $database;

    /**
     * @var string
     */
    protected $databaseName;

    /**
     * Tables constructor.
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    /**
     * @return string[]
     */
    public function getDefaultTables(): array
    {
        return $this->defaultTables;
    }

    /**
     * @return Database
     */
    public function getDatabase(): Database
    {
        return $this->database;
    }

    /**
     * @param string $sql
     * @return Database\AbstractResult|Database\PdoResult|false
     */
    public function query(string $sql)
    {
        return $this->database->query($sql);
    }

    /**
     * @param string $sql
     * @return Database\PrepareStatement|false
     */
    public function prepare(string $sql)
    {
        return $this->database->prepare($sql);
    }

    /**
     * @param string $sql
     * @return AbstractResult|PdoResult|false
     */
    public function unbufferedQuery(string $sql)
    {
        return $this->database->unbufferedQuery($sql);
    }

    /**
     * @param string $sql
     * @return bool|PrepareStatement
     */
    public function unbufferedPrepare(string $sql)
    {
        return $this->database->unbufferedPrepare($sql);
    }

    public function rollbackBuffer()
    {
        $this->database->rollbackBuffer();
    }

    /**
     * @return string
     */
    public function getDatabaseName() : string
    {
        if (!$this->databaseName) {
            $q = $this->query('SELECT DATABASE() AS DB');
            $this->databaseName = $q->fetchAssoc()['DB'];
            $q->closeCursor();
        }
        return $this->databaseName;
    }

    /**
     * @return Table[]
     */
    public function getTableList()
    {
        if ($this->tables) {
            return $this->tables;
        }
        $dbName = $this->getDatabaseName();
        if (isset(self::$tablesRecord[$dbName])) {
            return $this->tables = self::$tablesRecord[$dbName];
        }

        $q = $this->unbufferedQuery("
                SELECT
                TABLE_NAME as table_name,
                COLUMN_NAME as column_name,
                ORDINAL_POSITION as ordinal_position,
                COLUMN_DEFAULT as default_value,
                UPPER(IS_NULLABLE) as nullable,
                UPPER(DATA_TYPE) as type, 
               CHARACTER_MAXIMUM_LENGTH as max_length,
                EXTRA as extra,
               COLUMN_KEY as column_key,
               UPPER(PRIVILEGES) as privileges
            FROM information_schema.columns
            WHERE table_schema = '{$dbName}'
              and TABLE_NAME LIKE 'sto_%'
            order by TABLE_NAME, ORDINAL_POSITION
        ");
        self::$tablesRecord[$dbName] = [];
        $tables =& self::$tablesRecord[$dbName];
        while ($w = $q->fetchAssoc()) {
            $w['nullable'] = $w['nullable'] === 'YES';
            $w['privileges'] = array_map('trim', explode(',', $w['privileges']));
            $w['max_length'] = is_numeric($w['max_length']) ? abs(intval($w['max_length'])) : null;
            $w['ordinal_position'] = abs(intval($w['ordinal_position']));
            $w['column_key'] = is_string($w['column_key']) ? $w['column_key'] : null;
            $tables[$w['table_name']][$w['column_name']] = $w;
        }
        $q->closeCursor();
        foreach ($tables as $tableName => $columns) {
            foreach ($columns as $columnKey => $column) {
                $columns[$columnKey] = new Column(
                    $this,
                    $tableName,
                    $columnKey,
                    $column['ordinal_position'],
                    $column['default_value'],
                    $column['nullable'],
                    $column['type'],
                    $column['max_length'],
                    $column['privileges'],
                    $column['extra'],
                    $column['column_key']
                );
            }

            $tables[$tableName] = new Table($tableName, $columns);
        }

        return $this->tables = self::$tablesRecord[$dbName];
    }

    /**
     * @return string[]
     */
    public function getListTable() : array
    {
        return array_keys($this->getTableList());
    }

    public function getTableDefinition(string $tableName)
    {
        return $this->getTableList()[$tableName]??(
                $this->getTableList()[strtolower($tableName)]??null
            );
    }

    public function getColumns(string $tableName)
    {
        $table = $this->getTableDefinition($tableName);
        return $table ? $table->getColumns() : null;
    }

    /**
     * @return string[]
     */
    public function getNotExistsTable()
    {
        return array_diff($this->defaultTables, $this->getListTable());
    }
}
