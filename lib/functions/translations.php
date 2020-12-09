<?php

use ArrayIterator\Dependency\Translation;
use ArrayIterator\Dependency\Translator;

function get_available_languages()
{
    $cache = cache_get('available_languages', 'languages', $found);
    if ($found && is_array($cache)) {
        return hook_apply('available_languages', $cache);
    }
    $data = translation()->getAvailableLanguages();
    cache_set('available_languages', $data, 'languages');
    return hook_apply('available_languages', $data);
}

/**
 * @return array
 */
function get_language_files() : array
{
    static $files;
    if (is_array($files)) {
        return $files;
    }
    $files = [];
    $languageDir = get_language_dir();
    if (!is_dir($languageDir)) {
        return $files;
    }
    $lang = get_available_languages();
    foreach ($lang as $key => $l) {
        $file = sprintf('%s/%s.php', get_language_dir(), $l['iso_2']);
        if (!is_file($file)) {
            $file = sprintf('%s/%s.php', get_language_dir(), $l['iso_3']);
            if (!is_file($file)) {
                continue;
            }
        }

        $l['path'] = normalize_directory($file);
        $files[$key] = $l;
    }
    if (!isset($files[Translation::ISO_2_NO_TRANSLATE])) {
        $lang = $lang??[
            'iso_3' => 'eng',
            'iso_2' => 'en',
            'language_name' => 'English',
        ];
        $lang['path'] = null;
        $files[Translation::ISO_2_NO_TRANSLATE] = $lang;
    }
    return $files;
}

/**
 * @return string
 */
function get_selected_site_language() : string
{
    $selectedLanguage = \translation()->getSelectedLanguage();
    $lang = get_option('selected_language', null, get_current_site_id(), $found);
    $update = false;
    if (!$found || !is_string($lang)) {
        $update = true;
        $lang = \translation()->getSelectedLanguage();
    }
    if ($update) {
        update_option('selected_language', $lang);
    }

    $language = hook_apply('selected_language', $lang);
    if (!is_string($language)) {
        $language = \translation()->getSelectedLanguage();
    }

    if (!isset(get_language_files()[$language])
        && !isset(get_available_languages()[$language])
    ) {
        if ($language !== $lang && !isset(get_language_files()[$language])) {
            $language = Translation::ISO_2_NO_TRANSLATE;
        }
    }

    if ($selectedLanguage !== $language) {
        \translation()->setSelectedLanguage($language);
    }
    return $language;
}

/**
 * @return Translator
 */
function translator() : Translator
{
    static $required = [];

    $selectedLanguage = \translation()->getSelectedLanguage();
    $translator = translation()->getTranslator($selectedLanguage);
    if (!isset($required[$selectedLanguage])) {
        $required[$selectedLanguage] = true;
        $languages = get_language_files()[$selectedLanguage];
        if ($languages['path']
            && is_string($languages['path'])
            && is_file($languages['path'])
        ) {
            $translator->loadAll();
            /** @noinspection PhpIncludeInspection */
            $languages = @require $languages['path'];
            if (!is_array($languages)) {
                return $translator;
            }

            foreach ($languages as $key => $str) {
                unset($languages[$key]);
                if (!is_string($str) || !is_string($key)) {
                    continue;
                }
                $translator->addRecord($key, $str);
            }
        }
    }

    return $translator;
}

/**
 * @param string $code
 * @return string
 */
function translate(string $code) : string
{
    return translator()->trans($code);
}

/**
 * @param string $code
 * @return string
 */
function trans(string $code) : string
{
    return \translator()->trans($code);
}
