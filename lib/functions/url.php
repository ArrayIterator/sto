<?php

/**
 * @return string
 */
function get_current_url(): string
{
    $scheme = get_http_scheme();
    $host = get_host();
    return hook_apply(
        'current_url',
        sprintf('%s://%s%s', $scheme, $host, request_uri()),
        $scheme,
        $host,
        request_uri()
    );
}

/**
 * @param string $pathUri
 * @return string
 */
function get_site_url(string $pathUri = ''): string
{
    static $path;
    $server = server_environment();
    $scheme = get_http_scheme();
    $host = get_host();
    if (!$path) {
        $documentRoot = rtrim(preg_replace('~[\\\/]+~', '/', $server['DOCUMENT_ROOT']), '/');
        $rootPath = rtrim(preg_replace('~[\\\/]+~', '/', realpath(ROOT_DIR) ?: ROOT_DIR), '/');
        $path = trim(substr($rootPath, strlen($documentRoot)), '/') . '/';
    }
    $uri = sprintf('%s://%s%s', $scheme, $host, $path);
    $originalPath = $pathUri;
    $pathUri = (string)$pathUri;
    $pathUri = substr($pathUri, 0, 1) === '/'
        ? ltrim($pathUri, '/')
        : $pathUri;

    return hook_apply(
        'site_url',
        sprintf('%s%s', $uri, $pathUri),
        $uri,
        $pathUri,
        $originalPath
    );
}


/**
 * @param string $pathUri
 * @return string
 */
function get_admin_url(string $pathUri = ''): string
{
    $path = sprintf('%s/', get_admin_path());
    $originalPathUri = $pathUri;
    $pathUri = substr($pathUri, 0, 1) === '/'
        ? ltrim($pathUri, '/')
        : $pathUri;
    $adminUri = get_site_url($path);
    return hook_apply(
        'admin_url',
        sprintf('%s%s', $adminUri, $pathUri),
        $adminUri,
        $pathUri,
        $originalPathUri
    );
}

/* -------------------------------------------------
 *                     ADMIN
 * ------------------------------------------------*/

/**
 * @return string
 */
function get_admin_login_url(): string
{
    return hook_apply(
        'admin_login_url',
        get_admin_url('login.php')
    );
}

/**
 * @return string
 */
function get_admin_modules_url(): string
{
    return hook_apply(
        'admin_modules_url',
        get_admin_url('modules.php')
    );
}

/**
 * @return string
 */
function get_admin_profile_url(): string
{
    return hook_apply(
        'admin_profile_url',
        get_admin_url('profile.php')
    );
}

/**
 * @return string
 */
function get_admin_quarantined_url(): string
{
    return hook_apply(
        'admin_quarantined_url',
        get_admin_url('quarantined.php')
    );
}

/**
 * @return string
 */
function get_admin_settings_url(): string
{
    return hook_apply(
        'admin_settings_url',
        get_admin_url('settings.php')
    );
}

/**
 * @return string
 */
function get_admin_students_url(): string
{
    return hook_apply(
        'admin_students_url',
        get_admin_url('students.php')
    );
}

/**
 * @return string
 */
function get_admin_supervisor_url(): string
{
    return hook_apply(
        'admin_supervisor_url',
        get_admin_url('supervisors.php')
    );
}

/**
 * @return string
 */
function get_admin_themes_url(): string
{
    return hook_apply(
        'admin_themes_url',
        get_admin_url('themes.php')
    );
}

/* -------------------------------------------------
 *                     PUBLIC
 * ------------------------------------------------*/
/**
 * @return string
 */
function get_login_url(): string
{
    return hook_apply(
        'login_url',
        get_site_url(get_login_path())
    );
}

/**
 * @return string
 */
function current_login_url(): string
{
    return is_admin_page()
        ? get_admin_login_url()
        : get_login_url();
}

/**
 * @param string $pathUri
 * @return string
 */
function get_api_url(string $pathUri = ''): string
{
    $path = sprintf('%s/', get_route_api_path());
    $originalPathUri = $pathUri;
    $pathUri = substr($pathUri, 0, 1) === '/'
        ? ltrim($pathUri, '/')
        : $pathUri;
    $apiUri = get_site_url($path);
    return hook_apply(
        'api_url',
        sprintf('%s%s', $apiUri, $pathUri),
        $apiUri,
        $pathUri,
        $originalPathUri
    );
}
