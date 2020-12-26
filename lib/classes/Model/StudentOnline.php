<?php

namespace ArrayIterator\Model;

// end here cause I don't want throw error
if (!defined('ROOT_DIR')) {
    return;
}

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
