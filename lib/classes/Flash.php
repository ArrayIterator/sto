<?php
namespace ArrayIterator;

use ArrayIterator\Helper\StringFilter;
use Exception;
use PDOException;

/**
 * Class Flash
 * @package ArrayIterator
 */
final class Flash
{
    /**
     * @var string
     */
    protected $tableName = 'sto_metadata';

    /**
     * @var string
     */
    protected $prefix = 'flash';

    /**
     * @var Database
     */
    protected $database;

    /**
     * @var array[]
     */
    protected static $previousFlash = [];

    /**
     * @var array[]
     */
    protected static $currentFlash = [];

    /**
     * @var array
     */
    private $ids = [];

    /**
     * Flash constructor.
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }

    public function setPrefix(string $name)
    {
        $name = str_replace(['[', '['], '', $name);
        $this->prefix = $name;
    }

    public function getPrefix() : string
    {
        return $this->prefix;
    }

    protected function createNameFor(string $name, $prefix = null) : string
    {
        $prefix = $prefix ? str_replace(['[', '['], '', $prefix) : $this->getPrefix();
        return sprintf('flash[%s][%s]', $prefix, $name);
    }

    /**
     * @param string $name
     * @param null $default
     * @return mixed|null
     */
    public function get(string $name, $default = null)
    {
        $data = $this->getData($name);
        return $data ? ($data['meta_value']??null): $default;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getData(string $name)
    {
        $table = $this->tableName;
        $prefix = $this->getPrefix();
        if (!isset(self::$previousFlash[$prefix])) {
            self::$previousFlash[$prefix] = [];
            $stmt = $this
                ->database
                ->unbufferedQuery(
                    sprintf(
                        "SELECT * FROM {$table} WHERE meta_name LIKE %s",
                        $this->database->quote(sprintf('flash[%s][%%', $this->getPrefix()))
                    )
                );
            while ($row = $stmt->fetchAssoc()) {
                $this->ids[] = (int) $row['id'];
                if (!preg_match('#^flash\[([^]]+)\]\[(.+)\]$#', $row['meta_name'], $match)) {
                    continue;
                }
                $prefix = $match[1];
                $sName = $match[2];
                $row['meta_value'] = StringFilter::unSerialize($row['meta_value']);
                self::$previousFlash[$prefix][$sName] = $row;
            }
            $stmt->closeCursor();
        }

        return isset(self::$previousFlash[$prefix][$name])
            ? self::$previousFlash[$prefix][$name]
            : null;
    }

    public function remove(string $name)
    {
        $prefix = $this->getPrefix();
        if (!isset(self::$currentFlash[$prefix])) {
            return;
        }
        unset(self::$currentFlash[$prefix][$name]);
    }

    public function set(string $name, $value)
    {
        $prefix = $this->getPrefix();
        if (!isset(self::$currentFlash[$prefix])) {
            self::$currentFlash[$prefix] = [];
        }
        self::$currentFlash[$prefix][$name] = $value;
    }

    /**
     * @param string $name
     * @param $value
     * @return bool
     */
    public function add(string $name, $value) : bool
    {
        $prefix = $this->getPrefix();
        if (isset(self::$currentFlash[$prefix], self::$currentFlash[$prefix][$name])) {
            return false;
        }
        $this->set($name, $value);
        return true;
    }

    /**
     * @return array[]
     */
    public static function getPreviousFlash(): array
    {
        return self::$previousFlash;
    }

    /**
     * @return array[]
     */
    public static function getCurrentFlash(): array
    {
        return self::$currentFlash;
    }

    public function __destruct()
    {
        $table = $this->tableName;
        try {
            $sql = "DELETE FROM {$table} WHERE (created_at < (NOW() - INTERVAL 30 SECOND))";
            if (!empty($this->ids)) {
                $id = reset($this->ids);
                $sql .= count($this->ids) > 1
                    ? sprintf(' OR id IN(%s) ', implode(', ', $this->ids))
                    : (
                        is_int($id)
                            ? sprintf(' OR id = %s ', reset($this->ids))
                            : ''
                    );
            }
            $stmt = $this->database->prepare($sql);
            $stmt->execute();
        } catch (Exception $e) {
            // pass
        }

        try {
            $this->database->beginTransaction();
            foreach (self::$currentFlash as $prefix => $item) {
                unset(self::$currentFlash[$prefix]);
                foreach ($item as $keyName => $value) {
                    $stmt = $this
                        ->database
                        ->prepare(
                            "INSERT INTO {$table} (meta_name, meta_value, created_at) 
                                VALUES (:name, :value, now())
                                ON DUPLICATE
                                    KEY UPDATE meta_value=:value, created_at=now()
                            "
                        );
                    $stmt->execute([
                        ':name' => sprintf('flash[%s][%s]', $prefix, $keyName),
                        ':value' => StringFilter::serialize($value)
                    ]);
                }
            }
            $this->database->commit();
        } catch (PDOException $ex) {
            $this->database->rollBack();
        } catch (Exception $e) {
            // pass
        }
    }
}
