<?php

/**
 * @param string $pathUri
 * @return string
 */
function get_admin_url(string $pathUri = '') : string
{
    $path = sprintf('/%s/', ADMIN_PATH);
    $originalPathUri = $pathUri;
    $pathUri = substr($pathUri, 0, 1) === '/'
        ? ltrim($pathUri, '/')
        : $pathUri;
    return hook_apply(
        'admin_url',
        get_home_url(sprintf('%s%s', $path, $pathUri)),
        $pathUri,
        $originalPathUri
    );
}

function get_login_url()
{
}