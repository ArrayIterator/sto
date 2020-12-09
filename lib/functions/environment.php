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
    return server_environment()[$name]??(
        server_environment()[strtoupper($name)]??null
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
 * @return UploadedFile[]
 */
function files(): array
{
    return server_request()->getUploadedFiles();
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
 * @param bool $recreate
 * @return array
 */
function clean_buffer(bool $recreate = true): array
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

/**
 * Get Cookie Multi Domain
 *
 * @return string
 */
function cookie_multi_domain(): string
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

    return setcookie(...$arguments);
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
