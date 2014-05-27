<?php

namespace BFI\Cache;

class Redis implements CacheInterface
{
    /**
     * @var \Redis
     */
    protected $_connection = null;

    /**
     * C'tor
     * Create Connection for BFI cache
     */
    function __construct()
    {
        $this->_connection = new \Redis();
        $this->_connection->connect('/var/run/redis/redis.sock');
        $this->_connection->setOption(\Redis::OPT_PREFIX, 'aQaCache_');
    }

    /**
     * D'tor
     * Save current db and disconnectu
     */
    function __destruct()
    {
        $this->_connection->save();
        $this->_connection->close();
    }

    /**
     * Set a cache value
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set($key, $value)
    {
        $this->_connection->set($key, $value);
    }

    /**
     * Get a value to a key
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        $val = $this->_connection->get($key);
        if ($val === false) {
            return $default;
        }
        return $val;
    }

    /**
     * Remove an entry from a cache
     * @param string $key
     * @return void
     */
    public function remove($key)
    {
        $this->_connection->delete($key);
    }

    /**
     * Remove all entries from the Cache
     * @return void
     */
    public function clear()
    {
        foreach ($this->_connection->getKeys('*') as $key) {
            $this->_connection->delete($key);
        }
    }
}
