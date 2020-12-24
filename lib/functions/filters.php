<?php

use ArrayIterator\Helper\NormalizerData;
use ArrayIterator\Helper\Path;
use ArrayIterator\Helper\StringFilter;

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
    return StringFilter::deepReplace($search, $subject);
}

/**
 * @param string $string
 * @param bool $slash_zero
 * @return string
 */
function replace_null_string(string $string, bool $slash_zero = true): string
{
    return StringFilter::replaceNullString($string, $slash_zero);
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

/**
 * @param string $message
 * @return string
 */
function esc_attr(string $message) : string
{
    return htmlspecialchars($message, ENT_QUOTES|ENT_COMPAT);
}

/**
 * @param string $message
 */
function esc_attr_e(string $message)
{
    echo htmlspecialchars($message, ENT_QUOTES|ENT_COMPAT);
}

/**
 * @param string $message
 * @return string
 */
function esc_html(string $message) : string
{
    $message = StringFilter::sanitizeInvalidUtf8FromString( $message );
    $message = esc_attr( $message );
    return $message;
}

function esc_html_e(string $message)
{
    echo esc_html($message);
}

/**
 * @param array $array
 * @return array
 */
function array_map_string_empty(array $array) : array
{
    foreach ($array as $key => &$v) {
        if (is_numeric($v) || is_object($v) && method_exists($v, '__tostring')) {
            $v = (string) $v;
        }

        if (!is_string($v)) {
            $v = '';
        }
    }

    return $array;
}

/**
 * @param mixed ...$args
 * @return string
 */
function add_query_args(...$args) : string
{
    return NormalizerData::addQueryArgs(...$args);
}

function remove_query_args($key, $query = false)
{
    return NormalizerData::removeQueryArg($key, $query);
}

/**
 * @param array|string $data
 * @param string|null $prefix
 * @param string|null $sep
 * @param string $key
 * @param bool $urlEncode
 * @return string
 */
function build_query(
    $data,
    string $prefix = null,
    string $sep = null,
    string $key = '',
    bool $urlEncode = true
) : string {
    return NormalizerData::buildQuery($prefix, $sep, $key, $urlEncode);
}

/**
 * @param string|array|object $class
 * @param string $fallback
 * @return string|string[]|null
 */
function normalize_html_class($class, string $fallback = '')
{
    if (is_string($class)) {
        return NormalizerData::normalizeHtmlClass($class, $fallback);
    }

    if ($class === false || is_bool($class)) {
        return normalize_html_class($fallback);
    }

    if (is_array($class) || is_object($class) && $class instanceof Traversable) {
        foreach ($class as $key => $v) {
            $class[$key] = normalize_html_class($v);
        }

        return $class;
    }

    if (is_numeric($class)) {
        return normalize_html_class((string) $class, $fallback);
    }

    if (is_object($class)) {
        foreach (get_object_vars($class) as $key => $i) {
            $class->$key = normalize_html_class($i);
        }
        return $class;
    }

    return normalize_html_class($fallback);
}

/**
 * @param string $data
 * @param int $start
 * @param int|null $end
 * @param string|null $append
 * @return false|mixed|string|string[]|null
 */
function substr_tag_strip(string $data, int $start = 0, int $end = null, string $append = null)
{
    $data = strip_tags($data);
    $data = preg_replace('#([\s])+#sm', '$1', $data);
    $newData = substr($data, $start, $end);
    if ($append && $data !== $newData) {
        $newData .= $append;
    }

    return $newData;
}
