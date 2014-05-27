<?php

namespace BFI;

/**
 * Class Config
 * @package AQA
 */
class Config
{
    /**
     * The config values
     * @var array
     */
    protected static $_values = array();

    /**
     * Get a config value
     * @param string $name
     * @return mixed
     */
    public static function get($name)
    {
        $name = strval($name);
        if (array_key_exists($name, self::$_values)) {
            return self::$_values[$name];
        }
        return null;
    }

    /**
     * Set a config vaue
     * @param string $name
     * @param mixed $value
     */
    public static function set($name, $value)
    {
        self::$_values[strval($name)] = $value;
    }

    /**
     * @param string $file
     * @throws Exception
     */
    public static function loadXml($file)
    {
        if (! is_file($file)) {
            throw new Exception('could not load config file ' . $file);
        }
        $values = new \SimpleXMLElement(file_get_contents($file));
        foreach ($values as $key => $val) {
            self::set($key, $val);
        }
    }
}