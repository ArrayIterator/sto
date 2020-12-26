<?php
namespace ArrayIterator\Model;

use ArrayIterator\Database;

// end here cause I don't want throw error
if (!defined('ROOT_DIR')) {
    return;
}

/**
 * Class AbstractUserLog
 * @package ArrayIterator\Model
 * @property int $user_id
 * @property string|mixed $type
 * @property string|null $note
 */
abstract class AbstractUserLog extends Model
{
    public static function findByUserId(
        $value,
        int $limit = null,
        int $offset = null,
        Database $database = null
    ) {
        return static::findBy($value, $limit, $offset, 'user_id', null, $database);
    }

    /**
     * @param int $userId
     * @return Database\PrepareStatement|false
     */
    public function getByUserId(int $userId)
    {
        return $this->find($userId, null, null, 'user_id');
    }

    /**
     * @param AbstractUserModel $model
     * @param string $type
     * @param null $note
     * @return bool
     */
    public function insertData(
        AbstractUserModel $model,
        string $type,
        $note = null
    ) : bool {
        $userId = $model->getId();
        if (!$userId) {
            return false;
        }
        $obj = $model->getObjectUserLog();
        $obj->user_id = $userId;
        $obj->type = trim($type);
        if ($note !== null) {
            $obj->note = $note;
        }

        return (bool) $obj->save();
    }
}
