<?php

use ArrayIterator\ArrayGetter;

/**
 * @param string $name
 * @param null $default
 * @param int|null $siteId
 * @param $found
 * @return mixed
 */
function get_option(string $name, $default = null, int $siteId = null, &$found = null)
{
    $cacheSiteId = cache_get_site_id();
    $siteId = $siteId === null ? get_current_site_id() : $siteId;
    $switched = $cacheSiteId !== $siteId;
    if ($switched) {
        cache_switch_to($siteId);
    }
    $cache = cache_get($name, 'site_options', $found);
    if ($found) {
        $switched && cache_switch_to($cacheSiteId);
        return hook_apply('options_' . $name, $cache, $name, $default, $siteId);
    }

    $data = option()->value($name, $default, $siteId, $found);
    if ($found) {
        cache_set($name, $data, 'site_options');
    }

    $switched && cache_switch_to($cacheSiteId);
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
    $cacheSiteId = cache_get_site_id();
    $siteId = $siteId === null ? get_current_site_id() : $siteId;
    $switched = $cacheSiteId !== $siteId;
    if ($switched) {
        cache_switch_to($siteId);
    }
    cache_set($name, $value, 'site_options');
    $switched && cache_switch_to($cacheSiteId);
    return option()->set($name, $value, $siteId);
}

/**
 * @param int|null $siteId
 * @param mixed ...$args
 * @return ArrayGetter
 */
function get_options(int $siteId = null, ...$args): ArrayGetter
{
    return option()->values($siteId, ...$args);
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

    $cacheSiteId = cache_get_site_id();
    $siteId = $siteId === null ? get_current_site_id() : $siteId;
    $switched = $cacheSiteId !== $siteId;
    if ($switched) {
        cache_switch_to($siteId);
    }

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
    if ($switched) {
        cache_switch_to($siteId);
    }

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
    if ($switched) {
        cache_switch_to($siteId);
    }
    foreach ($name as $key => $item) {
        unset($name[$key]);
        if (!is_string($item)) {
            continue;
        }
        cache_delete($item, 'site_options');
        $name[$item] = '?';
    }

    $switched && cache_switch_to($cacheSiteId);
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
    $cache = cache_get($optionName, 'site_options', $found);
    if ($found) {
        return $cache;
    }

    $res = get_option($optionName, $default, get_current_site_id(), $found);
    if ($found) {
        cache_set($optionName, $res);
    }
    return $res;
}

function get_site_wide_active_modules(): array
{
    $modules = get_option('active_modules', null, 1);
    $update = false;
    if (!is_array($modules)) {
        $modules = [];
    }
    foreach ($modules as $key => $time) {
        if (!is_string($key) || !is_int($time)) {
            $update = true;
            unset($modules[$key]);
        }
    }

    if ($update) {
        update_option('active_modules', $modules, 1);
    }

    asort($modules);
    return hook_apply('site_wide_active_modules', $modules);
}

/**
 * @return array
 */
function get_site_active_modules(): array
{
    $modules = get_site_option('active_modules');

    if (!is_array($modules)) {
        $modules = [];
    }

    $update = false;
    if (!is_array($modules)) {
        $modules = [];
    }

    foreach ($modules as $key => $time) {
        if (!is_string($key) || !is_int($time)) {
            $update = true;
            unset($modules[$key]);
        }
    }

    if ($update) {
        update_option('active_modules', $modules, get_current_site_id());
    }
    asort($modules);
    return hook_apply('site_active_modules', $modules);
}

/**
 * @param string $module
 * @param bool $replaceTime
 * @return bool
 */
function set_site_wide_active_module(string $module, bool $replaceTime = false): bool
{
    $modules = get_site_wide_active_modules();
    if (isset($modules[$module]) && !$replaceTime) {
        return true;
    }
    $modules[$module] = time();
    return update_option('active_modules', $modules, 1);
}

/**
 * @param string $module
 * @param bool $replaceTime
 * @return bool
 */
function set_site_active_module(string $module, bool $replaceTime = false): bool
{
    $modules = get_site_active_modules();
    if (isset($modules[$module]) && !$replaceTime) {
        return true;
    }

    $modules[$module] = time();
    return update_option('active_modules', $modules);
}

/**
 * @param string $module
 * @return bool
 */
function remove_site_wide_active_module(string $module): bool
{
    $modules = get_site_wide_active_modules();
    if (!isset($modules[$module])) {
        return false;
    }
    unset($modules[$module]);
    return update_option('active_modules', $modules, 1);
}

/**
 * @param string $module
 * @return bool
 */
function remove_site_active_module(string $module): bool
{
    $modules = get_site_active_modules();
    if (!isset($modules[$module])) {
        return false;
    }
    unset($modules[$module]);
    return update_option('active_modules', $modules);
}

/**
 * @return array
 */
function get_all_active_modules(): array
{
    $modules = get_site_wide_active_modules();
    $siteModules = get_site_active_modules();
    foreach ($siteModules as $key => $item) {
        if (!isset($modules[$key])) {
            $modules[$key] = $item;
        }
    }

    asort($modules);
    return $modules;
}
