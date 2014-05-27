<?php

namespace BFI\Cache;

/**
 * Class CacheInterface
 * @package BFI\Cache
 */
interface CacheInterface
{
    /**
     * Set a cache value
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set($key, $value);

    /**
     * Get a value to a key
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null);

    /**
     * Remove an entry from a cache
     * @param string $key
     * @return void
     */
    public function remove($key);

    /**
     * Remove all entries from the Cache
     * @return void
     */
    public function clear();
}