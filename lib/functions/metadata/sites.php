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

    $current_site = get_current_site_meta();
    $id = $current_site ? $current_site['id'] : 0;
    // $data = hook_apply('current_site_id', $id);
    if (!is_numeric($id) || !is_int(abs($id))) {
        return 0;
    }

    return $id;
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
 * @return Site
 */
function get_global_site_meta() : Site
{
    static $site;
    if ($site) {
        return $site;
    }
    $site = site()->findById(1)->fetchClose();
    return $site;
}

/**
 * @return array|false
 */
function get_current_site_meta()
{
    static $meta = [];

    $host = get_host();
    $enableMultisite = enable_multisite();
    if (isset($meta[$host])) {
        $data = $meta[$host];
        if ($data) {
            $site = get_site_by_id($data['id']);
            $data = $site ? $site->toArray() : $data;
        }

        return hook_apply('current_site_meta', $data, $host, $enableMultisite);
    }

    if (!$enableMultisite) {
        $site = get_site_by_id(1);
        $site  = $site ? $site->toArray() : [
            'id' => 1,
            'name' => 'Default System',
            'host' => $host,
            'token' => null,
            'status' => 'active',
            'type' => 'host',
            'metadata' => null
        ];

        $meta[$site['host']] = $site;
        $meta[$host] = $site;
    } elseif (!isset($meta[$host])) {
        $global = get_global_site_meta();
        /**
         * @var Site $site
         */
        $site = site()->getHostOrAdditionalMatch($host);
        if (!$site && $global->get('host') === $host) {
            $site = $global;
        }

        // no save cached
        $meta[$host] = $site
            ? $site->toArray()
            : false;
    }

    $data = $meta[$host];

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
        $site->closeCursor();
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

