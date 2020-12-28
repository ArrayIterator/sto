<?php

use ArrayIterator\Dependency\Translation;
use ArrayIterator\Dependency\Translator;

/**
 * @return string
 */
function get_translations_table_name() : string
{
    return translation()->getTableName();
}

/**
 * @return string
 */
function get_translator_table_name() : string
{
    return translation()->getTranslator()->getTableName();
}

/**
 * Get available translated Languages
 *
 * @return array
 */
function get_available_translated_language(): array
{
    $cache = cache_get('available_translated_language', 'languages', $found);
    if ($found && is_array($cache)) {
        return hook_apply('available_translated_language', $cache);
    }

    $stmt = database_query(
        sprintf(
            'SELECT k.iso_3, k.iso_2, k.language_name 
            FROM `%s` as k
            INNER JOIN `%s` as l ON l.iso_3 = k.iso_3
            GROUP BY l.iso_3',
            get_translations_table_name(),
            get_translator_table_name()
        )
    );

    $data = [];
    while ($row = $stmt->fetchAssoc()) {
        $row['iso_2'] = strtolower($row['iso_2']);
        $row['iso_3'] = strtolower($row['iso_3']);
        $row['path'] = false;
        $row['db'] = true;
        $data[$row['iso_2']] = $row;
    }
    $stmt->closeCursor();
    foreach (get_language_files() as $key => $item) {
        if (isset($data[$key])) {
            $data[$key]['path'] = $item['path'] ?: null;
            continue;
        }

        $item['db'] = false;
        $data[$key] = $item;
    }

    cache_set('available_translated_language', $data, 'languages');
    return hook_apply('available_translated_language', $data);
}

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
function get_language_files(): array
{
    $files  = cache_get('language_files', 'globals', $found);
    if ($found && is_array($files)) {
        return $files;
    }

    $files = [];

    cache_set('language_files', $files, 'globals');
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
        $lang = $lang ?? [
                'iso_3' => 'eng',
                'iso_2' => 'en',
                'language_name' => 'English',
            ];
        $lang['path'] = null;
        $files[Translation::ISO_2_NO_TRANSLATE] = $lang;
    }

    cache_set('language_files', $files, 'globals');
    return $files;
}

/**
 * @return string
 */
function get_selected_site_language(): string
{
    $selectedLanguage = \translation()->getSelectedLanguage();
    $lang = get_option('selected_language', null, get_current_site_id(), $found);
    $update = false;
    if (!$found || !is_string($lang)) {
        $update = true;
        $lang = translation()->getSelectedLanguage();
    }

    $lang = is_string($lang) ? trim($lang) : $lang;
    if ($update) {
        update_option('selected_language', $lang);
    }

    $language = hook_apply('selected_language', $lang);
    if (!is_string($language)) {
        $language = translation()->getSelectedLanguage();
    }

    if (!isset(get_language_files()[$language])
        && !isset(get_available_languages()[$language])
    ) {
        if ($language !== $lang && !isset(get_language_files()[$language])) {
            $language = Translation::ISO_2_NO_TRANSLATE;
        }
    }

    if ($selectedLanguage !== $language) {
        translation()->setSelectedLanguage($language);
    }

    return $language;
}

/**
 * @return Translator
 */
function translator(): Translator
{
    static $required = [];

    $selectedLanguage = get_selected_site_language();

    $translator = translation()->getTranslator($selectedLanguage);
    if (!isset($required[$selectedLanguage])) {
        $required[$selectedLanguage] = true;
        $languages = get_language_files()[$selectedLanguage]??(get_available_languages()[$selectedLanguage]??null);
        if ($languages
            && isset($languages['path'])
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
 * @param string|null $message
 * @param string|null $fallback
 * @param null $found
 * @return string
 */
function translate(string $message = null, string $fallback = null, &$found = null): string
{
    if ($message === null) {
        $found = false;
        return '';
    }

    return translator()->trans($message, $fallback, $found);
}

/**
 * @param string $message
 * @param $found
 * @return string
 */
function trans(string $message = null, &$found = null): string
{
    return translate($message, null, $found);
}

/**
 * @param string $message
 * @param $found
 */
function trans_e(string $message = null, &$found = null)
{
    render(trans($message, $found));
}

/**
 * @param string $message
 * @param mixed ...$args
 * @return string
 */
function trans_sprintf(string $message = null, ...$args): string
{
    if ($message === null) {
        return '';
    }

    return sprintf(trans($message), ...$args);
}

/**
 * @param string $message
 * @param mixed ...$args
 */
function trans_printf(string $message = null, ...$args)
{
    if ($message === null) {
        return;
    }

    printf(trans($message), ...$args);
}

/**
 * @param string $message

 * @param $found
 * @return string
 */
function esc_html_trans(string $message = null, &$found = null): string
{
    return esc_html(trans($message, $found));
}

/**
 * @param string $message
 * @param $args
 * @return string
 */
function esc_html_trans_sprintf(string $message = null, ...$args): string
{
    return esc_html(trans_sprintf($message, ...$args));
}

/**
 * @param string $message
 * @param $args
 */
function esc_html_trans_printf(string $message = null, ...$args)
{
    render(esc_html_trans_sprintf($message, ...$args));
}

function esc_html_trans_e(string $message = null, &$found = null)
{
    render(esc_html_trans($message, $found));
}

/**
 * @param string $message
 * @param $args
 */
function esc_html_trans_sprintf_e(string $message = null, ...$args)
{
    esc_html_trans_printf($message, ...$args);
}


/**
 * @param string $message
 * @param $found
 * @return string
 */
function esc_attr_trans(string $message = null, &$found = null): string
{
    return esc_attr(trans($message, $found));
}

function esc_attr_trans_sprintf(string $message = null, ...$args): string
{
    return esc_attr(trans_sprintf($message, ...$args));
}

function esc_attr_trans_printf(string $message = null, ...$args)
{
    render(esc_attr(trans_sprintf($message, ...$args)));
}

function esc_attr_trans_sprintf_e(string $message = null, ...$args)
{
    esc_attr_trans_printf($message, ...$args);
}

/**
 * @param string $message
 * @param $found
 */
function esc_attr_trans_e(string $message = null, &$found = null)
{
    render(esc_attr_trans($message, $found));
}
