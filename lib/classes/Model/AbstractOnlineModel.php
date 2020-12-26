<?php

namespace ArrayIterator\Model;

use ArrayIterator\Database\PrepareStatement;
use Throwable;

// end here cause I don't want throw error
if (!defined('ROOT_DIR')) {
    return;
}

/**
 * Class AbstractOnlineModel
 * @package ArrayIterator\Model
 */
abstract class AbstractOnlineModel extends Model
{
    const ONLINE_SECOND = 15;
    protected $lastExecute = null;
    protected $tableId;

    /**
     * @return AbstractUserModel
     */
    abstract public function getUserObject(): AbstractUserModel;

    /**
     * @return int
     */
    public function getIntervalOnlineSecond(): int
    {
        return static::ONLINE_SECOND;
    }

    /**
     * @return bool
     */
    public function replaceOffline(): bool
    {
        $time = time();
        if ($this->lastExecute && $this->lastExecute + 4 > $time) {
            return true;
        }
        try {
            $sql = sprintf(
                'UPDATE %s SET online=FALSE WHERE now() > (last_online_at + INTERVAL %s SECOND)',
                $this->getTableName(),
                $this->getIntervalOnlineSecond()
            );
            $stmt = $this->prepare($sql);
            $stmt->execute();
            $stmt->closeCursor();
            $this->lastExecute = $time;
            return true;
        } catch (Throwable $e) {
            // pass
        }

        return false;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        // fix offline
        $this->replaceOffline();
        $sql = sprintf(
            'SELECT count(online) as o FROM %s 
                WHERE online=TRUE AND last_online_at > (now() - INTERVAL %d SECOND)
            ',
            $this->getTableName(),
            $this->getIntervalOnlineSecond()
        );
        $stmt = $this->prepare($sql);
        $stmt->execute();
        return abs($stmt->fetchAssoc()['o'] ?? 0);
    }

    /**
     * @return PrepareStatement|false
     */
    public function getUserOnline()
    {
        // fix offline
        $this->replaceOffline();
        $sql = sprintf(
            'SELECT %1$s.*,
                s.last_online_at as last_online_at,
                s.created_at as first_online_at
                FROM %1$s
                INNER JOIN %2$s s ON %1$s.id = s.id
                WHERE s.online = TRUE AND s.last_online_at > (now() - INTERVAL %3$d SECOND)
            ',
            $this->getUserObject(),
            $this->getTableName(),
            $this->getIntervalOnlineSecond()
        );
        $stmt = $this->prepare($sql);

        if ($stmt->execute()) {
            return $stmt;
        }
        $stmt->closeCursor();
        return false;
    }

    /**
     * @param AbstractUserModel $abstractUser
     * @return bool
     */
    public function setOnline(AbstractUserModel $abstractUser): bool
    {
        $id = $abstractUser->getId();
        if (!$abstractUser->isFromStatement()) {
            if ($id === 0) {
                return false;
            }
            $abstractUser = $abstractUser->findById($id);
            if (!$abstractUser) {
                return false;
            }
        }

        // do offline
        $this->replaceOffline();
        $stmt = $this->prepare(
            sprintf(
                "INSERT INTO %s(id, online)
                    VALUES (?, TRUE)
                    ON DUPLICATE KEY UPDATE online=TRUE, last_online_at = CURRENT_TIMESTAMP()
                ",
                $this->getTableName()
            )
        );
        return $stmt->execute([$id]);
    }

    /**
     * @param AbstractUserModel $student
     * @return bool
     */
    public function setOffline(AbstractUserModel $student): bool
    {
        $id = $student->getId();
        if (!$student->isFromStatement()) {
            if ($id === 0) {
                return false;
            }
            $student = $student->findOneById($id);
            if (!($student ? $student->fetch() : $student)) {
                return false;
            }
        }
        // do offline
        $this->replaceOffline();
        $stmt = $this->prepare(
            sprintf(
                "UPDATE %s SET online=FALSE WHERE id=?",
                $this->getTableName()
            )
        );

        return $stmt->execute([$id]);
    }

    /**
     * @param AbstractUserModel $student
     * @return bool
     */
    public function remove(AbstractUserModel $student): bool
    {
        $id = $student->getId();
        if (!$student->isFromStatement()) {
            if ($id === 0) {
                return false;
            }
            $student = $student->findOneById($id);
            if (!($student ? $student->fetch() : $student)) {
                return false;
            }
        }

        // do offline
        $this->replaceOffline();
        $stmt = $this->prepare(sprintf(
            "DELETE FROM %s WHERE id=?",
            $this->getTableName()
        ));
        return $stmt->execute([$id]);
    }

    /**
     * @param int $id
     * @return false|AbstractOnlineModel
     */
    public function userOnline(int $id)
    {
        $stmt = $this->prepare(sprintf(
            'SELECT
                `online`,
                `created_at`,
                `last_online_at` 
                FROM %s WHERE id=?
            ',
            $this->getTableName()
        ));

        if (!$stmt->execute([$id])) {
            return false;
        }

        $status = $stmt->fetchClose();
        return $status;
    }
}
