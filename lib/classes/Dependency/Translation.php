<?php
namespace ArrayIterator\Dependency;

use ArrayIterator\Database\AbstractResult;
use ArrayIterator\Database\PdoResult;
use ArrayIterator\Database\PrepareStatement;
use ArrayIterator\Database\QueryPrepareInterface;
use ArrayIterator\Model\Languages;

/**
 * Class Translation
 * @package ArrayIterator\Dependency
 */
class Translation implements QueryPrepareInterface
{
    const ISO_2_NO_TRANSLATE = 'en';

    /**
     * @var Translator[]
     */
    protected $translations = [];

    /**
     * @var string[]
     */
    protected $translationRelation = [];

    /**
     * @var array
     */
    protected $availableLanguages;

    /**
     * @var Languages
     */
    protected $languages;

    /**
     * @var string
     */
    protected $selected_language = self::ISO_2_NO_TRANSLATE;

    /**
     * Translation constructor.
     * @param Languages $language
     */
    public function __construct(Languages $language)
    {
        $this->languages = $language;
    }

    /**
     * @param string $sql
     * @return AbstractResult|PdoResult|false
     */
    public function query(string $sql)
    {
        return $this->languages->query($sql);
    }

    /**
     * @param string $sql
     * @return PrepareStatement|false
     */
    public function prepare(string $sql)
    {
        return $this->languages->prepare($sql);
    }

    public function unbufferedQuery(string $sql)
    {
        return $this->languages->unbufferedQuery($sql);
    }

    public function unbufferedPrepare(string $sql)
    {
        return $this->languages->unbufferedPrepare($sql);
    }

    public function rollbackBuffer()
    {
        $this->languages->rollbackBuffer();
    }

    /**
     * @return Languages
     */
    public function getLanguages(): Languages
    {
        return $this->languages;
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

    public function getAvailableLanguages()
    {
        if (is_array($this->availableLanguages)) {
            return $this->availableLanguages;
        }

        $stmt = $this->languages->unbufferedQuery(
            'SELECT * FROM languages_code'
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
     * @param $code
     * @return Translator|null
     */
    public function getTranslator($code)
    {
        $code = trim(strtolower($code));
        if (strlen($code) > 3 && strlen($code) < 2) {
            return null;
        }

        if (isset($this->translations[$code])) {
            return $this->translations[$code];
        }

        $languages = $this->getAvailableLanguages();
        if (strlen($code) === 3) {
            $oldCode = $code;
            foreach ($languages as $item) {
                if ($code === $item['iso_3']) {
                    $code = $item['iso_2'];
                    break;
                }
            }
            if ($code === $oldCode) {
                return null;
            }
            $languages = $languages[$code];
            $this->translations[$code] = new Translator($this, $languages);
            return $this->translations[$code];
        }
        if (!isset($languages[$code])) {
            unset($languages);
            return null;
        }
        $languages = $languages[$code];
        $this->translations[$code] = new Translator($this, $languages);
        return $this->translations[$code];
    }

    public function clear()
    {
        $this->translations = [];
    }
}
