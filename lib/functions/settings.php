<?php

use ArrayIterator\Model\Site;

/**
 * @return int
 */
function get_current_site_id(): int
{
    if (!enable_multisite()) {
        return application()->getDefaultSiteId();
    }

    $data = hook_apply('current_site_id', application()->getSite()->getModelSiteId());
    if (!is_numeric($data) || !is_int(abs($data))) {
        return 0;
    }

    return abs($data);
}

/**
 * @return Site|false
 */
function get_current_site()
{
    return get_site_by_id(get_current_user_id());
}

/**
 * @return Site[]
 */
function get_all_sites() : array
{
    $sites = cache_get('site_ids', 'sites_all', $found);
    if ($found && is_array($sites)) {
        return $sites;
    }
    cache_set('site_ids', [], 'sites_all');
    $stmt = site()->getAllStmt();
    if (!$stmt) {
        return [];
    }

    $data = [];
    while ($row = $stmt->fetch()) {
        $siteId = $row->getSiteId();
        $data[$siteId] = $row;
        cache_set($siteId, $data, 'sites');
    }
    cache_set('site_ids', $data, 'sites_all');

    return $data;
}

/**
 * @return bool
 */
function enable_multisite(): bool
{
    return (bool)hook_apply('enable_multisite', (bool)ENABLE_MULTISITE);
}

/**
 * @return array|false
 */
function get_current_site_meta()
{
    static $meta = [];
    $host = get_host();
    if (!isset($meta[$host])) {
        /**
         * @var Site $site
         */
        $site = site()->getHostOrAdditionalMatch($host);
        cache_set($site->getId(), $site, 'sites');
        $meta[$host] = $site
            ? $site->toArray()
            : false;
    }

    $data = $meta[$host];
    $enableMultisite = enable_multisite();
    if (!$enableMultisite) {
        $data = $meta[$host] ?: [
            'id' => 1,
            'name' => 'Default System',
            'host' => $host,
            'token' => null,
            'status' => 'active',
            'type' => 'host',
            'metadata' => null
        ];
    }

    return hook_apply('current_site_meta', $data, $host, $enableMultisite);
}

/**
 * @param int $siteId
 * @return Site|false
 */
function get_site_by_id(int $siteId)
{
    $data= cache_get($siteId, 'sites', $found);
    if (!$found && ($data instanceof Site || $data === false)) {
        return $data;
    }
    $site = site()->findById($siteId);
    cache_set($siteId, false, 'sites');
    if ($site) {
        $data = $site->fetch();
        cache_set($siteId, $data, 'sites');
        return $data;
    }
    return false;
}

/**
 * @return bool
 */
function site_is_global() : bool
{
    return get_current_site_id() === 1;
}

/**
 * @return int|false
 */
function determine_site_id()
{
    $meta = get_current_site_meta();
    return $meta ? ($meta['id'] ?? false) : false;
}

/**
 * @return string|false
 */
function get_site_host_type()
{
    $meta = get_current_site_meta();
    return $meta ? ($meta['type'] ?? false) : false;
}

/**
 * @return false|string
 */
function get_site_status()
{
    $meta = get_current_site_meta();
    $status = $meta ? ($meta['status'] ?? false) : false;
    if (!is_string($status)) {
        return false;
    }
    return trim(strtolower($status));
}

/**
 * @param string $status
 * @return false|string
 */
function site_status_is(string $status)
{
    $status = get_site_status();
    return $status === trim(strtolower($status));
}

/**
 * @return bool
 */
function site_status_is_active() : bool
{
    return site_status_is('active');
}

/**
 * @return bool
 */
function site_status_is_banned() : bool
{
    return site_status_is('banned');
}

/**
 * @return bool
 */
function site_status_is_deleted() : bool
{
    return site_status_is('deleted') || site_status_is('delete');
}

/**
 * @return bool
 */
function site_status_is_pending() : bool
{
    return site_status_is('pending');
}

