<?php

function sanitize_header_name(string $headerName)
{
    return str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', $headerName))));
}

function normalize_header_name(string $headerName)
{
    return hook_apply(
        'normalize_header_name',
        sanitize_header_name($headerName),
        $headerName
    );
}

/**
 * @return array
 */
function get_all_headers() : array
{
    static $headers = [];
    if (!empty($headers)) {
        return $headers;
    }
    $server = server_environment();
    $copy_server = [
        'CONTENT_TYPE'   => 'Content-Type',
        'CONTENT_LENGTH' => 'Content-Length',
        'CONTENT_MD5'    => 'Content-Md5',
    ];

    foreach ($server as $key => $value) {
        if (substr($key, 0, 5) === 'HTTP_') {
            $key = substr($key, 5);
            if (!isset($copy_server[$key]) || !isset($server[$key])) {
                $key = sanitize_header_name($key);
                $headers[$key] = $value;
            }
        } elseif (isset($copy_server[$key])) {
            $headers[$copy_server[$key]] = $value;
        }
    }

    if (!isset($headers['Authorization'])) {
        if (isset($server['REDIRECT_HTTP_AUTHORIZATION'])) {
            $headers['Authorization'] = $server['REDIRECT_HTTP_AUTHORIZATION'];
        } elseif (isset($server['PHP_AUTH_USER'])) {
            $basic_pass = isset($server['PHP_AUTH_PW']) ? $server['PHP_AUTH_PW'] : '';
            $headers['Authorization'] = 'Basic ' . base64_encode($server['PHP_AUTH_USER'] . ':' . $basic_pass);
        } elseif (isset($server['PHP_AUTH_DIGEST'])) {
            $headers['Authorization'] = $server['PHP_AUTH_DIGEST'];
        }
    }

    return $headers;
}

/**
 * @param string $name
 * @return string|null
 */
function get_header(string $name)
{
    $name = sanitize_header_name($name);
    return get_all_headers()[$name]??null;
}

/**
 * @param string $headerName
 * @param string $headerValue
 * @param int|null $httpCode
 */
function set_header(string $headerName, string $headerValue, int $httpCode = null)
{
    $headerName = normalize_header_name($headerName);
    if (hook_apply('remove_header', $headerName) === true) {
        remove_header($headerName, false);
    }

    $headerValue = trim($headerValue);
    $header = hook_apply(
        'set_header',
        sprintf('%s: %s', $headerName, $headerValue),
        $headerName,
        $headerValue,
        $httpCode
    );
    $replaceHeader = (bool) hook_apply('replace_header', true, $headerName, $headerValue, $httpCode);
    header($header, $replaceHeader, $httpCode);
}

/**
 * @param string $headerName
 * @param bool $normalize
 */
function remove_header(string $headerName, bool $normalize = true)
{
    $headerName = $normalize ? normalize_header_name($headerName) : $headerName;
    header_remove($headerName);
}

/**
 * @return false
 */
function is_json_request() : bool
{
    $header = get_header('Content-Type');
    if (!is_string($header)) {
        return false;
    }

    return preg_match('~^application/json~i', trim($header)) !== false;
}

function is_ajax_request() : bool
{
    $header = get_header('X-Requested-With');
    if (!is_string($header)) {
        return false;
    }
    return strtolower($header) === 'xmlhttprequest';
}

/**
 * @return string|null
 */
function get_referer()
{
    return get_header('Referer');
}
