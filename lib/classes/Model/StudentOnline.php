<?php

namespace ArrayIterator\Model;

/**
 * Class StudentOnline
 * @package ArrayIterator\Model
 */
class StudentOnline extends AbstractOnlineModel
{
    protected $tableName = 'sto_student_online';

    /**
     * @return Student
     */
    public function getUserObject(): AbstractUserModel
    {
        return new Student($this->database);
    }
}
