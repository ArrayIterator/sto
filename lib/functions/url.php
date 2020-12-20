<?php

use ArrayIterator\Helper\NormalizerData;
use ArrayIterator\Helper\Path;
use ArrayIterator\Info\Module;
use ArrayIterator\Info\Theme;
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
        $documentRoot = get_server_environment('DOCUMENT_ROOT') ?: ROOT_DIR;
        $documentRoot = un_slash_it(preg_replace('~[\\\/]+~', '/', $documentRoot));
        $rootPath = un_slash_it(preg_replace('~[\\\/]+~', '/', realpath(ROOT_DIR) ?: ROOT_DIR));
        $path = trim(substr($rootPath, strlen($documentRoot)), '/') . '/';
    }

    $uri = (string)get_uri()
        ->withPath($path)
        ->withQuery('');

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

/* -------------------------------------------------
 *                     ADMIN
 * ------------------------------------------------*/

/**
 * @param string $pathUri
 * @return string
 */
function get_admin_url(string $pathUri = ''): string
{
    static $adminUri;
    if (!$adminUri) {
        $adminUri = get_site_url(slash_it(get_admin_path()));
    }

    $originalPathUri = $pathUri;
    $pathUri = substr($pathUri, 0, 1) === '/'
        ? ltrim($pathUri, '/')
        : $pathUri;
    return hook_apply(
        'admin_url',
        sprintf('%s%s', $adminUri, $pathUri),
        $adminUri,
        $pathUri,
        $originalPathUri
    );
}

/**
 * @param array $query
 * @return string
 */
function get_admin_logout_redirect_url(array $query = []) : string
{
    $referer = get_referer();
    $loginUrl = get_admin_login_url();
    if (!$referer) {
        return $loginUrl;
    }

    static $uri;
    if (!$uri) {
        $exp = explode('?', $referer);
        $url = $exp[0];
        unset($exp[0]);
        $url_admin = preg_replace('#^[^:]+://#', '', get_admin_url());
        $url_2     = preg_replace('#^[^:]+://#', '', $url);
        $baseName = '';
        if (strpos($url_2, $url_admin) === 0) {
            $baseName = ltrim(substr($url_2, strlen($url_admin)), '/');
        }
        switch ($baseName) {
            case 'login.php':
            case 'logout.php':
            case 'init.php':
                $baseName = '';
                break;
        }
        if (!empty($exp)) {
            $baseName .= '?'.implode('?', $exp);
        }
        $uri = NormalizerData::addQueryArgs(['redirect' => $baseName], $loginUrl);
    }

    return NormalizerData::addQueryArgs($query, $uri);
}

function get_admin_login_redirect_url(array $query = []) : string
{
    static $uri;
    if ($uri) {
        $uri = hook_apply('admin_login_redirect_url', $uri);
        return NormalizerData::addQueryArgs($query, $uri);
    }

    $redirectUri = get_admin_base_name_file();
    $loginUrl    = get_admin_login_url();
    switch (get_admin_base_name_file()) {
        case 'logout.php':
        case 'init.php':
        case 'login.php':
            $redirectUri = null;
            break;
    }

    if (!$redirectUri || ! file_exists(get_admin_directory() .'/' . $redirectUri)) {
        $redirectUri = null;
    }

    $params = get_admin_param_redirect();
    $redirectParams = $params['redirect'] ?? null;
    if ($redirectParams !== null) {
        $redirectParams = urldecode($redirectParams);
        $exp    = explode('?', $redirectParams);
        $redirectUri = $redirectUri === null
        && isset($exp[0])
        && !in_array(trim($exp[0], '\\/'), ['logout.php', 'login.php', 'init.php'])
            ? $exp[0]
            : $redirectUri;
        unset($exp[0]);
        $redirectParams = implode('?', $exp);
        $params['redirect'] = $redirectParams;
    }
    unset($params['error'], $query['logout'], $query['redirect']);
    if ($redirectUri) {
        $query['redirect'] = $redirectUri.'?'.$redirectParams;
    }

    $query = array_merge($params, $query);
    $uri = hook_apply('admin_login_redirect_url', $uri);
    return NormalizerData::addQueryArgs($query, $loginUrl);
}

/**
 * @return array
 */
function get_admin_param_redirect() : array
{
    static $params;
    if (is_array($params)) {
        return $params;
    }

    $params   = query_param();
    $redirect = $_REQUEST['redirect']??($params['redirect']??null);
    $redirectParams = [];

    if (is_string($redirect)) {
        $redirectFile = explode('?', trim($redirect))[0];
        $redirectFile = trim($redirectFile, '\\/');
        $redirectParam = explode('?', trim($redirect))[1]??'';
        parse_str($redirectParam, $redirectParams);
        $redirectParams = $redirectParams??[];
        unset($redirectParams['redirect']);

        if (substr($redirectFile, -4) !== '.php') {
            $redirectFile = null;
        } else {
            switch ($redirectFile) {
                case 'logout.php':
                case 'init.php':
                case 'login.php':
                    $redirectFile = null;
                    break;
            }

            if (!$redirectFile || !file_exists(get_admin_directory() . '/' . $redirectFile)) {
                $redirectFile = null;
            }
        }
    } else {
        $redirectFile = null;
    }

    if (!empty($redirectParams)) {
        $redirect = $redirectFile.'?'.http_build_query($redirectParams);
    }

    if ($redirect) {
        $params['redirect'] = $redirect;
    }

    return $params;
}

/**
 * @param array $query
 * @return string
 */
function get_current_admin_login_url(array $query = []) : string
{
    static $uri;
    if ($uri) {
        $uri = hook_apply('current_admin_login_redirect_uri', $uri);
        return $uri;
    }

    $login_uri = get_admin_login_url();
    $params = get_admin_param_redirect();
    unset($params['error'], $params['logout']);

    $uri = NormalizerData::addQueryArgs($params, $login_uri);
    $uri = hook_apply('current_admin_login_redirect_uri', $uri);
    return NormalizerData::addQueryArgs($query, $uri);
}

/**
 * @param array $query
 * @return string
 */
function get_admin_redirect_url(array $query = []) : string
{
    $params = get_admin_param_redirect();
    $redirect = $params['redirect']??'';
    $redirect = urlencode($redirect);
    $params   = array_merge($params, $query);
    unset($params['redirect'], $params['logout']);

    return NormalizerData::addQueryArgs($params, get_admin_url($redirect));
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
function get_reset_password_url(): string
{
    return hook_apply(
        'forgot_url',
        get_site_url(get_reset_password_path())
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
 * @param string $uri
 * @return string
 */
function get_assets_url(string $uri = ''): string
{
    $assets = '/assets/';
    if ($uri && $uri[0] == '/') {
        $uri = substr($uri, 1);
    }
    return get_site_url($assets . $uri);
}

/**
 * @param string $uri
 * @return string
 */
function get_assets_vendor_url(string $uri = ''): string
{
    $assets = '/assets/vendor/';
    if ($uri && $uri[0] == '/') {
        $uri = substr($uri, 1);
    }
    return get_site_url($assets . $uri);
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

/* -------------------------------------------------
 *                     MODULES
 * ------------------------------------------------*/

/**
 * @param string|null $moduleFile
 * @return string
 */
function get_modules_uri($moduleFile = null): string
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
        $moduleFile = (string)$moduleFile;
    }

    if (!$moduleFile || trim($moduleFile) === '') {
        return $moduleUri;
    }

    $mod = normalize_path($moduleFile);
    $root = normalize_path(get_root_dir());
    if (strpos($mod, $moduleDir) === 0) {
        if (substr($mod, -4) === '.php') {
            $path = slash_it(substr(dirname($mod), strlen($moduleDir)));
        } else {
            $path = slash_it(substr($mod, strlen($moduleDir)));
        }
    } elseif (strpos($mod, $root) === 0) {
        $path = slash_it(substr(dirname($mod), strlen($root)));
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

/* -------------------------------------------------
 *                     THEMES
 * ------------------------------------------------*/

/**
 * @param string|null $themeFile
 * @return string
 */
function get_themes_uri($themeFile = null): string
{
    static $themeUri;
    static $themeDir;
    if (!$themeDir) {
        $themeDir = un_slash_it(normalize_path(get_themes_dir()));
    }
    if (!$themeUri) {
        $themeUri = slash_it(get_site_url(get_themes_path()));
    }

    if (!is_string($themeFile)) {
        if ($themeFile instanceof Theme) {
            $themeFile = basename($themeFile->getPath());
        }
        $themeFile = (string)$themeFile;
    }

    if (!$themeFile || trim($themeFile) === '') {
        return $themeUri;
    }

    $mod = normalize_path($themeFile);
    $root = normalize_path(get_root_dir());
    if (strpos($mod, $themeDir) === 0) {
        if (substr($mod, -4) === '.php') {
            $path = slash_it(substr(dirname($mod), strlen($themeDir)));
        } else {
            $path = slash_it(substr($mod, strlen($themeDir)));
        }
    } elseif (strpos($mod, $root) === 0) {
        $path = slash_it(substr(dirname($mod), strlen($root)));
    } elseif (preg_match('#^[/]*([^/]+)(\1\.php)?$#', $mod, $match)
        || preg_match('#^[/]*([^/]+)\.php$#', $mod, $match)
    ) {
        $path = slash_it($match[1]);
    } else {
        $path = strpos($mod, '.php') ? $mod : slash_it($mod);
    }

    return sprintf(
        '%s/%s',
        un_slash_it($themeUri),
        $path[0] === '/' ? substr($path, 1) : $path
    );
}

/**
 * @param string $path
 * @return string
 */
function get_theme_uri(string $path = ''): string
{
    $uri = un_slash_it(get_themes_uri(get_active_theme()));
    if ($path !== '') {
        $path = $path[0] === '/' ? substr($path, 1) : $path;
    }

    return sprintf('%s/%s', $uri, $path);
}

/**
 * @return string
 */
function get_css_uri(): string
{
    return get_theme_uri('theme.css');
}

/* -------------------------------------------------
 *                     API
 * ------------------------------------------------*/

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
