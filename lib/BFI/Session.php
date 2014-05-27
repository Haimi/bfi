<?php
/**
 * Created by JetBrains PhpStorm.
 * User: haimi
 * Date: 06.08.13
 * Time: 17:25
 * To change this template use File | Settings | File Templates.
 */

namespace BFI;


class Session
{
    /**
     * The Session instance
     * @var Session
     */
    static private $_instance;

    /**
     * The Session namespaces
     * @var array
     */
    private $_namespaces = array();

    /**
     * C'tor
     */
    private function __construct()
    {
        if (!session_id()) {
            session_start();
        }
        $this->_namespaces = & $_SESSION;
    }

    /**
     * Reset a value
     * @param string $name
     */
    static public function reset($name)
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        unset(self::$_instance->_namespaces[strval($name)]);
    }

    /**
     * Set a Session value
     * @param string $name
     * @param mixed $val
     */
    static public function set($name, $val)
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        self::$_instance->_namespaces[strval($name)] = $val;
    }

    /**
     * Get a Session value
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    static public function get($name, $default = null)
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        if (array_key_exists($name, self::$_instance->_namespaces)) {
            return self::$_instance->_namespaces[$name];
        }
        return $default;
    }

    /**
     * Test if value exists in Session
     * @param string $name
     * @return bool
     */
    static public function exists($name)
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        return array_key_exists($name, self::$_instance->_namespaces);
    }
}