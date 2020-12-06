<?php

namespace ArrayIterator\Cache\Adapter;

use ArrayIterator\Cache\Cache;

/**
 * Interface CacheAdapterInterface
 * @package ArrayIterator\Cache\Adapter
 */
interface CacheAdapterInterface
{
    const DEFAULT_GROUP = 'default';

    public function __construct();

    /**
     * @return bool
     */
    public function connect() : bool;

    /**
     * @return int
     */
    public function getSiteId(): int;

    /**
     * @param Cache $cache
     * @return mixed
     */
    public function setCache(Cache $cache);

    /**
     * @return Cache|null
     */
    public function getCache();

    /**
     * @param bool $suspend
     * @return bool
     */
    public static function suspendCache(bool $suspend = null): bool;

    /**
     * @param string|int|float $key
     * @param mixed $data
     * @param string $group
     * @param int $expire
     * @return bool
     */
    public function add($key, $data, string $group = self::DEFAULT_GROUP, int $expire = 0): bool;

    /**
     * @param string|int|float $key
     * @param mixed $data
     * @param string $group
     * @param int $expire
     * @return bool
     */
    public function set($key, $data, string $group = self::DEFAULT_GROUP, int $expire = 0): bool;

    /**
     * @param string|int|float $key
     * @param $data
     * @param string $group
     * @param int $expire
     * @return bool
     */
    public function replace($key, $data, string $group = self::DEFAULT_GROUP, int $expire = 0): bool;

    /**
     * @param string|int|float $key
     * @param string $group
     * @param null $found
     * @return mixed
     */
    public function get($key, string $group = self::DEFAULT_GROUP, &$found = null);

    /**
     * @param array $keys
     * @param string $group
     * @return array
     */
    public function getMultiple(array $keys, string $group = self::DEFAULT_GROUP): array;

    /**
     * @param $key
     * @param string $group
     * @return bool
     */
    public function exist($key, string $group = self::DEFAULT_GROUP): bool;

    /**
     * Reset Cache Data
     *
     * @return void
     */
    public function reset();

    /**
     * @return array
     */
    public function getStats(): array;

    /**
     * @param int $siteId
     * @return static
     */
    public function switchTo(int $siteId): CacheAdapterInterface;

    /**
     * @param string|int|float $name
     * @return mixed
     */
    public function __get($name);

    /**
     * @param string|int|float $name
     * @param mixed $value
     * @return mixed
     */
    public function __set($name, $value);

    /**
     * @param string|int|float $name
     * @return bool
     */
    public function __isset($name): bool;

    /**
     * @param string|int|float $name
     */
    public function __unset($name);
}
