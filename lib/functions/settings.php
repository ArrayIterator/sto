<?php
function get_current_site_id() : int
{
    if (!enable_multisite()) {
        return application()->getDefaultSiteId();
    }

    return hook_apply('current_site_id', application()->getSite()->getModelSiteId());
}

function enable_multisite() : bool
{
    return (bool) hook_apply('enable_multisite', (bool) ENABLE_MULTISITE);
}

/**
 * @return array|false
 */
function current_site_meta()
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
        $data = $meta[$host]?:[
            'id' => 1,
            'name' => 'Default System',
            'host' => $host,
            'token' => null,
            'status' => 'active',
            'type'  => 'host',
            'metadata' => null
        ];
    }

    return hook_apply('current_site_meta', $data, $host, $enableMultisite);
}

/**
 * @return int|null
 */
function determine_site_id()
{
    $meta = current_site_meta();
    return $meta ? ($meta['id']??null) : null;
}

/**
 * @return string|null
 */
function get_site_host_type()
{
    $meta = current_site_meta();
    return $meta ? ($meta['type']??null) : null;
}
