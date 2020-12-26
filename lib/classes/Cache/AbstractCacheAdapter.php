<?php

namespace ArrayIterator\Cache;

// end here cause I don't want throw error
if (!defined('ROOT_DIR')) {
    return;
}

/**
 * Class AbstractCacheAdapter
 * @package ArrayIterator\Cache
 */
class AbstractCacheAdapter implements CacheAdapterInterface
{
    protected $cache;

    /**
     * @var array|mixed[]
     */
    protected $caches = [];

    /**
     * @var string
     */
    protected $cachePrefix = '';

    /**
     * @var int|null
     */
    protected $siteId;

    /**
     * The amount of times the cache data was already stored in the cache.
     *
     * @var int
     */
    public $cache_hits = 0;

    /**
     * Amount of times the cache did not have the request in cache.
     * @var int
     */
    public $cache_misses = 0;

    /**
     * List of global cache groups.
     * @var array
     */
    protected $global_groups = [];

    /**
     * AbstractCacheAdapter constructor.
     * @param int|null $siteId
     * @param Cache $cache
     */
    public function __construct(int $siteId = null, Cache $cache = null)
    {
        $this->cache = $cache;
        $this->siteId = (int)$siteId;
        $this->cachePrefix = $this->siteId ? $this->siteId . ':' : '';
    }

    public function connect(): bool
    {
        return true;
    }

    /**
     * @return int|null
     */
    public function getSiteId(): int
    {
        return $this->siteId;
    }

    /**
     * @return string
     */
    public function getCachePrefix(): string
    {
        return $this->cachePrefix;
    }

    /**
     * @return int
     */
    public function getCacheHits(): int
    {
        return $this->cache_hits;
    }

    /**
     * @return int
     */
    public function getCacheMisses(): int
    {
        return $this->cache_misses;
    }

    /**
     * @return array
     */
    public function getGlobalGroups(): array
    {
        return $this->global_groups;
    }

    /**
     * @param Cache $cache
     */
    public function setCache(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @return Cache|null
     * @noinspection PhpMissingReturnTypeInspection
     */
    public function getCache()
    {
        return $this->cache;
    }

    public static function suspendCache(bool $suspend = null): bool
    {
        static $_suspend = false;

        if (is_bool($suspend)) {
            $_suspend = $suspend;
        }

        return $_suspend;
    }

    public function add($key, $data, string $group = self::DEFAULT_GROUP, int $expire = 0): bool
    {
        if (self::suspendCache()) {
            return false;
        }

        if (empty($group)) {
            $group = static::DEFAULT_GROUP;
        }

        $id = $key;
        if (!isset($this->global_groups[$group])) {
            $id = $this->cachePrefix . $key;
        }

        if ($this->exist($id, $group)) {
            return false;
        }

        return $this->set($key, $data, $group, (int)$expire);
    }

    public function set($key, $data, string $group = self::DEFAULT_GROUP, int $expire = 0): bool
    {
        if (empty($group)) {
            $group = static::DEFAULT_GROUP;
        }

        if (!isset($this->global_groups[$group])) {
            $key = $this->cachePrefix . $key;
        }

        if (is_object($data)) {
            $data = clone $data;
        }

        $this->caches[$group][$key] = $data;
        return true;
    }

    /**
     * @param float|int|string $key
     * @param string $group
     * @return bool
     */
    public function delete($key, string $group = self::DEFAULT_GROUP): bool
    {
        if (empty($group)) {
            $group = self::DEFAULT_GROUP;
        }

        if (!isset($this->global_groups[$group])) {
            $key = $this->cachePrefix . $key;
        }

        if (!$this->exist($key, $group)) {
            return false;
        }

        unset($this->cache[$group][$key]);
        return true;
    }

    public function replace($key, $data, string $group = self::DEFAULT_GROUP, int $expire = 0): bool
    {
        if (empty($group)) {
            $group = static::DEFAULT_GROUP;
        }

        $id = $key;
        if (!isset($this->global_groups[$group])) {
            $id = $this->cachePrefix . $key;
        }

        if (!$this->exist($id, $group)) {
            return false;
        }

        return $this->set($key, $data, $group, (int)$expire);
    }

    /**
     * @param float|int|string $key
     * @param string $group
     * @param $found
     * @return bool|int
     */
    public function get($key, string $group = self::DEFAULT_GROUP, &$found = null)
    {
        if (empty($group)) {
            $group = static::DEFAULT_GROUP;
        }

        if (!isset($this->global_groups[$group])) {
            $key = $this->cachePrefix . $key;
        }

        if ($this->exist($key, $group)) {
            $found = true;
            $this->cache_hits += 1;
            if (is_object($this->caches[$group][$key])) {
                return clone $this->caches[$group][$key];
            } else {
                return $this->caches[$group][$key];
            }
        }

        $found = false;
        $this->cache_misses += 1;
        return false;
    }

    public function getMultiple(array $keys, string $group = self::DEFAULT_GROUP): array
    {
        $values = [];

        foreach ($keys as $key) {
            $values[$key] = $this->get($key, $group);
        }

        return $values;
    }

    public function exist($key, string $group = self::DEFAULT_GROUP): bool
    {
        return isset($this->caches[$group])
            && (isset($this->caches[$group][$key])
                || array_key_exists($key, $this->caches[$group]));
    }

    public function addGlobalGroups($groups)
    {
        $groups = (array)$groups;
        $groups = array_fill_keys($groups, true);
        $this->global_groups = array_merge($this->global_groups, $groups);
    }

    /**
     * @param string|int|float $key
     * @param int $offset
     * @param string $group
     * @return int|false
     */
    public function decrement($key, int $offset = 1, string $group = self::DEFAULT_GROUP)
    {
        if (empty($group)) {
            $group = static::DEFAULT_GROUP;
        }

        if (isset($this->global_groups[$group])) {
            $key = $this->cachePrefix . $key;
        }

        if (!$this->exist($key, $group)) {
            return false;
        }

        if (!is_numeric($this->cache[$group][$key])) {
            $this->cache[$group][$key] = 0;
        }

        $offset = (int)$offset;

        $this->cache[$group][$key] -= $offset;

        if ($this->cache[$group][$key] < 0) {
            $this->cache[$group][$key] = 0;
        }

        return $this->cache[$group][$key];
    }

    /**
     * @param $key
     * @param int $offset
     * @param string $group
     * @return false|int
     */
    public function increment($key, int $offset = 1, string $group = self::DEFAULT_GROUP)
    {
        if (empty($group)) {
            $group = static::DEFAULT_GROUP;
        }

        if (!isset($this->global_groups[$group])) {
            $key = $this->cachePrefix . $key;
        }

        if (!$this->exist($key, $group)) {
            return false;
        }

        if (!is_numeric($this->caches[$group][$key])) {
            $this->caches[$group][$key] = 0;
        }

        $offset = (int)$offset;
        $this->caches[$group][$key] += $offset;

        if ($this->caches[$group][$key] < 0) {
            $this->caches[$group][$key] = 0;
        }

        return $this->caches[$group][$key];
    }

    public function flush(): bool
    {
        $this->reset();
        return true;
    }

    public function reset()
    {
        // Clear out non-global caches since the blog ID has changed.
        foreach (array_keys($this->caches) as $group) {
            if (!isset($this->global_groups[$group])) {
                unset($this->caches[$group]);
            }
        }
    }

    public function getStats(): array
    {
        $groups = [];
        foreach ($this->caches as $group => $cache) {
            $group[$group] = strlen(serialize($cache));
        }
        return [
            'hits' => $this->cache_hits,
            'miss' => $this->cache_misses,
            'groups' => $groups
        ];
    }

    /**
     * @param int|null $siteId
     * @return static
     */
    public function switchTo(int $siteId = null): CacheAdapterInterface
    {
        $this->siteId = (int)$siteId;
        $this->cachePrefix = $this->siteId ? $this->siteId . ':' : '';
        return $this;
    }

    public function __get($name)
    {
        return $this->$name;
    }

    public function __set($name, $value)
    {
        return $this->$name = $value;
    }

    public function __isset($name): bool
    {
        return isset($this->$name);
    }

    public function __unset($name)
    {
        unset($this->$name);
    }
}
