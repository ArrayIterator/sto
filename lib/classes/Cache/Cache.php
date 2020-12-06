<?php

namespace ArrayIterator\Cache;

use ArrayIterator\Cache\Adapter\CacheAdapterInterface;

/**
 * Class Cache
 * @package ArrayIterator\Cache
 */
class Cache
{
    const DEFAULT_ADAPTER = ObjectCache::class;

    /**
     * @var CacheAdapterInterface
     */
    protected $cacheAdapter;

    /**
     * Cache constructor.
     * @param string $defaultAdapter
     * @param int|null $siteId
     * @param mixed ...$args
     */
    public function __construct(
        $defaultAdapter = self::DEFAULT_ADAPTER,
        int $siteId = null,
        ...$args
    ) {
        if (is_object($defaultAdapter) && $defaultAdapter instanceof CacheAdapterInterface) {
            $this->setCacheAdapter($defaultAdapter);
        } elseif (!is_string($defaultAdapter)
            || !is_subclass_of($defaultAdapter, CacheAdapterInterface::class)
        ) {
            $defaultAdapter = self::DEFAULT_ADAPTER;
        }
        if (is_string($defaultAdapter)) {
            $this->setCacheAdapter(new $defaultAdapter($siteId, $this, $args));
        } elseif (is_int($siteId)) {
            $this->cacheAdapter->switchTo($siteId);
        }
    }

    /**
     * @return int
     */
    public function getSiteId(): int
    {
        return $this->cacheAdapter->getSiteId();
    }

    /**
     * @param int|null $siteId
     */
    public function setSiteId(int $siteId = null)
    {
        $this->cacheAdapter->switchTo($siteId);
    }

    public function setCacheAdapter(CacheAdapterInterface $adapter)
    {
        $this->cacheAdapter = $adapter;
        $this->cacheAdapter->setCache($this);
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        return call_user_func_array([$this->cacheAdapter, $name], $arguments);
    }

    public function __set($name, $value)
    {
        return $this->cacheAdapter->__set($name, $value);
    }

    public function __isset($name): bool
    {
        return $this->cacheAdapter->__isset($name);
    }

    public function __unset($name)
    {
        $this->cacheAdapter->__unset($name);
    }

    public function __get($name)
    {
        return $this->cacheAdapter->__get($name);
    }
}
