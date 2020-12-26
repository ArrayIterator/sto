<?php
namespace ArrayIterator\Model;

// end here cause I don't want throw error
if (!defined('ROOT_DIR')) {
    return;
}

/**
 * Class SupervisorLogs
 * @package ArrayIterator\Model
 */
class SupervisorLogs extends AbstractUserLog
{
    protected $tableName = 'sto_supervisor_logs';
}
