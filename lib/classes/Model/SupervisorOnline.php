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
class SupervisorOnline extends AbstractOnlineModel
{
    protected $tableName = 'sto_supervisor_online';
    protected $primaryKey = 'id';

    /**
     * @return Supervisor
     */
    public function getUserObject(): AbstractUserModel
    {
        return new Supervisor($this->database);
    }
}
