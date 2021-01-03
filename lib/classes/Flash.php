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
     * @var array|bool[]
     */
    protected $previousPrefix = [];

    /**
     * @var array
     */
    private $ids = [];

    /**
     * Flash constructor.
     * @param Database $database
     * @param string|null $prefix
     */
    public function __construct(Database $database, string $prefix = null)
    {
        $this->database = $database;
        if ($prefix) {
            $this->prefix = $prefix;
        }
    }

    protected function normalizePrefix(string $name)
    {
        return str_replace(['[', '['], '', $name);
    }

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * @param string $name
     * @return string
     */
    public function setPrefix(string $name) : string
    {
        $previousPrefix = $this->prefix;
        $name = $this->normalizePrefix($name);
        $this->prefix = $name;
        if (isset($this->previousPrefix[$previousPrefix])) {
            unset($this->previousPrefix[$previousPrefix]);
        }

        unset($this->previousPrefix[$name]);
        $this->previousPrefix[$previousPrefix] = true;
        return $this->prefix;
    }

    /**
     * @return false|string
     */
    public function restorePrefix()
    {
        if (empty($this->previousPrefix)) {
            return false;
        }
        end($this->previousPrefix);
        $name = key($this->previousPrefix);
        reset($this->previousPrefix);
        return $this->setPrefix($name);
    }

    /**
     * @return bool[]
     */
    public function getPreviousPrefix(): array
    {
        return $this->previousPrefix;
    }

    public function getPrefix() : string
    {
        return $this->prefix;
    }

    protected function createNameFor(string $name, $prefix = null) : string
    {
        $prefix = $prefix ? $this->normalizePrefix($prefix) : $this->getPrefix();
        return sprintf('flash[%s][%s]', $prefix, $name);
    }

    /**
     * @param string $name
     * @param string|null $prefix
     * @param null $default
     * @return mixed|null
     */
    public function get(string $name, string $prefix = null, $default = null)
    {
        $data = $this->getData($name);
        return $data ? ($data['meta_value']??null): $default;
    }

    /**
     * @param string $name
     * @param string|null $prefix
     * @return mixed|null
     */
    public function getData(string $name, string $prefix = null)
    {
        $table = $this->tableName;
        $prefix = ! $prefix ? $this->getPrefix() : $this->normalizePrefix($prefix);
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

    public function set(string $name, $value, string $prefix = null)
    {
        $prefix = !$prefix ? $this->getPrefix() : $this->normalizePrefix($prefix);
        if (!isset(self::$currentFlash[$prefix])) {
            self::$currentFlash[$prefix] = [];
        }
        self::$currentFlash[$prefix][$name] = $value;
    }

    /**
     * @param string $name
     * @param $value
     * @param string|null $prefix
     * @return bool
     */
    public function add(string $name, $value, string $prefix = null) : bool
    {
        $prefix = !$prefix ? $this->getPrefix() : $this->normalizePrefix($prefix);
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
