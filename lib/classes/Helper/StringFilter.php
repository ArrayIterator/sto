<?php

namespace ArrayIterator\Helper;

use Exception;
use LogicException;
use Throwable;
use Traversable;

/**
 * Class StringFilter
 * @package ArrayIterator\Helper
 */
final class StringFilter
{
    /**
     * @var string[]
     */
    protected static $protocols = [
        'http',
        'https',
        'ftp',
        'ftps',
        'mailto',
        'news',
        'irc',
        'gopher',
        'nntp',
        'feed',
        'telnet',
        'mms',
        'rtsp',
        'sms',
        'svn',
        'tel',
        'fax',
        'xmpp',
        'webcal',
        'urn'
    ];

    /**
     * @param string $string
     * @param bool $slash_zero
     * @return string
     */
    public static function replaceNullString(string $string, bool $slash_zero = true): string
    {
        $string = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $string);
        if ($slash_zero) {
            $string = preg_replace('/\\\\+0+/', '', $string);
        }

        return $string;
    }

    /**
     * @param $search
     * @param string $subject
     * @return string|string[]
     */
    public static function deepReplace($search, string $subject)
    {
        $subject = (string)$subject;

        $count = 1;
        while ($count) {
            $subject = str_replace($search, '', $subject, $count);
        }
        return $subject;
    }

    /**
     * @param string $url
     * @param array|mixed|null $protocols
     * @param bool $display
     * @return string
     */
    public static function escapeUrl(
        string $url,
        $protocols = null,
        bool $display = true
    ): string {
        if ('' == trim($url)) {
            return $url;
        }

        $url = str_replace(' ', '%20', ltrim($url));
        $url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\[\]\\x80-\\xff]|i', '', $url);

        if ('' === $url) {
            return $url;
        }

        if (0 !== stripos($url, 'mailto:')) {
            $strip = array('%0d', '%0a', '%0D', '%0A');
            $url = self::deepReplace($strip, $url);
        }

        $url = str_replace(';//', '://', $url);
        /*
         * If the URL doesn't appear to contain a scheme, we presume
         * it needs http:// prepended (unless it's a relative link
         * starting with /, # or ?, or a PHP file).
         */
        if (strpos($url, ':') === false && !in_array($url[0], array('/', '#', '?')) &&
            !preg_match('/^[a-z0-9-]+?\.php/i', $url)) {
            $url = 'http://' . $url;
        }

        // Replace ampersands and single quotes only when displaying.
        if ($display) {
            $url = htmlentities($url);
            $url = str_replace('&amp;', '&#038;', $url);
            $url = str_replace("'", '&#039;', $url);
        }

        if ((false !== strpos($url, '[')) || (false !== strpos($url, ']'))) {

            $parsed = parse_url($url);
            $front = '';

            if (isset($parsed['scheme'])) {
                $front .= $parsed['scheme'] . '://';
            } elseif ('/' === $url[0]) {
                $front .= '//';
            }

            if (isset($parsed['user'])) {
                $front .= $parsed['user'];
            }

            if (isset($parsed['pass'])) {
                $front .= ':' . $parsed['pass'];
            }

            if (isset($parsed['user']) || isset($parsed['pass'])) {
                $front .= '@';
            }

            if (isset($parsed['host'])) {
                $front .= $parsed['host'];
            }

            if (isset($parsed['port'])) {
                $front .= ':' . $parsed['port'];
            }

            $end_dirty = str_replace($front, '', $url);
            $end_clean = str_replace(array('[', ']'), array('%5B', '%5D'), $end_dirty);
            $url = str_replace($end_dirty, $end_clean, $url);

        }
        if ('/' === $url[0]) {
            $good_protocol_url = $url;
        } else {
            if (!is_array($protocols)) {
                $protocols = self::$protocols;
            }

            preg_match('#^([^:]+):([/]+)?([^/].+)#', $url, $match);
            $protocol = $match[1] ?? null;
            $uri = $match[3] ?? null;
            if (!$protocol || !in_array($protocol, $protocols)) {
                return '';
            }

            $good_protocol_url = sprintf('%s://%s', $protocol, $uri);
            if (strtolower($good_protocol_url) != strtolower($url)) {
                return '';
            }
        }

        /**
         * Filters a string cleaned and escaped for output as a URL.
         *
         * @param string $good_protocol_url The cleaned URL to be returned.
         * @param string $original_url The URL prior to cleaning.
         * @param string $display If 'display', replace ampersands and single quotes only.
         */
        return $good_protocol_url;
    }

    /**
     * @param bool $reset
     */
    public static function mbStringBinarySafeEncoding(bool $reset = false)
    {
        static $encodings = [];
        static $overloaded = null;

        if (is_null($overloaded)) {
            $overloaded = function_exists('mb_internal_encoding')
                && (ini_get('mbstring.func_overload') & 2);
        }

        if (false === $overloaded) {
            return;
        }

        if (!$reset) {
            $encoding = mb_internal_encoding();
            array_push($encodings, $encoding);
            mb_internal_encoding('ISO-8859-1');
        }

        if ($reset && $encodings) {
            $encoding = array_pop($encodings);
            mb_internal_encoding($encoding);
        }
    }

    public static function resetMbStringEncoding()
    {
        self::mbStringBinarySafeEncoding(true);
    }

    /**
     * Check if data is (or contains) Binary
     *
     * @param string $str
     * @return bool
     */
    public static function isBinary(string $str): bool
    {
        return preg_match('~[^\x20-\x7E]~', $str) > 0;
    }

    /**
     * Check if data is Base 64
     *
     * @param string $str
     * @return bool
     */
    public static function isBase64(string $str): bool
    {
        return preg_match(
                '~^(?:[A-Za-z0-9+/]{4})*(?:[A-Za-z0-9+/]{2}==|[A-Za-z0-9+/]{3}=)?$~',
                $str
            ) > 0;
    }

    /**
     * check if data is https? url
     *
     * @param string $str
     * @return bool
     */
    public static function isHttpUrl(string $str): bool
    {
        return preg_match('~^https?://[^.]+\.(.+)$~i', $str) > 0;
    }

    /**
     * Sanitize non utf-8 string
     * @param string $data
     * @return string
     */
    public static function sanitizeUtf8Encode(string $data): string
    {
        static $utf8;
        if (!is_bool($utf8)) {
            $utf8 = function_exists('utf8_encode');
        }
        if (!$utf8) {
            return $data;
        }

        $regex = '/(
            [\xC0-\xC1] # Invalid UTF-8 Bytes
            | [\xF5-\xFF] # Invalid UTF-8 Bytes
            | \xE0[\x80-\x9F] # Overlong encoding of prior code point
            | \xF0[\x80-\x8F] # Overlong encoding of prior code point
            | [\xC2-\xDF](?![\x80-\xBF]) # Invalid UTF-8 Sequence Start
            | [\xE0-\xEF](?![\x80-\xBF]{2}) # Invalid UTF-8 Sequence Start
            | [\xF0-\xF4](?![\x80-\xBF]{3}) # Invalid UTF-8 Sequence Start
            | (?<=[\x00-\x7F\xF5-\xFF])[\x80-\xBF] # Invalid UTF-8 Sequence Middle
            | (?<![\xC2-\xDF]
                |[\xE0-\xEF]
                |[\xE0-\xEF][\x80-\xBF]
                |[\xF0-\xF4]
                |[\xF0-\xF4][\x80-\xBF]
                |[\xF0-\xF4][\x80-\xBF]{2}
                )[\x80-\xBF] # Overlong Sequence
            | (?<=[\xE0-\xEF])[\x80-\xBF](?![\x80-\xBF]) # Short 3 byte sequence
            | (?<=[\xF0-\xF4])[\x80-\xBF](?![\x80-\xBF]{2}) # Short 4 byte sequence
            | (?<=[\xF0-\xF4][\x80-\xBF])[\x80-\xBF](?![\x80-\xBF]) # Short 4 byte sequence (2)
        )/x';
        return preg_replace_callback($regex, function ($e) {
            return utf8_encode($e[1]);
        }, $data);
    }

    /**
     * Sanitize Result to UTF-8 , this is recommended to sanitize
     * that result from socket that invalid decode UTF8 values
     *
     * @param string $string
     *
     * @return string
     */
    public static function sanitizeInvalidUtf8FromString(string $string): string
    {
        static $iconv = null;
        if (!is_bool($iconv)) {
            $iconv = function_exists('iconv');
        }
        if (!$iconv) {
            return self::sanitizeUtf8Encode($string);
        }

        if (!function_exists('mb_strlen') || mb_strlen($string, 'UTF-8') !== strlen($string)) {
            $result = false;
            // try to un-serial
            try {
                // add temporary error handler
                set_error_handler(function ($errNo, $errStr) {
                    throw new Exception(
                        $errStr,
                        $errNo
                    );
                });
                /**
                 * use trim if possible
                 * Serialized value could not start & end with white space
                 */
                /** @noinspection PhpComposerExtensionStubsInspection */
                $result = iconv('windows-1250', 'UTF-8//IGNORE', $string);
            } catch (Exception $e) {
                // pass
            } finally {
                restore_error_handler();
            }
            if ($result !== false) {
                return self::sanitizeUtf8Encode($string);
            }
        }

        return self::sanitizeUtf8Encode($string);
    }

    /**
     * Sanitize Result to UTF-8 , this is recommended to sanitize
     * that result from socket that invalid decode UTF8 values
     *
     * @param mixed $data
     * @return mixed
     */
    public static function sanitizeInvalidUtf8($data)
    {
        if (is_string($data)) {
            return self::sanitizeInvalidUtf8FromString($data);
        }

        if (is_array($data) || $data instanceof Traversable) {
            foreach ($data as $key => $item) {
                $data[$key] = self::sanitizeInvalidUtf8($item);
            }
            return $data;
        }
        if (is_object($data)) {
            $realData = $data;
            try {
                foreach ($data as $key => $item) {
                    $data->$key = self::sanitizeInvalidUtf8($item);
                }
            } catch (Throwable $e) {
                $data = $realData;
            }
        }

        return $data;
    }

    /* --------------------------------------------------------------------------------*
     |                              Serialize Helper                                   |
     |                                                                                 |
     | Custom From WordPress Core wp-includes/functions.php                            |
     |---------------------------------------------------------------------------------|
     */

    /**
     * Check value to find if it was serialized.
     * If $data is not an string, then returned value will always be false.
     * Serialized data is always a string.
     *
     * @param mixed $data Value to check to see if was serialized.
     * @param bool $strict Optional. Whether to be strict about the end of the string. Defaults true.
     * @return bool  false if not serialized and true if it was.
     */
    public static function isSerialized($data, $strict = true): bool
    {
        /* if it isn't a string, it isn't serialized
         ------------------------------------------- */
        if (!is_string($data) || trim($data) == '') {
            return false;
        }

        $data = trim($data);
        // null && boolean
        if ('N;' == $data || $data == 'b:0;' || 'b:1;' == $data) {
            return true;
        }

        if (strlen($data) < 4 || ':' !== $data[1]) {
            return false;
        }

        if ($strict) {
            $last_char = substr($data, -1);
            if (';' !== $last_char && '}' !== $last_char) {
                return false;
            }
        } else {
            $semicolon = strpos($data, ';');
            $brace = strpos($data, '}');

            // Either ; or } must exist.
            if (false === $semicolon && false === $brace
                || false !== $semicolon && $semicolon < 3
                || false !== $brace && $brace < 4
            ) {
                return false;
            }
        }

        $token = $data[0];
        switch ($token) {
            /** @noinspection PhpMissingBreakStatementInspection */
            case 's':
                if ($strict) {
                    if ('"' !== substr($data, -2, 1)) {
                        return false;
                    }
                } elseif (false === strpos($data, '"')) {
                    return false;
                }
            // or else fall through
            case 'a':
            case 'O':
            case 'C':
                return (bool)preg_match(sprintf("/^%s:[0-9]+:/s", $token), $data);
            case 'i':
            case 'd':
                $end = $strict ? '$' : '';
                return (bool)preg_match(sprintf("/^%s:[0-9.E-]+;%s/", $token, $end), $data);
        }

        return false;
    }

    /**
     * Un-serialize value only if it was serialized.
     *
     * @param mixed $original Maybe un-serialized original, if is needed.
     * @return mixed  Un-serialized data can be any type.
     * @noinspection PhpMissingReturnTypeInspection
     */
    public static function unSerialize($original)
    {
        if (!is_string($original) || trim($original) == '') {
            return $original;
        }

        /**
         * Check if serialized
         * check with trim
         */
        if (self::isSerialized($original)) {
            // try to un-serial
            try {
                // add temporary error handler
                set_error_handler(function ($errNo, $errStr) {
                    throw new Exception(
                        $errStr,
                        $errNo
                    );
                });
                /**
                 * use trim if possible
                 * Serialized value could not start & end with white space
                 */
                $result = @unserialize(trim($original));
                if (trim($original) !== 'b:0;' && $result === false) {
                    return $original;
                }
                return $result;
            } catch (Exception $e) {
                // pass
            } finally {
                restore_error_handler();
            }
        }

        return $original;
    }

    /**
     * Serialize data, if needed. @param mixed $data Data that might be serialized.
     * @param bool $doubleSerialize Double Serialize if want to use returning real value of serialized
     *                                for database result
     * @return mixed A scalar data
     * @uses for ( un-compress serialize values )
     * This method to use safe as save data on database. Value that has been
     * Serialized will be double serialize to make sure data is stored as original
     *
     *
     */
    public static function serialize($data, $doubleSerialize = true)
    {
        if (is_array($data) || is_object($data)) {
            return @serialize($data);
        }

        /**
         * Double serialization is required for backward compatibility.
         * if @param bool $doubleSerialize is enabled
         */
        if ($doubleSerialize && self::isSerialized($data, false)) {
            return serialize($data);
        }

        return $data;
    }

    /**
     * @param string $email
     * @param bool $validateDNSSR
     * @return string|false
     */
    public static function filterEmailCommon(
        string $email,
        bool $validateDNSSR = false
    ) {
        $email = trim(strtolower($email));
        $explode = explode('@', $email);
        // validate email address & domain
        if (count($explode) <> 2
            // Domain must be contain Period and it will be real email
            || strpos($explode[1], '.') === false
            // could not use email with double period and hyphens
            || preg_match('~[.]{2,}|[\-_]{3,}~', $explode[0])
            // check validate email
            || !preg_match('~^[a-zA-Z0-9]+(?:[a-zA-Z0-9._\-]?[a-zA-Z0-9]+)?$~', $explode[0])
        ) {
            return false;
        }

        // filtering Email Address
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        // if validate DNS
        if ($validateDNSSR === true && !@checkdnsrr($explode[0], 'MX')) {
            return false;
        }

        return $email;
    }

    /**
     * Validate RegexP
     * @param string $regexP
     * @return bool|string
     */
    public static function regexP(string $regexP)
    {
        if (@preg_match($regexP, '') === false) {
            $last = error_get_last();
            error_clear_last();
            throw new LogicException(
                preg_replace('~^[^:]+:\s*~', '', $last['message']),
                E_USER_WARNING
            );
        }

        return $regexP;
    }

    public static function sha256(string $string): string
    {
        return hash('sha256', $string);
    }

    public static function sha512(string $string): string
    {
        return hash('sha512', $string);
    }
}
