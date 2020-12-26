<?php

namespace ArrayIterator\Model;


use ArrayIterator\Database;
use PDO;

// end here cause I don't want throw error
if (!defined('ROOT_DIR')) {
    return;
}

/**
 * Class Languages
 * @package ArrayIterator\Model
 */
class TranslationsDictionary extends Model
{
    protected $tableName = 'sto_translations_dictionary';

    /**
     * Languages constructor.
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        parent::__construct($database);
    }

    /**
     * @return null
     */
    public function getModelSiteId()
    {
        return null;
    }

    public function unbufferedQuery(string $sql)
    {
        return $this->database->unbufferedQuery($sql);
    }

    public function unbufferedPrepare(string $sql)
    {
        return $this->database->unbufferedPrepare($sql);
    }

    public function rollbackBuffer()
    {
        $this->database->rollbackBuffer();
    }

    /**
     * @param string $message
     * @return bool|string
     */
    public function set(string $message)
    {
        $selector = sha1($message);
        $stmt = $this
            ->prepare(
                sprintf(
                    'SELECT code, translate FROM %s WHERE code = ? OR translate = ?',
                    $this->getTableName()
                )
            );
        $stmt->execute([$selector, $message]);
        $fetch = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        if ($fetch) {
            if ($fetch['code'] === $selector && $fetch['translate'] !== $message) {
                $selector = sha1($selector . (string)microtime());
            } elseif ($fetch['code'] !== $selector && $fetch['translate'] === $message) {
                return $fetch['code'];
            } else {
                return $selector;
            }
        }

        $result = $this
            ->prepare(sprintf('INSERT INTO %s(code, translate) value(?, ?)', $this->getTableName()))
            ->execute([$selector, $message]);
        if (!$result) {
            return false;
        }

        return $selector;
    }
}
