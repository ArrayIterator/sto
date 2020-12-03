<?php
function json_success($data, $options = JSON_UNESCAPED_SLASHES)
{
    if (!is_int($options)) {
        $options = JSON_UNESCAPED_SLASHES;
    }

    return json_encode(hook_apply('json_success', ['data' => $data]), $options);
}

function json_error(string $message, $data = null, $options = JSON_UNESCAPED_SLASHES)
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
    $options = (int) hook_apply('json_options', $options, $response, $message, $data);
    return json_encode($response, $options);
}

function serve_json_data(string $data, int $code = 200)
{
    clean_buffer();
    hook_run('before_serve_json_data', $data, $code);
    set_header(
        'Content-Type',
        hook_apply('serve_json_header', 'application/json; charset=utf-8', $data, $code),
        $code
    );
    echo $data;
    hook_run('after_serve_json_data', $data, $code);
}

function json(int $code, ...$data)
{
    $data = $code < 300 ? json_success(...$data) : json_error(...$data);
    serve_json_data($data, $code);
    exit;
}

function get_route_api_path() : string
{
    $route = hook_apply('route_api_path', '/api');
    // only valid path
    $route = '/'.trim(preg_replace('~(\?.*)$~', '', $route), '/');
    return $route;
}

function is_route_api() : bool
{
    if (defined('ROUTE_API')) {
        return (bool) hook_apply('is_route_api', ROUTE_API, null);
    }

    $path = '/'.trim(get_route_api_path(), '/');
    $path = preg_quote($path, '~');
    return hook_apply(
        'is_route_api',
        (bool) preg_match("~^/{$path}(/.*)?$~", request_uri()),
        $path
    );
}
