<?php

use ArrayIterator\ArrayGetter;
use ArrayIterator\Info\Theme;

/**
 * @return string
 */
function get_option_table_name() : string
{
    return option()->getTableName();
}

/**
 * @param string $name
 * @param null $default
 * @param int|null $siteId
 * @param $found
 * @return mixed
 */
function get_option(string $name, $default = null, int $siteId = null, &$found = null)
{
    $siteId = $siteId === null ? get_current_site_id() : $siteId;

    $cache = cache_get_current($name, 'site_options', $found, $siteId);
    if ($found) {
        return hook_apply('options_' . $name, $cache, $name, $default, $siteId);
    }

    $data = option()->value($name, $default, $siteId, $found);
    if ($found) {
        $optionSiteId = $data['site_id']??null;
        if (is_int($optionSiteId) && $optionSiteId && $siteId !== $optionSiteId) {
            cache_set_current($name, $data, 'site_options', $optionSiteId);
        }
        cache_set_current($name, $data, 'site_options', $siteId);
    }

    return hook_apply('options_' . $name, $data, $name, $default, $siteId);
}

/**
 * @param string $name
 * @param $value
 * @param int|null $siteId
 * @return bool
 */
function update_option(string $name, $value, int $siteId = null): bool
{
    $siteId = $siteId === null ? get_current_site_id() : $siteId;
    cache_set_current($name, $value, 'site_options', $siteId);
    return option()->set($name, $value, $siteId);
}

/**
 * @param int|null $siteId
 * @param mixed ...$args
 * @return ArrayGetter
 */
function get_options(int $siteId = null, ...$args): ArrayGetter
{
    $siteId = $siteId === null ? get_current_site_id() : $siteId;
    $data = option()->values($siteId, ...$args);
    $cacheId = cache_get_site_id();
    $is_switched = $siteId !== $cacheId;
    $is_switched && cache_switch_to($siteId);
    foreach ($data as $key => $item) {
        object_cache()->set($key, $item, 'site_options');
    }

    $is_switched && cache_switch_to($cacheId);
    return $data;
}

/**
 * @param array $value
 * @param int|null $siteId
 * @return bool
 */
function update_options(array $value, int $siteId = null): bool
{
    if (empty($value)) {
        return false;
    }

    $siteId = $siteId === null ? get_current_site_id() : $siteId;
    $cacheSiteId = cache_get_site_id();
    $switched = $cacheSiteId !== $siteId;
    $switched && cache_switch_to($siteId);

    $options = option();
    $value = [];
    foreach ($value as $key => $item) {
        unset($value[$key]);
        if (!is_string($key)) {
            continue;
        }
        cache_set($key, $item, 'site_options');
        $value[$key] = $options->sanitizeDatabaseValue('option_value', $item);
    }

    $switched && cache_switch_to($cacheSiteId);
    if (empty($value)) {
        return false;
    }

    $siteId = $siteId ?? get_current_site_id() ?: $options->getSiteId();
    $optionTable = $options->getTableName();
    /** @noinspection SyntaxError */
    $sql = sprintf(
        'INSERT INTO %s (site_id, option_name, option_value) VALUES ',
        $optionTable
    );
    $args = [];
    $c = 0;
    foreach ($value as $key => $item) {
        unset($value[$key]);
        $c++;
        $h = sprintf(':h_%d', $c);
        $r = sprintf(':r_%d', $c);
        $args[$h] = $key;
        $args[$r] = $item;
        $sql .= sprintf('(%s, %s, %s), ', $siteId, $h, $r);
    }
    $sql = rtrim($sql, ', ');
    $sql .= ' ON DUPLICATE KEY UPDATE option_name=VALUE(option_name), option_value=VALUE(option_value)';

    $stmt = database_prepare(
        $sql
    );
    $bool = $stmt->execute($args);
    $stmt->closeCursor();
    unset($args);
    return $bool;
}


/**
 * @param string $name
 * @param int|null $siteId
 * @return bool
 */
function delete_option(string $name, int $siteId = null): bool
{
    $options = option();
    $siteId = func_num_args() === 1
        ? (get_current_site_id() ?: $options->getSiteId())
        : $siteId;
    $sql = sprintf(
        'DELETE FROM %s WHERE `site_id`%s AND option_name=?',
        $options->getTableName(),
        ($siteId === null ? ' IS NULL' : sprintf('=%s', $siteId))
    );

    $cacheSiteId = cache_get_site_id();
    $siteId = $siteId === null ? get_current_site_id() : $siteId;
    $switched = $cacheSiteId !== $siteId;
    $switched && cache_switch_to($siteId);
    cache_delete($name, 'site_options');
    $switched && cache_switch_to($cacheSiteId);

    $stmt = database_prepare($sql);
    $res = $stmt->execute([$name]);
    $stmt->closeCursor();
    return (bool)$res;
}

/**
 * @param string[] $name
 * @param int|null $siteId
 * @return bool
 */
function delete_options(array $name, int $siteId = null): bool
{
    if (empty($name)) {
        return false;
    }
    $cacheSiteId = cache_get_site_id();
    $siteId = $siteId === null ? get_current_site_id() : $siteId;
    $switched = $cacheSiteId !== $siteId;
    $switched && cache_switch_to($siteId);
    $deleted = [];
    foreach ($name as $key => $item) {
        unset($name[$key]);
        $deleted[] = $key;
        if (!is_string($item)) {
            continue;
        }
        cache_delete($item, 'site_options');
        $name[$item] = '?';
    }
    $switched && cache_switch_to($cacheSiteId);
    if ($switched) {
        foreach ($deleted as $item) {
            cache_delete($item, 'site_options');
        }
    }

    if (empty($name)) {
        return false;
    }


    $options = option();
    $siteId = func_num_args() === 1
        ? (get_current_site_id() ?: $options->getSiteId())
        : $siteId;
    $sql = sprintf(
        'DELETE FROM %s WHERE `site_id`%s',
        $options->getTableName(),
        ($siteId === null ? ' IS NULL' : sprintf('=%s', $siteId))
    );
    if (count($name) === 1) {
        $sql .= ' AND option_name=?';
    } else {
        $sql .= sprintf(' AND option_name IN(%s)', implode(',', $name));
    }
    $stmt = database_prepare($sql);
    $res = $stmt->execute(array_keys($name));
    $stmt->closeCursor();
    return (bool)$res;
}

function get_site_option(string $optionName, $default = null)
{
    $siteId = get_current_site_id();
    $cache = cache_get_current($optionName, 'site_options', $found, $siteId);
    if ($found) {
        return $cache;
    }

    $res = get_option($optionName, $default, $siteId, $found);
    if ($found) {
        cache_set_current($optionName, $res, 'site_options', $siteId);
    }
    return $res;
}

/**
 * @return Theme
 */
function get_active_theme(): Theme
{
    $site_id = get_current_site_id();
    $theme = get_option('active_theme', null, $site_id, $found);
    if ($theme) {
        $theme = get_theme($theme);
    }

    if (!$theme || !$found) {
        $themes = get_all_themes();
        $theme = key($themes);
        if ($theme) {
            update_option('active_theme', $theme, $site_id);
        }

        $theme = $themes[$theme];
    }

    return $theme ?: new Theme('', []);
}

/**
 * @return Theme
 */
function get_current_theme(): Theme
{
    return get_active_theme();
}

/**
 * @return bool
 */
function allow_student_reset_password() : bool
{
    $allowed = cache_get_current('allow_student_forgot_password', 'site_options', $found);
    if (!$found) {
        $allowed = get_site_option('allow_student_forgot_password');
        $allowed = $allowed === null ? 'no' : $allowed;
    }

    $applied = hook_apply('allow_student_forgot_password', $allowed);
    $applied = is_string($applied) ? strtolower(trim($applied)) : $applied;
    return $applied === null || $applied === false
        ? false
        : in_array($applied, ['yes', 'y', 'true', '1', true, 1], true);
}
