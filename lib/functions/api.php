<?php
/* -------------------------------------------------
 *                     JSON
 * ------------------------------------------------*/

/**
 * @param mixed $data
 * @param int $options
 * @return false|string
 */
function json_success($data, $options = JSON_UNESCAPED_SLASHES)
{
    if (!is_int($options)) {
        $options = JSON_UNESCAPED_SLASHES;
    }

    $options = (int)hook_apply('json_success_options', $options, $data);
    return json_encode(hook_apply('json_success', ['data' => $data]), $options);
}

/**
 * @param string|array $message
 * @param mixed $data
 * @param int $options
 * @return false|string
 */
function json_error($message, $data = null, $options = JSON_UNESCAPED_SLASHES)
{
    if (!is_int($options)) {
        $options = JSON_UNESCAPED_SLASHES;
    }

    $response = [
        'error' => [
            'message' => $message
        ]
    ];
    if (is_array($data)) {
        foreach ($data as $key => $message) {
            unset($data[$key]);
            $response['error'][$key] = $message;
        }
    } elseif ($data !== null) {
        $response['error']['data'] = $data;
    }

    $response = hook_apply('json_error', $response, $message, $data);
    $options = (int)hook_apply('json_error_options', $options, $response, $message, $data);
    return json_encode($response, $options);
}

/**
 * @param string $data
 * @param int $code
 */
function serve_json_data(string $data, int $code = 200)
{
    clean_buffer();
    hook_run('before_serve_json_data', $data, $code);
    set_content_type(
        hook_apply('serve_json_data_header', 'application/json; charset=utf-8', $data, $code),
        $code
    );


    render($data);
    hook_run('after_serve_json_data', $data, $code);
}

/**
 * @param int $code
 * @param mixed ...$data
 */
function json(int $code, ...$data)
{
    $data = $code < 300 ? json_success(...$data) : json_error(...$data);
    serve_json_data($data, $code);
    do_exit();
}

/**
 * @param $data
 * @param bool $pretty
 * @return false|string
 */
function json_ns($data, $pretty = false)
{
    $flags = JSON_UNESCAPED_SLASHES;
    if ($pretty === true) {
        $flags |= JSON_PRETTY_PRINT;
    }
    return json_encode($data, $flags);
}

/**
 * @return bool
 */
function is_route_api(): bool
{
    if (defined('ROUTE_API')) {
        return (bool)hook_apply('is_route_api', ROUTE_API, null);
    }
    $siteUri = preg_replace('#(https?://)[^/]+/?#', '/', get_current_url());
    $apiUri = preg_replace('#(https?://)[^/]+/?#', '/', rtrim(get_api_url(), '/'));
    $path = preg_quote($apiUri, '~');
    $bool = $apiUri === $siteUri
        || (bool)preg_match("~^{$path}(/$|/.*)?(?:\?.*)?$~", $siteUri);
    define('ROUTE_API', $bool);

    return hook_apply(
        'is_route_api',
        ROUTE_API
    );
}
