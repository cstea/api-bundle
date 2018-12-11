<?php declare(strict_types = 1);

namespace Cstea\ApiBundle\Traits;

use Psr\Cache\CacheItemPoolInterface;

trait CacheableAware
{
    /** @var CacheItemPoolInterface */
    private $cacheAdapter;

    /**
     * Setter Injector for Cache adapter
     *
     * @required
     * @param CacheItemPoolInterface $adapter Cache adapter.
     */
    public function setCacheAdapter(CacheItemPoolInterface $adapter): void
    {
        $this->cacheAdapter = $adapter;
    }

    /**
     * Simple cache setter.
     *
     * @param string $key   Cache key.
     * @param mixed  $value Value to cache.
     * @param int    $ttl   TTL.
     */
    protected function cacheSetValue(string $key, $value, int $ttl = 3600): void
    {
        if ($this->cacheAdapter !== null) {
            $cacheItem = $this->cacheAdapter->getItem($key);
            $cacheItem->set($value);
            $cacheItem->expiresAfter($ttl);
            $this->cacheAdapter->save($cacheItem);
        }
    }

    /**
     * Simple cache getter.
     *
     * @param string $key Cache key.
     * @return mixed|null
     */
    protected function cacheGetValue(string $key)
    {
        $value = null;
        if ($this->cacheAdapter !== null) {
            $cacheItem = $this->cacheAdapter->getItem($key);
            if ($cacheItem->isHit()) {
                $value = $cacheItem->get();
            }
        }
        
        return $value;
    }

    /**
     * Cache wrapper
     *
     * @param string   $key      Cache ID/Key.
     * @param callable $callable Function that returns data to store in cache.
     * @param int      $ttl      Cache duration in seconds.
     * @return mixed
     */
    protected function cache(string $key, callable $callable, int $ttl = 3600)
    {
        if ($this->cacheAdapter !== null) {
            $cacheItem = $this->cacheAdapter->getItem($key);
            if ($cacheItem->isHit()) {
                $data = $cacheItem->get();
                if (\method_exists($this, 'getLogger')) {
                    $this->getLogger()->notice('Hitting cached data', ['key' => $key]);
                    $this->getLogger()->debug('Fetching from cache', ['data' => $data]);
                }
                return $data;
            }
        }
        
        $result = $callable();
        
        if ($this->cacheAdapter !== null) {
            $cacheItem->set($result);
            $cacheItem->expiresAfter($ttl);
            if (\method_exists($this, 'getLogger')) {
                $this->getLogger()->notice('Saving cached item', ['key' => $key, 'ttl' => $ttl]);
                $this->getLogger()->debug('Item to cache', ['data' => $result]);
            }
            $this->cacheAdapter->save($cacheItem);
            
            $cacheKeys = $this->cacheAdapter->getItem('cstea.apibundle.cacheKeys');
            $keys = [];
            if ($cacheKeys->isHit()) {
                $keys = $cacheKeys->get();
            }
            if (!\in_array($key, $keys)) {
                $keys[] = $key;
                $cacheKeys->set($keys);
                $this->cacheAdapter->save($cacheKeys);
            }
        }
        
        return $result;
    }

    /**
     * Invalidate keys that share the same namespace.
     *
     * @param string $namespace Namespace to invalidate.
     */
    protected function invalidateCache(string $namespace): void
    {
        if ($this->cacheAdapter !== null) {
            $cacheKeys = $this->cacheAdapter->getItem('cstea.apibundle.cacheKeys');
            if ($cacheKeys->isHit()) {
                $cachedKeys = $cacheKeys->get();
                $deleteKeys = \array_filter($cachedKeys, static function ($key) use ($namespace) {
                    return \stripos($key, $namespace) === 0;
                });
                if (\method_exists($this, 'getLogger')) {
                    $this->getLogger()->notice('Invalidating cache items', ['keys' => $deleteKeys]);
                }
                $this->cacheAdapter->deleteItems($deleteKeys);
                $cacheKeys->set(\array_diff($cachedKeys, $deleteKeys));
                $this->cacheAdapter->save($cacheKeys);
            }
        }
    }

}