<?php

namespace ArrayIterator\Helper;

/**
 * Class Uuid
 * @package ArrayIterator\Helper
 */
class UuidV4
{
    /**
     * Generate UUID
     *
     * @param int $case use CASE_LOWER|CASE_UPPER
     * @return string
     */
    public static function generate(int $case = CASE_LOWER): string
    {
        $uuid = sprintf(
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
        return $case === CASE_UPPER ? strtoupper($uuid) : $uuid;
    }

    /**
     * @param string $uuid
     * @param int|null $case CASE_LOWER|CASE_UPPER
     * @return bool
     */
    public static function validate(
        string $uuid,
        int $case = null
    ): bool {
        if (!$uuid) {
            return false;
        }
        $regex = '~^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$~';
        $case === null && $regex .= 'i';
        if ($case === CASE_LOWER) {
            $regex = strtolower($regex);
        }

        // 1070fd92-61ad-47bc-b749-613d6fd0b30d
        return (bool)preg_match(
            $regex,
            $uuid
        );
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return self::generate();
    }
}
