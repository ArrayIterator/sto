<?php

namespace ArrayIterator\Helper;

/**
 * Class Path
 * @package ArrayIterator\Helper
 */
class Path
{
    /**
     * @param string $path
     * @return bool
     */
    public static function isStream(string $path): bool
    {
        $scheme_separator = strpos($path, '://');
        if (false === $scheme_separator) {
            // $path isn't a stream.
            return false;
        }

        $stream = substr($path, 0, $scheme_separator);
        return in_array($stream, \stream_get_wrappers(), true);
    }

    /**
     * @param string $path
     * @return bool
     */
    public static function isAbsolute(string $path): bool
    {
        /*
         * Check to see if the path is a stream and check to see if its an actual
         * path or file as realpath() does not support stream wrappers.
         */
        if (self::isStream($path) && (is_dir($path) || is_file($path))) {
            return true;
        }

        /*
         * This is definitive if true but fails if $path does not exist or contains
         * a symbolic link.
         */
        if (realpath($path) === $path) {
            return true;
        }

        if (strlen($path) === 0 || '.' === $path[0]) {
            return false;
        }

        // Windows allows absolute paths like this.
        if (preg_match('#^[a-zA-Z]:\\\\#', $path)) {
            return true;
        }

        // A path starting with / or \ is absolute; anything else is relative.
        return ('/' === $path[0] || '\\' === $path[0]);
    }

    public static function join(string $base, string $path): string
    {
        if (self::isAbsolute($path)) {
            return $path;
        }

        return rtrim($base, '/') . '/' . ltrim($path, '/');
    }

    /**
     * @param string $path
     * @return string
     */
    public static function normalize(string $path): string
    {
        $wrapper = '';

        if (self::isStream($path)) {
            list($wrapper, $path) = explode('://', $path, 2);

            $wrapper .= '://';
        }

        // Standardise all paths to use '/'.
        $path = str_replace('\\', '/', $path);

        // Replace multiple slashes down to a singular,
        //allowing for network shares having two slashes.
        $path = preg_replace('|(?<=.)/+|', '/', $path);

        // Windows paths should uppercase the drive letter.
        if (':' === substr($path, 1, 1)) {
            $path = ucfirst($path);
        }

        return $wrapper . $path;
    }

    /**
     * @param string $dir
     * @return string
     */
    public static function normalizeDirectory(string $dir): string
    {
        return str_replace('/', DIRECTORY_SEPARATOR, self::normalize($dir));
    }

    /**
     * @param string $path
     * @return string
     */
    public static function slashIt(string $path): string
    {
        return self::unSlashIt($path) . '/';
    }

    /**
     * @param string $path
     * @return string
     */
    public static function unSlashIt(string $path): string
    {
        return rtrim($path, '\\/');
    }
}
