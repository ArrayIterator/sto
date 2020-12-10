<?php

namespace ArrayIterator\Model;

/**
 * Class Supervisor
 * @package ArrayIterator\Model
 */
class Supervisor extends AbstractUserModel
{
    protected $tableName = 'sto_supervisor';
    protected $primaryKey = 'id';

    /**
     * @return string
     */
    public function getUserRoleType(): string
    {
        return 'supervisor';
    }

    /**
     * @param $column
     * @param $value
     * @return mixed
     * @noinspection PhpMissingReturnTypeInspection
     */
    protected function sanitizeDatabaseValue($column, $value)
    {
        switch ($column) {
            case 'disallow_admin':
                return (bool)$value;
        }
        return parent::sanitizeDatabaseValue($column, $value);
    }

    protected function sanitizeValue($column, $value)
    {
        switch ($column) {
            case 'disallow_admin':
                return (bool)$value;
        }
        return parent::sanitizeDatabaseValue($column, $value);
    }
}
