<?php

use GuzzleHttp\Psr7\Stream;
use GuzzleHttp\Psr7\UploadedFile;

/**
 * @return array
 */
function server_environment(): array
{
    return server_request()->getServerParams();
}

/**
 * @param string $name
 * @return mixed|null
 */
function get_server_environment(string $name)
{
    return server_environment()[$name] ?? (
            server_environment()[strtoupper($name)] ?? null
        );
}

/**
 * @return string
 */
function get_server_protocol(): string
{
    return 'HTTP/' . server_request()->getProtocolVersion();
}

/**
 * @return array
 */
function cookies(): array
{
    return server_request()->getCookieParams();
}

/**
 * @return array
 */
function posts(): array
{
    return server_request()->getParsedBody();
}

/**
 * @return UploadedFile[]|UploadedFile[][]
 */
function files(): array
{
    return server_request()->getUploadedFiles();
}

/**
 * @param string $name
 * @return false|UploadedFile|UploadedFile[]
 */
function get_uploaded_file(string $name)
{
    return files()[$name] ?? false;
}

/**
 * @return Stream rewind stream
 */
function body_stream(): Stream
{
    $body = server_request()->getBody();
    $body->isSeekable() && $body->rewind();
    return $body;
}

/**
 * @return string
 */
function body(): string
{
    return (string)body_stream();
}

/**
 * @param string|null $key
 * @return array|string|mixed|null
 */
function cookie($key = null)
{
    if ($key === null) {
        return cookies();
    }
    if (!is_numeric($key) && !is_string($key)) {
        return null;
    }
    return cookies()[$key] ?? null;
}

/**
 * @param string|null $key
 * @return mixed
 * @noinspection PhpMissingReturnTypeInspection
 */
function post($key = null)
{
    if ($key === null) {
        return posts();
    }

    if (!is_numeric($key) && !is_string($key)) {
        return null;
    }

    return posts()[$key] ?? null;
}

/**
 * @param string|null $key
 * @return mixed
 */
function query_param($key = null)
{
    if ($key === null) {
        return server_request()->getQueryParams();
    }
    if (!is_numeric($key) && !is_string($key)) {
        return null;
    }
    return server_request()->getQueryParams()[$key] ?? null;
}

/**
 * @param string $key
 * @return bool
 */
function has_query_param(string $key) : bool
{
    return isset(server_request()->getQueryParams()[$key]);
}

/**
 * @param string $key
 * @return int
 */
function query_param_int(string $key) : int
{
    $value = query_param($key);
    return !is_numeric($value) ? 0 : abs(intval($value));
}

/**
 * @param bool $recreate
 * @return array
 */
function clean_buffer(bool $recreate = true): array
{

    $c = 4;
    $data = [];
    $cleaned = false;
    while (ob_get_level() > 0 && $c-- > 0) {
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
 * @return string https or http
 */
function get_http_scheme(): string
{
    $scheme = get_uri()->getScheme();
    return hook_apply('http_scheme', $scheme);
}

/**
 * @return string
 */
function get_host(): string
{
    $host = get_uri()->getHost();
    return hook_apply('host', $host);
}

/**
 * @return string
 */
function request_uri(): string
{
    return hook_apply(
        'request_uri',
        get_server_environment('REQUEST_URI')
    );
}

/**
 * @return string
 */
function http_method(): string
{
    $method = server_request()->getMethod();
    return hook_apply('http_method', $method);
}

function is_method_post() : bool
{
    $method = http_method();
    $method = $method ? strtoupper($method) : $method;
    return $method === 'POST';
}

function is_method_get() : bool
{
    $method = http_method();
    $method = $method ? strtoupper($method) : $method;
    return $method === 'GET';
}

function is_method_put() : bool
{
    $method = http_method();
    $method = $method ? strtoupper($method) : $method;
    return $method === 'GET';
}

function is_method_delete() : bool
{
    $method = http_method();
    $method = $method ? strtoupper($method) : $method;
    return $method === 'GET';
}

/**
 * @return string
 */
function get_remote_address() : string
{
    return get_server_environment('REMOTE_ADDR')?:'';
}

/**
 * @return string
 */
function get_real_ip_address() : string
{
    static $ip = null;
    if ($ip !== null) {
        return $ip;
    }
    $server = server_environment();
    if ( !empty($server['HTTP_CLIENT_IP']) ) {
        // Check IP from internet.
        $ip = $server['HTTP_CLIENT_IP'];
    } elseif (!empty($server['HTTP_X_FORWARDED_FOR']) ) {
        // Check IP is passed from proxy.
        $ip = $server['HTTP_X_FORWARDED_FOR'];
    } else {
        // Get IP address from remote address.
        $ip = get_remote_address();
    }
    if ($ip && strpos($ip, ',') !== false) {
        $ip = array_filter(array_map('trim', explode(',', $ip)));
        $ip = $ip[0]??false;
    }

    return $ip?:'';
}

/**
 * Get Cookie Multi Domain
 *
 * @return string
 */
function cookie_multi_domain(): string
{
    $host = get_host();
    if (filter_var($host, FILTER_VALIDATE_DOMAIN)) {
        $host = \ArrayIterator\Helper\NormalizerData::splitCrossDomain($host);
    }

    return hook_apply('cookie_domain', $host, get_host());
}

/**
 * @return int
 */
function cookie_lifetime(): int
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
    $lifetime = (int)hook_apply('cookie_lifetime', $cLifetime);
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
): bool {

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
    $arguments = (array)hook_apply(
        'create_cookie',
        [
            'name' => $name,
            'value' => $value,
            'expire' => $expire,
            'path' => $path,
            'domain' => $domain,
            'secure' => $secure
        ]
    );

    return call_user_func_array('setcookie', $arguments);
}

/**
 * @param string $name
 */
function delete_cookie(string $name)
{
    $ex = time() - 3600 * 24 * 7;
    setcookie($name, '', $ex);
    create_cookie($name, '', $ex);
}

/**
 * @return bool
 */
function has_cookie_succeed() : bool
{
    static $cookie = null;
    if ($cookie === null) {
        $cookie = cookie(DEFAULT_COOKIE_SUCCEED_NAME) === 'true';
        delete_cookie(DEFAULT_COOKIE_SUCCEED_NAME);
    }

    return $cookie;
}

/**
 *
 */
function create_cookie_succeed()
{
    create_cookie(DEFAULT_COOKIE_SUCCEED_NAME, 'true');
}

/**
 * @param string $tag
 * @param callable $function_to_add
 * @param int $priority
 * @param int $accepted_args
 * @return bool
 */
function hook_add(
    string $tag,
    callable $function_to_add,
    int $priority = 10,
    int $accepted_args = 1
): bool {
    return hooks()->add($tag, $function_to_add, $priority, $accepted_args);
}

/**
 * @param string $tag
 * @param $value
 * @return mixed
 */
function hook_apply(string $tag, $value)
{
    return hooks()->apply(...func_get_args());
}

/**
 * @param string $tag
 * @param int|null $priority
 * @return bool
 */
function hook_remove_all(string $tag, int $priority = null): bool
{
    return hooks()->removeAll($tag, $priority);
}

/**
 * @param string $tag
 * @param callable $function_to_remove
 * @param int $priority
 * @return bool
 */
function hook_remove(string $tag, callable $function_to_remove, int $priority = 10): bool
{
    return hooks()->remove($tag, $function_to_remove, $priority);
}

/**
 * @param string $tag
 * @param false $function_to_check
 * @return bool
 */
function hook_exist(string $tag, $function_to_check = false): bool
{
    return hooks()->exist($tag, $function_to_check);
}

function hook_run(string $tag, ...$arg)
{
    hooks()->run($tag, ...$arg);
}

/**
 * @param string $tag
 * @param mixed ...$arg
 */
function hook_run_once(string $tag, ...$arg)
{
    hooks()->runOnceAndRemove($tag, ...$arg);
}

/**
 * @param string $tag
 * @return int
 */
function hook_has_run(string $tag): int
{
    return hooks()->hasRun($tag);
}

/**
 * @param string|null $filter
 * @return bool
 */
function hook_is_in_stack(string $filter = null): bool
{
    return hooks()->inStack($filter);
}

/**
 * @param string $tag
 * @param array $args
 */
function hook_run_ref_array(string $tag, array $args)
{
    hooks()->runRefArray($tag, $args);
}

/**
 * @return array
 */
function get_visibilities(): array
{
    $visibilities = [
        STATUS_PUBLIC,
        ROLE_STUDENT,
        ROLE_ADMIN,
        ROLE_TEACHER,
        ROLE_AUDITOR,
        ROLE_INVIGILATOR,
        ROLE_CONTRIBUTOR,
        ROLE_EDITOR,
    ];
    return hook_apply('visibilities', $visibilities);
}

/**
 * @return array
 */
function get_post_statuses(): array
{
    return [
        STATUS_DRAFT,
        STATUS_PUBLISHED,
        STATUS_TRASH,
        STATUS_PENDING
    ];
}

/**
 * @return array
 */
function get_question_statuses(): array
{
    return [
        STATUS_PUBLISHED,
        STATUS_DRAFT,
        STATUS_PENDING,
        STATUS_DELETED,
        STATUS_HIDDEN
    ];
}

/**
 * @return bool
 */
function is_login_page(): bool
{
    static $login_page = null;
    if ($login_page !== null) {
        return hook_apply(
            'is_login_page',
            $login_page
        );
    }

    $siteUri = preg_replace('#(https?://)[^/]+/?#', '/', get_current_url());
    $loginUri = preg_replace('#(https?://)[^/]+/?#', '/', rtrim(get_login_url(), '/'));
    $path = preg_quote($loginUri, '~');
    $login_page = $loginUri === $siteUri || (bool)preg_match("~^{$path}(/)?(?:\?.*)?$~", $siteUri);
    return hook_apply(
        'is_login_page',
        $login_page
    );
}

/**
 * @param int $code
 * @return string
 */
function get_error_string_by_code(int $code): string
{
    switch ($code) {
        case E_ERROR:
            return 'E_ERROR';
        case E_RECOVERABLE_ERROR:
            return 'E_RECOVERABLE_ERROR';
        case E_WARNING:
            return 'E_WARNING';
        case E_PARSE:
            return 'E_PARSE';
        case E_NOTICE:
            return 'E_NOTICE';
        case E_STRICT:
            return 'E_STRICT';
        case E_DEPRECATED:
            return 'E_DEPRECATED';
        case E_CORE_ERROR:
            return 'E_CORE_ERROR';
        case E_CORE_WARNING:
            return 'E_CORE_WARNING';
        case E_COMPILE_ERROR:
            return 'E_COMPILE_ERROR';
        case E_COMPILE_WARNING:
            return 'E_COMPILE_WARNING';
        case E_USER_ERROR:
            return 'E_USER_ERROR';
        case E_USER_WARNING:
            return 'E_USER_WARNING';
        case E_USER_NOTICE:
            return 'E_USER_NOTICE';
        case E_USER_DEPRECATED:
            return 'E_USER_DEPRECATED';
    }
    return 'E_UNKNOWN';
}

/**
 * Init
 */
function init()
{
    !hook_has_run('init') && hook_run('init');
}

/**
 * @param int $limit
 * @param int $offset
 * @param int $total_data
 * @param int $total_result
 * @return array
 */
function calculate_page_query(
    int $limit,
    int $offset,
    int $total_data,
    int $total_result
) : array {
    $total_page = 0;
    $current_page = null;
    $currentOffset = $offset + $total_result;
    $currentOffset = $currentOffset > 0 ? $currentOffset : 0;
    $limit = $limit > 0 ? $limit : 0;

    if ($total_data > 0) {
        if ($limit > 0) {
            $total_page = (int) ($limit >= $total_data ? 1 : ceil($total_data / $limit));
        }

        if ($total_page > 0 && $total_result > 0) {
            $current_page = (int) (ceil($currentOffset/$limit));
        }
    }

    $nextTotal = $total_data - $currentOffset;
    $nextLimit = null;
    $nextOffset = null;
    if ($nextTotal > 0) {
        $nextOffset = $offset+$total_result;
        $nextLimit = $nextTotal > $limit ? $limit : $nextTotal;
    }

    return [
        'total_page' => $total_page,
        'current_page' => $current_page,
        'next_limit' => $nextLimit,
        'next_offset' => $nextOffset,
        'next_total' => $nextTotal,
        'total_result' => $total_result,
        'total' => $total_data,
        'limit' => $limit,
        'offset' => $offset,
    ];
}

/**
 * @param array|null $siteIds
 * @return array
 */
function get_generate_site_ids(array $siteIds = null) : array
{
    $currentSiteId = get_current_site_id();
    if ($siteIds === null) {
        $siteIds = [$currentSiteId];
    }

    foreach ($siteIds as $key => $item) {
        if (!is_numeric($item) || !is_int(abs($item))) {
            unset($siteIds[$key]);
            continue;
        }

        $item = (int) $item;
        if ($item < 1) {
            continue;
        }
        $siteIds[$key] = $item;
    }

    $siteIds = empty($siteIds) ? (
        is_super_admin()
        ? []
        : [$currentSiteId]
    ) : $siteIds;

    return $siteIds;
}

/**
 * @param int $limit
 * @return int
 */
function get_generate_max_search_result_limit(int $limit) : int
{
    $limit = $limit <= 1 ? 1 : (
    $limit > MYSQL_MAX_SEARCH_LIMIT
        ? MYSQL_MAX_SEARCH_LIMIT
        : $limit
    );

    return $limit < 1 || $limit > MYSQL_MAX_RESULT_LIMIT
        ? MYSQL_MAX_RESULT_LIMIT
        : $limit;
}

/**
 * @param int $offset
 * @return int
 */
function get_generate_min_offset(int $offset) : int
{
    return $offset > 0 ? $offset : 0;
}

/**
 * @param null $date
 * @param int $addSecond
 * @return float[]|int[]
 */
function calculate_clock_delay($date = null, int $addSecond = 0) : array
{
    if ($date === null) {
        $time = (int) ((microtime(true) - MICRO_TIME_FLOAT));
        $datetime = timezone_convert()->getCurrentTime();
        if ($time > 0) {
            $datetime = $datetime->modify(sprintf('+%d seconds', $time));
        }
    } else {
        $date = $date??timezone_convert()->getTimezone();
        try {
            $datetime = timezone()->getDateTime($date);
        } catch (Exception $e) {
            $datetime = timezone_convert()->getCurrentTime();
        }
    }
    // add 1 second
    $addSecond > 0
        && $datetime = $datetime->modify(sprintf('+%d seconds', $addSecond));

    $diff    = strtotime($datetime->format('Y-m-d H:i:s')) - strtotime($datetime->format('Y-m-d'));
    $seconds = (60 * fmod($diff / 60, 1)) * -1;
    $minutes = (3600 * fmod($diff / 3600, 1)) * -1;
    $hours   = (43200 * fmod($diff / 43200, 1)) * -1;

    return [$hours, $minutes, $seconds];
}
