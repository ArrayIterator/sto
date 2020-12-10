<?php

namespace ArrayIterator\Helper;

use Throwable;

/**
 * Class Random
 * @package ArrayIterator\Helper
 */
class Random
{
    /**
     * @param int $length
     * @param string|null $char
     * @return string
     * @noinspection PhpUnused
     */
    public static function char(int $length = 64, string $char = null): string
    {
        if ($length < 1) {
            return '';
        }

        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $chars .= '~`! @#$%^&*()_-+={[}]|\:;"\'<,>.?/';
        $chars = $char ?: $chars;
        $charactersLength = strlen($chars);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $chars[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * @param int $bytes
     * @return string
     */
    public static function bytes(int $bytes): string
    {
        static $pseudo = null;

        if ($bytes < 1) {
            return '';
        }
        try {
            return random_bytes($bytes);
        } catch (Throwable $e) {
            if (!is_bool($pseudo)) {
                $pseudo = function_exists('openssl_random_pseudo_bytes');
            }
            try {
                if ($pseudo) {
                    return openssl_random_pseudo_bytes($bytes);
                }
            } catch (Throwable $e) {
                // pass
            }
            $random = '';
            while (strlen($random) < $bytes) {
                $random .= chr(mt_rand(0, 255));
            }
            return $random;
        }
    }
}
