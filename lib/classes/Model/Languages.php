<?php
namespace ArrayIterator\Model;


use ArrayIterator\Database;
use ArrayIterator\Dependency\Translation;

/**
 * Class Languages
 * @package ArrayIterator\Model
 */
class Languages extends Model
{
    protected $tableName = 'sto_languages';
    protected $translation;

    /**
     * Languages constructor.
     * @param Database $database
     * @param Translation $translation
     */
    public function __construct(Database $database, Translation $translation)
    {
        parent::__construct($database);
        $this->translation = $translation;
    }

    /**
     * @return Translation
     */
    public function getTranslation(): Translation
    {
        return $this->translation;
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
            ->prepare('SELECT code, translate FROM languages WHERE code = ? OR translate = ?');
        $stmt->execute([$selector, $message]);
        $fetch = $stmt->fetch(\PDO::FETCH_ASSOC);
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
            ->prepare('INSERT INTO languages(code, translate) value(?, ?)')
            ->execute([$selector, $message]);
        if (!$result) {
            return false;
        }

        return $selector;
    }

    /**
     * @param string $message
     * @param string $translation
     * @param string $languageCode
     * @return bool|null
     */
    public function createTranslation(
        string $message,
        string $translation,
        string $languageCode
    ) {
        $translator = $this->translation->getTranslator($languageCode);
        if (!$translator) {
            return null;
        }
        return $translator->set($message, $translation);
    }
}
