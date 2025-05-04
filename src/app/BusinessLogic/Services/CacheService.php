<?php

namespace App\BusinessLogic\Services;

use Illuminate\Support\Facades\Cache;

class CacheService
{
    /**
     * Get an item from the cache, or store the default value.
     *
     * @param string $key
     * @param \DateTimeInterface|\DateInterval|int $ttl
     * @param \Closure $callback
     * @return mixed
     */
    public function remember(string $key, $ttl, \Closure $callback)
    {
        return Cache::remember($key, $ttl, $callback);
    }

    /**
     * Store an item in the cache.
     *
     * @param string $key
     * @param mixed $value
     * @param \DateTimeInterface|\DateInterval|int $ttl
     * @return bool
     */
    public function put(string $key, $value, $ttl): bool
    {
        return Cache::put($key, $value, $ttl);
    }

    /**
     * Retrieve an item from the cache.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return Cache::get($key, $default);
    }

    /**
     * Remove an item from the cache.
     *
     * @param string $key
     * @return bool
     */
    public function forget(string $key): bool
    {
        return Cache::forget($key);
    }

    /**
     * Clear the entire cache.
     *
     * @return bool
     */
    public function flush(): bool
    {
        return Cache::flush();
    }
}
