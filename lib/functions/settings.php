<?php
function get_current_site_id() : int
{
    if (disable_multisite()) {
        return application()->getDefaultSiteId();
    }

    return hook_apply('current_site_id', application()->getSite()->getModelSiteId());
}

function disable_multisite() : bool
{
    return (bool) hook_apply('disable_multisite', (bool) DISABLE_MULTISITE);
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
    $disableMultisite = disable_multisite();
    if ($disableMultisite) {
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

    return hook_apply('current_site_meta', $data, $host, $disableMultisite);
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
