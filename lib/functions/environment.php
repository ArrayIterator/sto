<?php

use GuzzleHttp\Psr7\Stream;

/**
 * @return array
 */
function server_environment() : array
{
    static $server;
    if (!$server) {
        $server = $_SERVER;
    }
    return $server;
}

/**
 * @return array
 */
function &cookies()
{
    return $_COOKIE;
}

function &posts()
{
    return $_POST;
}
function body_stream()
{
    static $stream;
    if (!is_resource($stream)) {
        $stream = new Stream(fopen('php://input', 'r'));
    }
    return $stream;
}

function body()
{
    body_stream()->rewind();
    return (string) body_stream();
}

function cookie($key = null)
{
    if ($key === null) {
        return cookies();
    }
    if (!is_numeric($key) && !is_string($key)) {
        return null;
    }
    return cookies()[$key]??null;
}

function post($key = null)
{
    if ($key === null) {
        return posts();
    }

    if (!is_numeric($key) && !is_string($key)) {
        return null;
    }

    return posts()[$key]??null;
}

/**
 * @param bool $recreate
 * @return array
 */
function clean_buffer(bool $recreate = true) : array
{
    $c = 4;
    $data = [];
    $cleaned = false;
    while (ob_get_length() && $c-- > 0) {
        $data[] = ob_get_clean();
        if ($recreate && !$cleaned) {
            $cleaned = true;
        }
    }

    if ($cleaned || $recreate && ob_get_level() < 1) {
        ob_start();
    }

    return $data;
}

/**
 * Use No Buffer
 */
function no_buffer()
{
    $max = 5;
    while (ob_get_level() > 0 && $max-- > 0) {
        ob_end_clean();
    }
}

/**
 * @return string
 */
function get_admin_path() : string
{
    static $adminPath = null;
    if ($adminPath) {
        return $adminPath;
    }

    $adminPath = ADMIN_PATH;
    if (!is_string($adminPath)) {
        $adminPath = get_scanned_admin_path();
    }
    $adminPath = trim(preg_replace('~[\\\\/]+~', '/', ADMIN_PATH, '/'))
        ?:get_scanned_admin_path();

    return $adminPath;
}

/**
 * @return string
 */
function get_scanned_admin_path() : string
{
    static $admin;
    if (is_string($admin)) {
        return $admin;
    }
    $filesToCheck = [
        'index.php',
        'install.php',
        'modules.php',
        'profile.php',
        'students.php',
        'supervisors.php',
    ];

    $admin = 'admin';
    $found = false;
    if (is_dir(ROOT_PATH.'/'.$admin)) {
        $glob = array_map('basename', glob(ROOT_PATH . '/'.$admin.'/*.php'));
        $glob = array_diff($filesToCheck, $glob);
        $found = empty($glob);
    }
    if (!$found) {
        foreach (glob(ROOT_PATH.'/*', GLOB_ONLYDIR) as $dir) {
            $admin = basename($dir);
            $glob = array_map('basename', glob($dir.'/*.php'));
            $glob = array_diff($filesToCheck, $glob);
            $found = empty($glob);
        }
    }

    $admin = $found ? $admin : 'admin';
    return hook_apply('admin_path', $admin);
}

/**
 * @return string https or http
 */
function get_http_scheme() : string
{
    static $scheme;
    if ($scheme) {
        return $scheme;
    }

    $scheme = 'http';
    $server = server_environment();
    if (isset($server['HTTPS']) && $server['HTTPS'] == 'on'
        || isset($server['HTTP_X_FORWARDED_PROTO']) && $server['HTTP_X_FORWARDED_PROTO'] == 'https'
        || $server['SERVER_PORT'] == 443
    ) {
        $scheme = 'https';
    }

    return hook_apply('http_scheme', $scheme);
}

/**
 * @return string
 */
function get_host() : string
{
    static $host;
    if ($host) {
        return $host;
    }
    $server = server_environment();
    $host = $server['HTTP_HOST']??$server['SERVER_NAME'];
    $port = $server['HTTP_X_FORWARDED_PORT']??$server['SERVER_PORT']??null;
    if ($port && $port != 443 && $port != 80) {
        $host = sprintf('%s:%d', $host, $port);
    }

    return hook_apply('host', $host);
}

/**
 * @param string $pathUri
 * @return string
 */
function get_home_url(string $pathUri = '')
{
    static $path;
    $server = server_environment();
    $scheme     = get_http_scheme();
    $host       = get_host();
    if (!$path) {
        $documentRoot = rtrim(preg_replace('~[\\\/]+~', '/', $server['DOCUMENT_ROOT']), '/');
        $rootPath = rtrim(preg_replace('~[\\\/]+~', '/', realpath(ROOT_PATH) ?: ROOT_PATH), '/');
        $path = trim(substr($rootPath, strlen($documentRoot)), '/').'/';
    }
    $uri = sprintf('%s://%s%s', $scheme, $host, $path);
    $originalPath = $pathUri;
    $pathUri = (string) $pathUri;
    $pathUri = substr($pathUri, 0, 1) === '/'
        ? ltrim($pathUri, '/')
        : $pathUri;

    return hook_apply(
        'home_url',
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
function get_admin_url(string $pathUri = '')
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

/**
 * @return string
 */
function request_uri() : string
{
    return hook_apply(
        'request_uri',
        server_environment()['REQUEST_URI']
    );
}

/**
 * @return string
 */
function http_method()
{
    return hook_apply('http_method', server_environment()['REQUEST_METHOD']);
}

/**
 * @return string
 */
function get_current_url() : string
{
    $scheme     = get_http_scheme();
    $host       = get_host();
    return hook_apply(
        'current_url',
        sprintf('%s://%s%s', $scheme, $host, request_uri()),
        $scheme,
        $host,
        request_uri()
    );
}

/**
 * Get Cookie Multi Domain
 *
 * @return string
 */
function cookie_multi_domain() : string
{
    $host = get_host();
    if (filter_var($host, FILTER_VALIDATE_DOMAIN)) {
        $host = \ArrayIterator\Helper\Normalizer::splitCrossDomain($host);
    }
    return hook_apply('cookie_domain', $host, get_host());
}

/**
 * @return int
 */
function cookie_lifetime() : int
{
    static $cLifetime = null;
    $cookieLifetime = 0;
    if ($cLifetime === null) {
        $lifetime = defined('COOKIE_LIFETIME') ? COOKIE_LIFETIME : $cookieLifetime;
        $lifetime = is_int($lifetime) || is_numeric($lifetime)
            ? abs(intval($lifetime))
            : $cookieLifetime;
        $cLifetime = $lifetime;
    }
    $lifetime = (int) hook_apply('cookie_lifetime', $cLifetime);
    if ($lifetime < 360) {
        $lifetime = $cookieLifetime;
    }

    return $lifetime;
}

/**
 * @param string $name
 * @param string $value
 * @param int $expire
 * @param string $path
 * @param false|string|null $domain
 * @param false $secure
 * @return bool
 */
function create_cookie(
    string $name,
    string $value = '',
    int $expire = null,
    string $path = "/",
    string $domain = COOKIE_DOMAIN,
    bool $secure = false
) : bool {

    $expire = $expire === null
        ? cookie_lifetime()
        : $expire;

    $expire = hook_apply(
        'cookie_expire',
        $expire,
        $name,
        $value,
        $expire,
        $path,
        $domain,
        $secure
    );
    $arguments = (array) hook_apply(
        'setcookie',
        [
            'name' => $name,
            'value' => $value,
            'expire' => $expire,
            'path' => $path,
            'domain' => $domain,
            'secure' => $secure
        ]
    );

    return setcookie(...$arguments);
}

function delete_cookie($name)
{
    $ex = time() - 3600*24*7;
    setcookie($name, '', $ex);
    create_cookie($name, '', $ex);
}
