<?php

namespace BFI\Db;

/**
 * Class Adapter
 * @package BFI\Db
 */
class Adapter
{
    /**
     * The PDO Connection
     * @var \PDO
     */
    private $_pdo;

    /**
     * The default connection
     * @var \PDO
     */
    public static $defaultConnection = null;

    /**
     * C'tor
     * @param string $dsn
     * @param string $username
     * @param string $passwd
     * @param array $options
     */
    public function __construct($dsn, $username = null, $passwd = null, $options = null)
    {
        $this->_pdo = new \PDO($dsn, $username, $passwd, $options);
        $this->_pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        if (is_null(self::$defaultConnection)) {
            self::$defaultConnection = $this->_pdo;
        }
    }
}