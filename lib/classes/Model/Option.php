<?php

namespace ArrayIterator\Model;

use ArrayIterator\ArrayGetter;
use ArrayIterator\Helper\StringFilter;
use Exception;

// end here cause I don't want throw error
if (!defined('ROOT_DIR')) {
    return;
}

/**
 * Class Option
 * @package ArrayIterator\Model
 */
class Option extends Model
{
    /**
     * @var string
     */
    protected $tableName = 'sto_options';

    /**
     * @return mixed|null
     */
    public function getOptionName()
    {
        return $this->data['option_name'] ?? null;
    }

    /**
     * @param string $name
     * @param int|null $siteId
     * @return Option|false
     */
    public function getByName(string $name, int $siteId = null)
    {
        $stmt = $this->findOneByName($name, $siteId);
        if ($res = $stmt->fetch()) {
            $stmt->closeCursor();
        }

        return $res ?: false;
    }

    /**
     * @param null $default
     * @return mixed|null
     */
    public function getOptionValue($default = null)
    {
        return array_key_exists('option_value', $this->data)
            ? $this->data['option_value']
            : $default;
    }

    /**
     * @return string|null
     * @noinspection PhpMissingReturnTypeInspection
     */
    public function getName()
    {
        return $this->data['option_name'] ?? null;
    }

    public function setOptionValue($value)
    {
        if ($this->fromStatement && !$this->database) {
            $this->data['option_value'] = StringFilter::unSerialize($value);
            return;
        }

        $this->data['option_value'] = $value;
    }

    /**
     * @param $column
     * @param mixed $value
     * @return mixed
     */
    public function sanitizeValue($column, $value)
    {
        if ($column === 'option_value') {
            return StringFilter::unSerialize($value);
        }
        if ($column === 'id' || $column === 'site_id') {
            return is_numeric($value) ? abs($value) : $value;
        }

        return $value;
    }

    public function sanitizeDatabaseValue($column, $value)
    {
        if ($column === 'option_value') {
            return StringFilter::serialize($value);
        }
        return $value;
    }

    public function findOneByName(string $optionName, int $siteId = null)
    {
        return $this->findOne($optionName, 'option_name', $siteId);
    }

    /**
     * @param string $optionName
     * @param null $default
     * @param int|null $siteId
     * @param $found
     * @return mixed|null
     */
    public function value(
        string $optionName,
        $default = null,
        int $siteId = null,
        &$found = null
    ) {
        $siteId = $siteId ?? $this->getModelSiteId();
        $found = false;
        $res = $this->getByName($optionName, $siteId);
        if ($res) {
            $found = true;
            return $res['option_value'];
        }

        return $default;
    }

    /**
     * @param int|null $siteId
     * @param mixed ...$optionNames
     * @return ArrayGetter
     */
    public function values(int $siteId = null, ...$optionNames): ArrayGetter
    {
        $siteId = $siteId ?? $this->getModelSiteId();
        $optionName = [];
        $c = 0;
        foreach ($optionNames as $item) {
            if (is_string($item)) {
                $optionName[sprintf(':l_%d', $c++)] = $item;
            }
        }

        $result = new ArrayGetter();
        if (empty($optionName)) {
            return $result;
        }

        $stmt = $this->unbufferedPrepare(
            sprintf(
                'SELECT * FROM %s WHERE site_id=%d AND option_name IN(%s)',
                $this->getTableName(),
                $siteId,
                implode(', ', array_keys($optionName))
            )
        );

        if (!$stmt->execute($optionName)) {
            return $result;
        }

        while ($row = $stmt->fetch()) {
            $optionName = $row['option_name'];
            $result->set($optionName, $row['option_value']);
        }
        $stmt->closeCursor();
        $this->rollbackBuffer();
        return $result;
    }

    /**
     * @param $optionName
     * @param $optionValue
     * @param int|null $siteId
     * @return bool
     */
    public function set($optionName, $optionValue, int $siteId = null): bool
    {
        $siteId = $siteId ?? $this->getModelSiteId();
        try {
            $res = $this
                ->prepare(
                    sprintf(
                        'INSERT INTO %s (option_name, option_value, site_id) values (:n, :v, :i)
                    ON DUPLICATE KEY UPDATE option_value=:v, site_id=:i',
                        $this->getTableName()
                    )
                );

            $ex = $res->execute([
                ':n' => $optionName,
                ':v' => StringFilter::serialize($optionValue),
                ':i' => $siteId
            ]);
            return (bool)$ex;
        } catch (Exception $e) {
            return false;
        } finally {
            if (isset($res) && $res) {
                $res->closeCursor();
            }
        }
    }
}
