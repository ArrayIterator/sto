<?php

use ArrayIterator\Helper\Path;

/**
 * @param string $headerName
 * @return string|string[]
 */
function sanitize_header_name(string $headerName)
{
    $headerName = preg_replace('~[_\-\s]+~', '-', $headerName);
    return ucwords(strtolower($headerName), '-');
}

/**
 * @param string $headerName
 * @return mixed
 */
function normalize_header_name(string $headerName)
{
    return hook_apply(
        'normalize_header_name',
        sanitize_header_name($headerName),
        $headerName
    );
}

/**
 * @param mixed $search
 * @param string $subject
 * @return string|string[]
 */
function deep_replace($search, string $subject)
{
    $subject = (string)$subject;

    $count = 1;
    while ($count) {
        $subject = str_replace($search, '', $subject, $count);
    }

    return $subject;
}

/**
 * @param string $string
 * @param bool $slash_zero
 * @return string
 */
function replace_null_string(string $string, $slash_zero = true): string
{
    $string = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $string);
    if ($slash_zero) {
        $string = preg_replace('/\\\\+0+/', '', $string);
    }

    return $string;
}

/**
 * @param string $location
 * @return string
 */
function sanitize_redirect(string $location): string
{
    // Encode spaces.
    $location = str_replace(' ', '%20', $location);
    $regex = '/
		(
			(?: [\xC2-\xDF][\x80-\xBF]        # double-byte sequences   110xxxxx 10xxxxxx
			|   \xE0[\xA0-\xBF][\x80-\xBF]    # triple-byte sequences   1110xxxx 10xxxxxx * 2
			|   [\xE1-\xEC][\x80-\xBF]{2}
			|   \xED[\x80-\x9F][\x80-\xBF]
			|   [\xEE-\xEF][\x80-\xBF]{2}
			|   \xF0[\x90-\xBF][\x80-\xBF]{2} # four-byte sequences   11110xxx 10xxxxxx * 3
			|   [\xF1-\xF3][\x80-\xBF]{3}
			|   \xF4[\x80-\x8F][\x80-\xBF]{2}
		){1,40}                              # ...one or more times
		)/x';
    $location = preg_replace_callback($regex, function ($matches) {
        return urlencode($matches[0]);
    }, $location);
    $location = preg_replace('|[^a-z0-9-~+_.?#=&;,/:%!*\[\]()@]|i', '', $location);
    $location = replace_null_string($location);

    // Remove %0D and %0A from location.
    $strip = ['%0d', '%0a', '%0D', '%0A'];
    return deep_replace($strip, $location);
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

/**
 * @param string $path
 * @return string
 */
function normalize_path(string $path): string
{
    return Path::normalize($path);
}

/**
 * @param string $dir
 * @return string
 */
function normalize_directory(string $dir): string
{
    return Path::normalizeDirectory($dir);
}

/**
 * @param string $path
 * @return string
 */
function slash_it(string $path): string
{
    return Path::slashIt($path);
}

/**
 * @param string $path
 * @return string
 */
function un_slash_it(string $path): string
{
    return Path::unSlashIt($path);
}
