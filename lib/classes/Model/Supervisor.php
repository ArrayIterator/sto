<?php

namespace ArrayIterator\Model;

// end here cause I don't want throw error
if (!defined('ROOT_DIR')) {
    return;
}

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
    public function objectUserLogClassName(): string
    {
        return SupervisorLogs::class;
    }

    /**
     * @return string
     */
    public function getUserRoleType(): string
    {
        return SUPERVISOR;
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
