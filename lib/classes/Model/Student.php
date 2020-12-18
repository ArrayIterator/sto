<?php

namespace ArrayIterator\Model;

/**
 * Class Student
 * @package ArrayIterator\Model
 */
class Student extends AbstractUserModel
{
    protected $tableName = 'sto_student';
    protected $primaryKey = 'id';

    /**
     * @return string
     */
    public function objectUserLogClassName(): string
    {
        return StudentLogs::class;
    }

    /**
     * @return string
     */
    public function getUserRoleType(): string
    {
        return STUDENT;
    }
}
