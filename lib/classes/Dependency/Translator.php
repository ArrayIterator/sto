<?php

namespace ArrayIterator\Dependency;

use PDO;

/**
 * Class Translator
 * @package ArrayIterator\Dependency
 */
class Translator
{
    protected $tableName = '';
    protected $translation;
    protected $info;
    protected $records = [];

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
            ->prepare("SELECT 
                    translation as translation,
                    language_code as code
                    FROM languages_translation
                    WHERE iso_3=? 
                    "
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
    public function setRecord(string $code, string $trans) : array
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
     * @return array|false
     */
    public function get(string $message)
    {
        $code = sha1($message);
        if ($this->getIso2() === Translation::ISO_2_NO_TRANSLATE) {
            return [
                'code' => $code,
                'translation' => $message
            ];
        }
        if (isset($this->records[$code])) {
            return $this->records[$code];
        }
        $stmt = $this
            ->translation
            ->prepare("
            SELECT 
                   translation as translation,
                   code as code,
                   l.translate as translate
            FROM languages_translation
            INNER JOIN languages l on languages_translation.language_code = l.code
            WHERE l.translate = ? 
            LIMIT 1
        ");

        if (!$stmt->execute([trim($message)])) {
            unset($stmt);
            return false;
        }

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result ? [
            'code' => $result['code'],
            'translation' => $result['translation']
        ] : false;
    }

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * @param string $message
     * @param string $translation
     * @return bool
     */
    public function set(string $message, string $translation) : bool
    {
        if ($this->getIso2() === Translation::ISO_2_NO_TRANSLATE) {
            return true;
        }

        $hash = sha1($message);
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

    public function trans($message, $fallback = null)
    {
        $selector = sha1($message);
        if ($this->getIso2() === Translation::ISO_2_NO_TRANSLATE) {
            if (isset($this->records[$selector])) {
                return $this->records[$selector]['translation'];
            }
            return $message;
        }

        if (!isset($this->records[$selector])) {
            $this->records[$selector] = $this->get($message);
        }

        $record = $this->records[$selector];
        if ($record === false) {
            return $fallback === null ? $message : $fallback;
        }

        return $record['translation'] ?? ($fallback === null ? $message : $fallback);
    }

    public function transOrSet($message, $translation = null)
    {
        if ($this->getIso2() === Translation::ISO_2_NO_TRANSLATE) {
            return $message;
        }
        $selector = sha1($message);
        if (!isset($this->records[$selector])) {
            $this->records[$selector] = $this->get($message);
        }

        $record = $this->records[$selector];
        if ($record === false) {
            if ($translation !== null) {
                $this->set($message, $translation);
                return $translation;
            }

            $this->translation->getTranslationsDictionary()->set($message);
            return $this->trans($message);
        }

        return $record['translation'];
    }
}
