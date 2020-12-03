<?php
namespace ArrayIterator\Helper;

/**
 * Class Uuid
 * @package ArrayIterator\Helper
 */
class Uuid
{
    /**
     * Generate UUID
     *
     * @return string
     */
    public static function generate() : string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            // 16 bits for "time_mid"
            mt_rand(0, 0xffff),
            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand(0, 0x0fff) | 0x4000,
            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0x3fff) | 0x8000,
            // 48 bits for "node"
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }

    /**
     * @param string|null $uuid
     * @return bool
     */
    public static function validate(string $uuid) : bool
    {
        if (!$uuid) {
            return false;
        }
        // 1070fd92-61ad-47bc-b749-613d6fd0b30d
        return (bool) preg_match(
            '~^[a-f0-9]{8}[\-]([a-f0-9]{4}[\-]){3}[a-f0-9]{12}$~',
            $uuid
        );
    }

    /**
     * @return string
     */
    public function __toString() : string
    {
        return self::generate();
    }
}
