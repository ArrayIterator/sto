<?php

namespace ArrayIterator\Cache\Adapter;

use ArrayIterator\Cache\AbstractCacheAdapter;
use ArrayIterator\Cache\Cache;
use Exception;
use Redis;

/**
 * Class RedisCache
 * @package ArrayIterator\Cache\Adapter
 */
class RedisCache extends AbstractCacheAdapter
{
    const DEFAULT_HOST = '127.0.0.1';
    const DEFAULT_PORT = 6379;
    protected $host = self::DEFAULT_HOST;
    protected $port = self::DEFAULT_PORT;

    /**
     * @var Redis
     */
    protected $redis = null;

    /**
     * @var string
     */
    protected $databaseName;

    /**
     * @var array|int[]
     */
    protected $options;

    /**
     * RedisCache constructor.
     * @param int|null $siteId
     * @param Cache|null $cache
     * @param string $host
     * @param int $port
     * @param string $database
     * @param array|int[] $options
     */
    public function __construct(
        int $siteId = null,
        Cache $cache = null,
        string $host = self::DEFAULT_HOST,
        int $port = self::DEFAULT_PORT,
        string $database = APP_NAME,
        array $options = [
            Redis::OPT_SERIALIZER => Redis::SERIALIZER_PHP
        ]
    ) {
        parent::__construct($siteId, $cache);
        $this->host = $host;
        $this->port = $port;
        $this->databaseName = $database;
        $this->options = $options;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function selectDatabase(string $name): bool
    {
        $this->databaseName = $name;
        if ($this->redis) {
            return $this->redis->select($this->databaseName);
        }
        return true;
    }

    public function connect(): bool
    {
        if ($this->redis === null) {
            $this->redis = false;
            try {
                $this->redis = new Redis();
                foreach ($this->options as $key => $item) {
                    $this->redis->setOption($key, $item);
                }
                if ($this->redis->connect($this->host, $this->port)) {
                    $this->redis->select($this->databaseName);
                    return true;
                }
            } catch (Exception $e) {
                $this->redis = false;
            }
        }

        return $this->redis
            ? $this->redis->isConnected()
            : false;
    }

    /**
     * @return Redis|false|null
     */
    public function getRedis()
    {
        return $this->redis;
    }

    public function getStats(): array
    {
        return [
            'hits' => (int)$this->cache_hits,
            'miss' => (int)$this->cache_misses,
            'groups' => []
        ];
    }

    public function getCacheHits(): int
    {
        if (!$this->connect()) {
            return parent::getCacheHits();
        }

        return (int)$this->redis->info('keyspace_hits');
    }

    public function getCacheMisses(): int
    {
        if (!$this->connect()) {
            return parent::getCacheMisses();
        }

        return (int)$this->redis->info('keyspace_misses');
    }

    public function add($key, $data, string $group = self::DEFAULT_GROUP, int $expire = 0): bool
    {
        if (self::suspendCache()) {
            return false;
        }

        if (!$this->connect()) {
            return parent::add($key, $data, $group, $expire);
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

    public function replace($key, $data, string $group = self::DEFAULT_GROUP, int $expire = 0): bool
    {
        if (!$this->connect()) {
            return parent::replace($key, $data, $group, $expire);
        }

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

    /***
     * @param float|int|string $key
     * @param mixed $data
     * @param string $group
     * @param int|null $expire
     * @return bool
     */
    public function set($key, $data, string $group = self::DEFAULT_GROUP, int $expire = 0): bool
    {
        if (!$this->connect()) {
            return parent::set($key, $data, $group, $expire);
        }

        if (empty($group)) {
            $group = static::DEFAULT_GROUP;
        }

        if (!isset($this->global_groups[$group])) {
            $key = $this->cachePrefix . $key;
        }

        if (is_object($data)) {
            $data = clone $data;
        }

        return $this->redis->set($this->createKey($group, $key), $data, $expire);
    }

    /**
     * @param float|int|string $key
     * @param string $group
     * @return bool
     */
    public function delete($key, string $group = self::DEFAULT_GROUP): bool
    {
        if (!$this->connect()) {
            return parent::delete($key, $group);
        }

        if (empty($group)) {
            $group = self::DEFAULT_GROUP;
        }

        if (!isset($this->global_groups[$group])) {
            $key = $this->cachePrefix . $key;
        }

        return $this->redis->del($this->createKey($key, $group)) > 0;
    }

    /**
     * @param float|int|string $key
     * @param string $group
     * @param $found
     * @return false|mixed|string
     */
    public function get($key, string $group = self::DEFAULT_GROUP, &$found = null)
    {
        if (!$this->connect()) {
            return parent::get($key, $group, $found);
        }

        if (empty($group)) {
            $group = static::DEFAULT_GROUP;
        }

        if (!isset($this->global_groups[$group])) {
            $key = $this->cachePrefix . $key;
        }

        if ($this->exist($key, $group)) {
            $found = true;
            $this->cache_hits += 1;
            return $this->redis->get($this->createKey($key, $group));
        }

        $found = false;
        $this->cache_misses += 1;
        return false;
    }

    protected function createKey(string $key, string $group): string
    {
        return sprintf('%s:%s', $group, sha1($key));
    }

    public function exist($key, string $group = self::DEFAULT_GROUP): bool
    {
        if (!$this->connect()) {
            return parent::exist($key, $group);
        }

        return $this->redis->exists($this->createKey($key, $group));
    }

    public function flush(): bool
    {
        return $this->redis->flushDB();
    }

    public function reset()
    {
        // pass
    }
}
