<?php

use ArrayIterator\Cache\Adapter\ObjectCache;

/**
 * @return int
 */
function cache_get_site_id(): int
{
    return object_cache()->getSiteId();
}

/**
 * @param int $siteId
 * @return ObjectCache
 */
function cache_switch_to(int $siteId): ObjectCache
{
    return object_cache()->switchTo($siteId);
}

/**
 * @param array $groups
 */
function cache_add_global_groups(array $groups)
{
    object_cache()->addGlobalGroups($groups);
}

/**
 * @param int|float|string $key
 * @param $data
 * @param string $group
 * @param int $exp
 * @return bool
 */
function cache_add($key, $data, string $group = ObjectCache::DEFAULT_GROUP, int $exp = 0): bool
{
    return object_cache()->add($key, $data, $group, $exp);
}

/**
 * @param int|float|string $key
 * @param $data
 * @param string $group
 * @param int $exp
 * @return bool
 */
function cache_set($key, $data, string $group = ObjectCache::DEFAULT_GROUP, int $exp = 0): bool
{
    return object_cache()->add($key, $data, $group, $exp);
}

function cache_set_current($key, $data, string $group = ObjectCache::DEFAULT_GROUP, int $exp = 0, int $siteId = null): bool
{
    $site_id = get_current_site_id();
    $siteId = $siteId === null ? $site_id : $siteId;
    $cache_set_id = cache_get_site_id();

    $is_switched = $siteId !== $cache_set_id;
    if (!$is_switched) {
        return cache_set($key, $data, $group, $exp);
    }

    cache_switch_to($siteId);
    $res = cache_set($key, $data, $group, $exp);
    cache_switch_to($cache_set_id);

    return $res;
}

/**
 * @param string|int|float $key
 * @param string $group
 * @param $found
 * @return mixed
 */
function cache_get($key, string $group = ObjectCache::DEFAULT_GROUP, &$found = null)
{
    return object_cache()->get($key, $group, $found);
}

/**
 * @param $key
 * @param string $group
 * @param null $found
 * @param int|null $siteId
 * @return mixed
 */
function cache_get_current($key, string $group = ObjectCache::DEFAULT_GROUP, &$found = null, int $siteId = null)
{
    static $site_id = null;
    if ($site_id === null) {
        $site_id = get_current_site_id();
    }

    $siteId = $siteId === null ? $site_id : $siteId;
    $cache_set_id = cache_get_site_id();

    $is_switched = $siteId !== $cache_set_id;
    if (!$is_switched) {
        return cache_get($key, $group, $found);
    }

    cache_switch_to($siteId);
    $res = cache_get($key, $group, $found);
    cache_switch_to($cache_set_id);

    return $res;
}

/**
 * @param string|float|int $key
 * @param string $group
 * @return bool
 */
function cache_delete($key, string $group = ObjectCache::DEFAULT_GROUP): bool
{
    return object_cache()->delete($key, $group);
}

/**
 * @param $key
 * @param string $group
 * @param int|null $siteId
 * @return bool
 */
function cache_delete_current($key, string $group = ObjectCache::DEFAULT_GROUP, int $siteId = null): bool
{
    static $site_id = null;
    if ($site_id === null) {
        $site_id = get_current_site_id();
    }

    $siteId = $siteId === null ? $site_id : $siteId;
    $cache_set_id = cache_get_site_id();

    $is_switched = $siteId !== $cache_set_id;
    if (!$is_switched) {
        return cache_delete($key, $group);
    }

    cache_switch_to($siteId);
    $res = cache_delete($key, $group);
    cache_switch_to($cache_set_id);
    return $res;
}

/**
 * @param $key
 * @param string $group
 * @return bool
 */
function cache_exist($key, string $group = ObjectCache::DEFAULT_GROUP): bool
{
    return object_cache()->exist($key, $group);
}

/**
 * @param $key
 * @param string $group
 * @param int|null $siteId
 * @return bool
 */
function cache_exist_current($key, string $group = ObjectCache::DEFAULT_GROUP, int $siteId = null): bool
{
    static $site_id = null;
    if ($site_id === null) {
        $site_id = get_current_site_id();
    }

    $siteId = $siteId === null ? $site_id : $siteId;
    $cache_set_id = cache_get_site_id();

    $is_switched = $siteId !== $cache_set_id;
    if (!$is_switched) {
        return cache_exist($key, $group);
    }

    cache_switch_to($siteId);
    $res = cache_exist($key, $group);
    cache_switch_to($cache_set_id);
    return $res;
}

/**
 * @param $key
 * @param string $group
 * @return bool
 */
function cache_exists($key, string $group = ObjectCache::DEFAULT_GROUP): bool
{
    return cache_exist($key, $group);
}

/**
 * @param $key
 * @param string $group
 * @param int|null $siteId
 * @return bool
 */
function cache_exists_current($key, string $group = ObjectCache::DEFAULT_GROUP, int $siteId = null): bool
{
    static $site_id = null;
    if ($site_id === null) {
        $site_id = get_current_site_id();
    }

    $siteId = $siteId === null ? $site_id : $siteId;
    $cache_set_id = cache_get_site_id();

    $is_switched = $siteId !== $cache_set_id;
    if (!$is_switched) {
        return cache_exists($key, $group);
    }

    cache_switch_to($siteId);
    $res = cache_exists($key, $group);
    cache_switch_to($cache_set_id);
    return $res;
}
