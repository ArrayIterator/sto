<?php
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
        $site = site()->getHostOrAdditionalMatch($host);
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
