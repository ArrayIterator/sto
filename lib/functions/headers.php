<?php
/**
 * @return string[]
 */
function get_status_header_list(): array
{
    static $headers;
    if ($headers) {
        return $headers;
    }
    $headers = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        103 => 'Early Hints',

        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        226 => 'IM Used',

        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Reserved',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',

        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        421 => 'Misdirected Request',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        451 => 'Unavailable For Legal Reasons',

        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        510 => 'Not Extended',
        511 => 'Network Authentication Required',
    ];

    return $headers;
}

/**
 * @param int $code
 * @return string
 */
function get_status_header(int $code): string
{
    $code = abs(intval($code));
    return get_status_header_list()[$code] ?? '';
}

/**
 * @return array
 */
function get_all_headers(): array
{
    static $headers = [];
    if (!empty($headers)) {
        return $headers;
    }
    $server = server_environment();
    $copy_server = [
        'CONTENT_TYPE' => 'Content-Type',
        'CONTENT_LENGTH' => 'Content-Length',
        'CONTENT_MD5' => 'Content-Md5',
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
    return get_all_headers()[$name] ?? null;
}

/**
 * @param string $headerName
 * @param string $headerValue
 * @param int|null $httpCode
 * @param bool $removeHeader
 */
function set_header(
    string $headerName,
    string $headerValue,
    int $httpCode = null,
    bool $removeHeader = true
) {
    $headerName = normalize_header_name($headerName);
    if (hook_apply('remove_header', $removeHeader, $headerName) === true) {
        remove_header($headerName, false);
    }

    $headerValue = trim($headerValue);
    $header = hook_apply(
        'set_header',
        trim(ltrim(sprintf('%s: %s', $headerName, $headerValue), ': ')),
        $headerName,
        $headerValue,
        $httpCode
    );
    $replaceHeader = (bool)hook_apply('replace_header', true, $headerName, $headerValue, $httpCode);
    $args = [$header, $replaceHeader];
    if (is_int($httpCode)) {
        $args[] = $httpCode;
    }
    header(...$args);
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
 * @param int $code
 * @param string|null $description
 */
function set_status_header(int $code, string $description = null)
{
    $description = !$description || trim($description) === ''
        ? get_status_header($code)
        : trim($description);

    if ($description === '') {
        return;
    }

    $protocol = get_server_protocol();
    $status_header = sprintf('%s %d %s', $protocol, $code, $description);
    $status_header = hook_apply(
        'status_header',
        $status_header,
        $code,
        $description,
        $protocol
    );

    if (!headers_sent()) {
        header($status_header, true, $code);
    }
}

/**
 * @return bool
 */
function is_json_request(): bool
{
    $header = get_header('Content-Type');
    if (!is_string($header)) {
        return false;
    }

    return preg_match('~^application/json~i', trim($header)) !== false;
}

/**
 * @return bool
 */
function is_ajax_request(): bool
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

/**
 * @return string|null
 */
function get_user_agent()
{
    return get_header('User-Agent');
}

/**
 * Set Nocache Header
 */
function set_no_cache_header()
{
    set_header('Cache-Control', 'no-cache, no-store, must-revalidate');
    set_header('Pragma', 'no-cache');
    set_header('Expires', '0');
}

/**
 * Set Headers To Robots Index
 */
function set_no_index_header()
{
    set_header('X-Robots-Tag', 'noindex, nofollow, noarchive, noodp, noydir');
}