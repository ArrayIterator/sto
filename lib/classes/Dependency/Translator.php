<?php

namespace ArrayIterator\Dependency;

/**
 * Class Translator
 * @package ArrayIterator\Dependency
 */
class Translator
{
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

    /**
     * @param string $message
     * @return array|false
     */
    public function get(string $message)
    {
        if ($this->getIso2() === Translation::ISO_2_NO_TRANSLATE) {
            return [
                'code' => sha1($message),
                'translation' => $message
            ];
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

        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result ? [
            'code' => $result['code'],
            'translation' => $result['translation']
        ] : false;
    }

    public function set($message, $translation)
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
                    'UPDATE languages_translation SET translation=? WHERE language_code=?'
                )->execute([$translation, $hash]);
            }

            if ($this->records[$hash]['translation'] === $translation
                && $this->records[$hash]['code'] === $hash
            ) {
                return true;
            }
        }

        $selector = $this->translation->getLanguages()->set($message);
        if (!$selector) {
            return false;
        }

        $stmt = $this
            ->translation
            ->prepare(
                'INSERT INTO languages_translation (language_code, iso_3, translation)
                values(:c, :i, :t) ON DUPLICATE KEY UPDATE translation=:t
            '
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
        if ($this->getIso2() === Translation::ISO_2_NO_TRANSLATE) {
            return $message;
        }

        $selector = sha1($message);
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

            $this->translation->getLanguages()->set($message);
            return $this->trans($message);
        }

        return $record['translation'];
    }
}
