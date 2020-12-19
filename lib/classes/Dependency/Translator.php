<?php

namespace ArrayIterator\Dependency;

use PDO;

/**
 * Class Translator
 * @package ArrayIterator\Dependency
 */
class Translator
{
    const TRANSLATION_FOUND = true;
    const TRANSLATION_DEFAULT = 1;
    const TRANSLATION_NOT_FOUND = false;

    protected $tableName = 'sto_translations';
    protected $translation;
    protected $info;
    protected $records = [];

    /**
     * @var array
     */
    protected $untranslated = [];

    /**
     * Translator constructor.
     * @param Translation $translation
     * @param array $languageDetail
     */
    public function __construct(
        Translation $translation,
        array $languageDetail
    ) {
        $this->translation = $translation;
        $this->info = $languageDetail;
    }

    /**
     * @return array[]|array|false
     */
    public function loadAll()
    {
        if ($this->getIso2() === Translation::ISO_2_NO_TRANSLATE) {
            return $this->records;
        }

        $stmt = $this
            ->translation
            ->prepare(
                sprintf(
                    "SELECT 
                    translation as translation,
                    dictionary_code as code
                    FROM %s
                    WHERE iso_3=? 
                    ",
                    $this->getTableName()
                )
            );
        if (!$stmt->execute([$this->getIso3()])) {
            return false;
        }
        while ($row = $stmt->fetchAssoc()) {
            $this->records[$row['code']] = $row;
        }
        $stmt->closeCursor();
        return $this->records;
    }

    /**
     * @param string $code
     * @param string $trans
     * @return array
     */
    public function setRecord(string $code, string $trans): array
    {
        $code = sha1($code);
        $this->records[$code] = [
            'code' => $code,
            'translation' => $trans
        ];

        return $this->records[$code];
    }

    /**
     * @param string $code
     * @param string $translation
     * @return array|false
     */
    public function addRecord(string $code, string $translation)
    {
        if (isset($this->records[sha1($code)])) {
            return false;
        }

        return $this->setRecord($code, $translation);
    }

    /**
     * @return Translation
     */
    public function getTranslation(): Translation
    {
        return $this->translation;
    }

    /**
     * @return array
     */
    public function getInfo(): array
    {
        return $this->info;
    }

    /**
     * @return string
     */
    public function getIso3(): string
    {
        return $this->info['iso_3'];
    }

    /**
     * @return string
     */
    public function getIso2(): string
    {
        return $this->info['iso_2'];
    }

    public function getLanguageName(): string
    {
        return $this->info['language_name'];
    }

    /**
     * @return array
     */
    public function getRecords(): array
    {
        return $this->records;
    }

    public function clearRecords()
    {
        $this->records = [];
    }

    /**
     * @param string $message
     * @param $found
     * @return array|false
     */
    public function get(string $message, &$found = null)
    {
        $code = sha1($message);
        if ($this->getIso2() === Translation::ISO_2_NO_TRANSLATE) {
            $found = self::TRANSLATION_DEFAULT;
            return [
                'code' => $code,
                'translation' => $message
            ];
        }

        $found = self::TRANSLATION_NOT_FOUND;
        if (isset($this->records[$code])) {
            $found = self::TRANSLATION_FOUND;
            return $this->records[$code];
        }

        $tableName = $this->getTableName();
        $tableDictionary = $this
            ->getTranslation()
            ->getTranslationsDictionary()
            ->getTableName();
        $stmt = $this
            ->translation
            ->prepare("
            SELECT 
                   translation as translation,
                   dictionary_code as code,
                   l.translate as translate
            FROM {$tableName}
            INNER JOIN {$tableDictionary} l on {$tableName}.dictionary_code = l.code
            WHERE BINARY l.translate = ? 
            LIMIT 1
        ");

        if (!$stmt->execute([trim($message)])) {
            unset($stmt);
            return false;
        }

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        if ($result) {
            $found = self::TRANSLATION_FOUND;
            return [
                'code' => $result['code'],
                'translation' => $result['translation']
            ];
        }

        $this->untranslated[$code] = $message;
        return false;
    }

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * @return string[]
     */
    public function getUntranslated(): array
    {
        return $this->untranslated;
    }

    /**
     * @param string $message
     * @param string $translation
     * @return bool
     */
    public function set(string $message, string $translation): bool
    {
        if ($this->getIso2() === Translation::ISO_2_NO_TRANSLATE) {
            return true;
        }

        $hash = sha1($message);
        if (isset($this->untranslated[$hash])) {
            unset($this->untranslated[$hash]);
        }

        if (!empty($this->records[$hash])) {
            if ($this->records[$hash]['translation'] !== $translation
                && $this->records[$hash]['code'] === $hash
            ) {
                return $this->translation->prepare(
                    sprintf(
                        'UPDATE %s SET translation=? WHERE dictionary_code=?',
                        $this->getTableName()
                    )
                )->execute([$translation, $hash]);
            }

            if ($this->records[$hash]['translation'] === $translation
                && $this->records[$hash]['code'] === $hash
            ) {
                return true;
            }
        }

        $selector = $this->translation->getTranslationsDictionary()->set($message);
        if (!$selector) {
            return false;
        }

        $stmt = $this
            ->translation
            ->prepare(
                sprintf(
                    'INSERT INTO %s (dictionary_code, iso_3, translation)
                        values(:c, :i, :t) ON DUPLICATE KEY UPDATE translation=:t
                    ',
                    $this->getTableName()
                )
            );

        $this->records[$hash] = [
            'code' => $selector,
            'translation' => $translation
        ];
        return $stmt->execute([
            ':c' => $selector,
            ':i' => $this->getIso3(),
            ':t' => $translation
        ]);
    }

    public function trans(string $message, $fallback = null, &$found = null)
    {
        $found = self::TRANSLATION_NOT_FOUND;
        $selector = sha1($message);
        if ($this->getIso2() === Translation::ISO_2_NO_TRANSLATE) {
            if (isset($this->records[$selector])) {
                $found = self::TRANSLATION_FOUND;
                return $this->records[$selector]['translation'];
            }

            $found = self::TRANSLATION_DEFAULT;
            return $message;
        }

        if (!isset($this->records[$selector])) {
            $this->records[$selector] = $this->get($message, $found);
        }

        $record = $this->records[$selector];
        if ($record === false) {
            $this->untranslated[$selector] = $message;
            return $fallback === null ? $message : $fallback;
        }
        if ($record['translation']) {
            unset($this->untranslated[$selector]);
            $found = self::TRANSLATION_FOUND;
            return $record['translation'];
        }

        $this->untranslated[$selector] = $message;
        return ($fallback === null ? $message : $fallback);
    }

    public function transOrSet(string $message, $translation = null, &$found = null)
    {
        if ($this->getIso2() === Translation::ISO_2_NO_TRANSLATE) {
            $found = self::TRANSLATION_DEFAULT;
            return $message;
        }

        $found = self::TRANSLATION_NOT_FOUND;
        $selector = sha1($message);
        if (!isset($this->records[$selector])) {
            $this->records[$selector] = $this->get($message, $found);
        }

        $record = $this->records[$selector];
        if ($record === false) {
            if ($translation !== null) {
                unset($this->untranslated[$selector]);
                $this->set($message, $translation);
                return $translation;
            }

            $this->translation->getTranslationsDictionary()->set($message);
            return $this->trans($message);
        }

        return $record['translation'];
    }
}
