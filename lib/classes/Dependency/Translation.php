<?php

namespace ArrayIterator\Dependency;

use ArrayIterator\Database\AbstractResult;
use ArrayIterator\Database\PdoResult;
use ArrayIterator\Database\PrepareStatement;
use ArrayIterator\Database\QueryPrepareInterface;
use ArrayIterator\Model\TranslationsDictionary;

// end here cause I don't want throw error
if (!defined('ROOT_DIR')) {
    return;
}

/**
 * Class Translation
 * @package ArrayIterator\Dependency
 */
class Translation implements QueryPrepareInterface
{
    protected $tableName = 'sto_translations_language';

    const ISO_2_NO_TRANSLATE = 'en';

    /**
     * @var Translator[]
     */
    protected $translations = [];

    /**
     * @var array
     */
    protected $availableLanguages;

    /**
     * @var TranslationsDictionary
     */
    protected $translationsDictionary;

    /**
     * @var string
     */
    protected $selected_language = self::ISO_2_NO_TRANSLATE;

    /**
     * Translation constructor.
     * @param TranslationsDictionary $language
     */
    public function __construct(TranslationsDictionary $language)
    {
        $this->translationsDictionary = $language;
    }

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * @param string $sql
     * @return AbstractResult|PdoResult|false
     */
    public function query(string $sql)
    {
        return $this->translationsDictionary->query($sql);
    }

    /**
     * @param string $sql
     * @return PrepareStatement|false
     */
    public function prepare(string $sql)
    {
        return $this->translationsDictionary->prepare($sql);
    }

    public function unbufferedQuery(string $sql)
    {
        return $this->translationsDictionary->unbufferedQuery($sql);
    }

    public function unbufferedPrepare(string $sql)
    {
        return $this->translationsDictionary->unbufferedPrepare($sql);
    }

    public function rollbackBuffer()
    {
        $this->translationsDictionary->rollbackBuffer();
    }

    /**
     * @return TranslationsDictionary
     */
    public function getTranslationsDictionary(): TranslationsDictionary
    {
        return $this->translationsDictionary;
    }

    /**
     * @return Translator[]
     */
    public function getTranslations(): array
    {
        return $this->translations;
    }

    /**
     * @return string
     */
    public function getSelectedLanguage(): string
    {
        return $this->selected_language;
    }

    /**
     * @param string $selected_language
     */
    public function setSelectedLanguage(string $selected_language)
    {
        $this->selected_language = $selected_language;
    }

    /**
     * @return array
     */
    public function getAvailableLanguages(): array
    {
        if (is_array($this->availableLanguages)) {
            return $this->availableLanguages;
        }

        $stmt = $this->translationsDictionary->unbufferedQuery(
            sprintf('SELECT * FROM %s', $this->getTableName())
        );

        while ($row = $stmt->fetchAssoc()) {
            $row['iso_2'] = strtolower($row['iso_2']);
            $row['iso_3'] = strtolower($row['iso_3']);
            $this->availableLanguages[$row['iso_2']] = $row;
        }

        $stmt->closeCursor();
        return $this->availableLanguages;
    }

    /**
     * @param string $code
     * @return Translator|null
     * @noinspection PhpMissingReturnTypeInspection
     */
    public function getTranslator(string $code = null)
    {
        if ($code === null) {
            $code = $this->getSelectedLanguage();
            if (!$code) {
                $code = self::ISO_2_NO_TRANSLATE;
            }
        }

        $code = trim(strtolower($code));
        if (strlen($code) > 3 && strlen($code) < 2) {
            return null;
        }

        if (isset($this->translations[$code])) {
            return $this->translations[$code];
        }

        $languageDetails = $this->getAvailableLanguages();
        if (strlen($code) === 3) {
            $oldCode = $code;
            foreach ($languageDetails as $item) {
                if ($code === $item['iso_3']) {
                    $code = $item['iso_2'];
                    break;
                }
            }
            if ($code === $oldCode) {
                return null;
            }
            $languageDetails = $languageDetails[$code];
            $this->translations[$code] = new Translator($this, $languageDetails);
            return $this->translations[$code];
        }
        if (!isset($languageDetails[$code])) {
            unset($languageDetails);
            return null;
        }
        $languageDetails = $languageDetails[$code];
        $this->translations[$code] = new Translator($this, $languageDetails);
        return $this->translations[$code];
    }

    /**
     * @param string $message
     * @param string $translation
     * @param string $languageCode
     * @return bool
     */
    public function createTranslation(
        string $message,
        string $translation,
        string $languageCode
    ): bool {
        $translator = $this->getTranslator($languageCode);
        if (!$translator) {
            return false;
        }
        return $translator->set($message, $translation);
    }

    public function clear()
    {
        $this->translations = [];
    }
}
