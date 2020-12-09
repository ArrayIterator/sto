<?php

use ArrayIterator\Helper\Path;
use ArrayIterator\Info\Module;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\UriInterface;

/**
 * @param string $uri
 * @return Uri
 */
function create_uri_for(string $uri): Uri
{
    return new Uri($uri);
}

/**
 * @return string
 */
function get_current_url(): string
{
    return hook_apply(
        'current_url',
        (string)get_uri(),
        get_uri()
    );
}

/**
 * @param string $pathUri
 * @return string
 */
function get_site_url(string $pathUri = ''): string
{
    static $path;
    if (!$path) {
        $documentRoot = get_server_environment('DOCUMENT_ROOT')?:ROOT_DIR;
        $documentRoot = un_slash_it(preg_replace('~[\\\/]+~', '/', $documentRoot));
        $rootPath = un_slash_it(preg_replace('~[\\\/]+~', '/', realpath(ROOT_DIR) ?: ROOT_DIR));
        $path = trim(substr($rootPath, strlen($documentRoot)), '/') . '/';
    }

    $uri = (string)get_uri()->withPath($path);
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
 * @return UriInterface
 */
function get_site_uri(): UriInterface
{
    static $uri;
    if (!$uri) {
        $uri = create_uri_for(get_site_url());
    }
    return $uri;
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

/**
 * @return UriInterface
 */
function get_admin_uri(): UriInterface
{
    static $uri;
    if (!$uri) {
        $uri = create_uri_for(get_admin_url());
    }
    return $uri;
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
 * @param string|null $moduleFile
 * @return string
 */
function get_module_uri($moduleFile = null) : string
{
    static $moduleUri;
    static $moduleDir;
    if (!$moduleDir) {
        $moduleDir = un_slash_it(normalize_path(get_modules_dir()));
    }
    if (!$moduleUri) {
        $moduleUri = slash_it(get_site_url(get_modules_path()));
    }

    if (!is_string($moduleFile)) {
        if ($moduleFile instanceof Module) {
            $moduleFile = $moduleFile->getPath();
        }
        $moduleFile = (string) $moduleFile;
    }

    if ($moduleFile === null || trim($moduleFile) === '') {
        return $moduleUri;
    }

    $mod = normalize_path($moduleFile);
    if (strpos($mod, $moduleDir) === 0) {
        if (substr($mod, -4) === '.php') {
            $path = slash_it(substr(dirname($mod), strlen($moduleDir)));
        } else {
            $path = slash_it(substr($mod, strlen($moduleDir)));
        }
    } elseif (preg_match('#^[/]*([^/]+)(\1\.php)?$#', $mod, $match)
        || preg_match('#^[/]*([^/]+)\.php$#', $mod, $match)
    ) {
        $path = slash_it($match[1]);
    } else {
        $path = strpos($mod, '.php') ? $mod : slash_it($mod);
    }

    return sprintf(
        '%s/%s',
        un_slash_it($moduleUri),
        $path[0] === '/' ? substr($path, 1) : $path
    );
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

/**
 * @param string $location
 * @param int $status
 * @param string|null $x_redirect_by
 * @return bool
 */
function redirect(
    string $location,
    int $status = 302,
    string $x_redirect_by = 'Sto'
): bool {
    global $is_IIS;

    $location = hook_apply('redirect', $location, $status);
    $status = hook_apply('redirect_status', $status, $location);

    if (!is_string($location)) {
        return false;
    }

    if ($status < 300 || 399 < $status) {
        do_exit('HTTP redirect status code must be a redirection code, 3xx.', 255);
    }

    $location = sanitize_redirect($location);

    if (!$is_IIS && PHP_SAPI != 'cgi-fcgi') {
        // This causes problems on IIS and some FastCGI setups.
        set_status_header($status);
    }

    $x_redirect_by = hook_apply('x_redirect_by', $x_redirect_by, $status, $location);
    if (is_string($x_redirect_by) && trim($x_redirect_by)) {
        set_header("X-Redirect-By", trim($x_redirect_by));
    }

    set_header('Location', $location, $status);

    return true;
}
