<?php
namespace ArrayIterator\Helper;

use RuntimeException;
use GuzzleHttp\Psr7\Stream;

/**
 * Class MimeTypes
 * @package ArrayIterator\Helper
 *
 * @link https://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types
 * The first array values each on mime types is a main mime type for extension
 */
final class MimeTypes
{
    /**
     * @var array[]
     */
    protected static $extensionMimeTypes;

    /**
     * Get Mime types List
     *
     * @return string[][]|array[]
     */
    public static function getExtensionMimeTypes() : array
    {
        if (self::$extensionMimeTypes === null) {
            $jsonFile = __DIR__ .'/MimeTypes/mime.types.json';
            if (!file_exists($jsonFile)) {
                throw new RuntimeException(
                    'File mime.types.json is no exists',
                    E_COMPILE_ERROR
                );
            }
            $stream = new Stream(fopen($jsonFile, 'r'));
            self::$extensionMimeTypes = json_decode(
                (string) $stream,
                true
            );
            $stream->close();
            unset($stream);
        }
        return self::$extensionMimeTypes;
    }

    /**
     * Get Mime types list from extension
     *
     * @param string $extension
     * @return string[]|false
     */
    public static function fromMimeType(string $extension)
    {
        $extension = strtolower($extension);
        return self::getExtensionMimeTypes()[$extension]?? null;
    }

    /**
     * Get List of extension from Mime Type
     *
     * @param string $extension mime type string
     * @return string[]|false
     */
    public static function fromExtension(string $extension)
    {
        $extension = trim(strtolower($extension));
        if ($extension === '') {
            return null;
        }
        // just make sure for explode
        $extension = explode('.', $extension);
        $extension = end($extension);
        // invalid extension, extensions only valid a-z0-9
        if (preg_match('~[^a-z0-9]~', $extension)) {
            return null;
        }
        $mimes = [];
        $first = [];
        foreach (self::getExtensionMimeTypes() as $key => $value) {
            if (in_array($extension, $value)) {
                if (reset($value) === $extension) {
                    $first[] = $key;
                }
                $mimes[] = $key;
            }
        }

        if (!empty($first)) {
            $mimes = array_merge($first, $mimes);
            unset($first);
            $mimes = array_values(array_unique($mimes));
        }

        return !empty($mimes) ? $mimes : false;
    }

    /**
     * @param string $extension
     * @return string|false
     */
    public static function mime(string $extension)
    {
        return self::fromExtension($extension)[0]??false;
    }
    /**
     * @param string $mimeType
     * @return string|false
     */
    public static function extension(string $mimeType)
    {
        return self::fromMimeType($mimeType)[0]??false;
    }

    /**
     * Clear Memories
     */
    public static function clear()
    {
        self::$extensionMimeTypes = null;
    }

    /**
     * MimeTypes constructor.
     */
    public function __construct()
    {
        self::clear();
    }
}
