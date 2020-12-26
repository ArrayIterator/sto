<?php

namespace ArrayIterator\Model;

// end here cause I don't want throw error
if (!defined('ROOT_DIR')) {
    return;
}

/**
 * Class StudentLogs
 * @package ArrayIterator\Model
 */
class StudentLogs extends AbstractUserLog
{
    protected $tableName = 'sto_student_logs';
}
