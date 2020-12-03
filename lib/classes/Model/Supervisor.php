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

    protected function sanitizeDatabaseValue($column, $value)
    {
        switch ($column) {
            case 'disallow_admin':
                return (bool) $value;
        }
        return parent::sanitizeDatabaseValue($column, $value);
    }

    protected function sanitizeValue($column, $value)
    {
        switch ($column) {
            case 'disallow_admin':
                return (bool) $value;
        }
        return parent::sanitizeDatabaseValue($column, $value);
    }
}
