<?php

use ArrayIterator\Helper\NormalizerData;
use ArrayIterator\Helper\Random;
use GuzzleHttp\Psr7\Stream;
use GuzzleHttp\Psr7\UploadedFile;


/**
 * @param mixed $arg
 * @return int
 */
function abs_int($arg) : int
{
    if (is_object($arg)) {
        if ($arg instanceof Countable) {
            return count($arg);
        } else {
            return 0;
        }
    }
    if (is_array($arg)) {
        $arg = !empty($arg) ? 1 : 0;
    }
    return (int) abs(intval($arg));
}

/**
 * Returning abs real value if numerical
 *
 * @param $arg
 * @return null|float|int
 */
function abs_r($arg)
{
    if ($arg === null || is_bool($arg)) {
        return null;
    }

    if (is_string($arg)) {
        $arg = trim($arg);
        if ($arg === '' || $arg === '+' || $arg === '-') {
            return null;
        }
        if (preg_match('#^[+\-]?[0-9]*\.[0-9]+$#', $arg)) {
            $arg = (float) $arg;
        } elseif (preg_match('#^[+\-]?[0-9]+$#', $arg)) {
            $arg = (int) ($arg);
        } else {
            return null;
        }
    } elseif (!is_int($arg) && !is_float($arg)) {
        return null;
    }

    return abs($arg);
}

/**
 * @param int|string|float $arg
 * @return float|int
 */
function abs_n($arg)
{
    // false or null === 0
    if ($arg === null || $arg === false || $arg === 0) {
        return 0;
    }

    // true is like `1`
    if ($arg === true || $arg === 1) {
        return 1;
    }
    if (is_string($arg)) {
        $arg = trim($arg);
        if ($arg === '' || $arg === '+' || $arg === '-') {
            return 0;
        }
        if (preg_match('#^[+\-]?[0-9]*\.[0-9]+#', $arg)) {
            $arg = (float) $arg;
        } elseif (preg_match('#^[+\-]?[0-9]+$#', $arg)) {
            $arg = (int) ($arg);
        } else {
            return 0;
        }
    } elseif (!is_int($arg) && !is_float($arg)) {
        return 0;
    }

    return abs($arg);
}

/**
 * @return array
 */
function request_environment() : array
{
    static $request;
    if (!$request) {
        $request = $_REQUEST;
    }
    return $request;
}

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
 * @param $found
 * @return mixed
 * @noinspection PhpMissingReturnTypeInspection
 */
function post($key = null, &$found = null)
{
    if ($key === null) {
        $found = true;
        return posts();
    }
    $found = false;
    if (!is_numeric($key) && !is_string($key)) {
        return null;
    }
    $found = array_key_exists($key, posts());
    return $found ? posts()[$key] : null;
}

/**
 * @param null $key
 * @param null $found
 * @return array|mixed|null
 * @noinspection PhpMissingReturnTypeInspection
 */
function post_param($key = null, &$found = null)
{
    return post($key, $found);
}

/**
 * @param null $key
 * @param null $found
 * @return array|mixed|null
 * @noinspection PhpMissingReturnTypeInspection
 */
function request_param($key = null, &$found = null)
{
    if ($key === null) {
        $found = true;
        return request_environment();
    }

    $found = false;
    if (!is_numeric($key) && !is_string($key)) {
        return null;
    }

    $found = array_key_exists($key, request_environment());
    return $found ? request_environment()[$key] : null;
}

/**
 * @param string|null $key
 * @param $found
 * @return mixed
 */
function query_param($key = null, &$found = null)
{
    if ($key === null) {
        $found = true;
        return server_request()->getQueryParams();
    }
    if (!is_numeric($key) && !is_string($key)) {
        return null;
    }
    $found = false;
    $found = array_key_exists($key, server_request()->getQueryParams());
    return $found ? server_request()->getQueryParams()[$key] : null;
}

/**
 * @param string $key
 * @param $value
 * @param bool $strict
 * @return bool
 */
function query_param_is(string $key, $value, bool $strict = true) : bool
{
    $param = query_param($key);
    return $strict ? $param === $value : $param == $value;
}


/**
 * @param string $key
 * @param $value
 * @param bool $strict
 * @return bool
 */
function request_param_is(string $key, $value, bool $strict = true) : bool
{
    $param = request_param($key);
    return $strict ? $param === $value : $param == $value;
}

/**
 * @param string $key
 * @param $value
 * @param bool $strict
 * @return bool
 */
function post_param_is(string $key, $value, bool $strict = true) : bool
{
    $param = post($key);
    return $strict ? $param === $value : $param == $value;
}

/**
 * @param string $key
 * @param array $value
 * @param bool $strict
 * @return bool
 */
function query_param_in(string $key, array $value, bool $strict = false) : bool
{
    $param = query_param($key);
    return in_array($param, $value, $strict);
}

/**
 * @param string $key
 * @param array $value
 * @param bool $strict
 * @return bool
 */
function post_param_in(string $key, array $value, bool $strict = false) : bool
{
    $param = post($key);
    return in_array($param, $value, $strict);
}

/**
 * @param string $key
 * @param array $value
 * @param bool $strict
 * @return bool
 */
function request_param_in(string $key, array $value, bool $strict = false) : bool
{
    $param = request_param($key);
    return in_array($param, $value, $strict);
}

/**
 * @param string $key
 * @return bool
 */
function has_query_param(string $key) : bool
{
    return array_key_exists($key, server_request()->getQueryParams());
}

/**
 * @param string $key
 * @return bool
 */
function has_request_param(string $key) : bool
{
    return array_key_exists($key, request_environment());
}

/**
 * @param string $key
 * @return bool
 */
function has_post_param(string $key) : bool
{
    return array_key_exists($key, posts());
}

/**
 * @param string $key
 * @param int $default
 * @return int
 */
function query_param_int(string $key, int $default = 0) : int
{
    $value = query_param($key, $found);
    if (!$found) {
        $value = $default;
    }
    return !is_numeric($value) ? $default : (int)$value;
}

function request_param_int(string $key, int $default = 0) : int
{
    $value = request_param($key, $found);
    if (!$found) {
        $value = $default;
    }
    return !is_numeric($value) ? $default : (int)$value;
}

function post_param_int(string $key, int $default = 0) : int
{
    $value = post($key, $found);
    if (!$found) {
        $value = $default;
    }
    return !is_numeric($value) ? $default : (int)$value;
}

/**
 * @param string $key
 * @param string $default
 * @param bool $trim
 * @return string
 */
function query_param_string(
    string $key,
    string $default = '',
    bool $trim = false
) : string {
    $value = query_param($key)??$default;
    if (is_string($value)) {
        return $trim ? trim($value) : $value;
    }
    if (is_numeric($value) || is_object($value) && method_exists($value, '__tostring')) {
        $value = (string) $value;
    }

    return !is_string($value) ? $default : ($trim ? trim($value) : $value);
}
/**
 * @param string $key
 * @param string $default
 * @param bool $trim
 * @return string
 */
function request_param_string(
    string $key,
    string $default = '',
    bool $trim = false
) : string {
    $value = request_param($key)??$default;
    if (is_string($value)) {
        return $trim ? trim($value) : $value;
    }
    if (is_numeric($value) || is_object($value) && method_exists($value, '__tostring')) {
        $value = (string) $value;
    }

    return !is_string($value) ? $default : ($trim ? trim($value) : $value);
}
/**
 * @param string $key
 * @param string $default
 * @param bool $trim
 * @return string
 */
function post_param_string(
    string $key,
    string $default = '',
    bool $trim = false
) : string {
    $value = post($key)??$default;
    if (is_string($value)) {
        return $trim ? trim($value) : $value;
    }
    if (is_numeric($value) || is_object($value) && method_exists($value, '__tostring')) {
        $value = (string) $value;
    }

    return !is_string($value) ? $default : ($trim ? trim($value) : $value);
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
            ? abs_int($lifetime)
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
 * @param array|int|null $siteIds
 * @return array
 */
function get_generate_site_ids($siteIds = null) : array
{
    $currentSiteId = get_current_site_id();
    if ($siteIds === null) {
        $siteIds = [];
        if (!is_super_admin()) {
            $siteIds = [$currentSiteId];
        }
    }
    $siteIds = (array) $siteIds;
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
function get_generate_max_search_result_limit(int $limit = null) : int
{
    if ($limit === null) {
        return MYSQL_DEFAULT_SEARCH_LIMIT;
    }

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

/**
 * @return int
 */
function get_max_upload_file_size() : int
{
    static $limit = null;
    if ($limit === null) {
        $limit = NormalizerData::getMaxUploadSize();
    }
    return $limit;
}

/**
 * @param int $length
 * @param string|null $char
 * @return string
 */
function random_char(int $length = 64, string $char = null) : string
{
    return Random::char($length, $char);
}

/**
 * @return string
 */
function get_current_random_string() : string
{
    static $random;
    if (!$random) {
        $random = random_char();
    }

    return $random;
}