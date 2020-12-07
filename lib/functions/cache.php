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
 * @return bool
 */
function cache_exist($key, string $group = ObjectCache::DEFAULT_GROUP): bool
{
    return object_cache()->exist($key, $group);
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
