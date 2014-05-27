<?php

namespace BFI\Plugin;

use BFI\Exception;
use BFI\Db\Adapter;

class Db extends APlugin
{
    /**
     * Current connection
     * @var Adapter
     */
    private $_connection = null;

    /**
     * C'tor
     * @param string $type
     * @param string $name
     * @param string $host
     * @param string $user
     * @param string $pass
     * @throws \BFI\Exception
     */
    public function __construct($type, $name, $host = null, $user = null, $pass = null)
    {
        $dbConfig = array(
            'driver' => 'Pdo_' . $type
        );
        switch ($type) {
            case 'mysql':
                $dbConfig['dsn'] = sprintf('mysql:host=%s;dbname=%s;charset=UTF8', $host, $name);
                $dbConfig['username'] = $user;
                $dbConfig['password'] = $pass;
                break;
            case 'sqlite':
                $dbConfig['dsn'] = 'sqlite:' . $name;
                $dbConfig['username'] = null;
                $dbConfig['password'] = null;
                break;
             default:
                throw new Exception(sprintf('Database type "%s" not implemented yet.', $type));
                break;
        }
        $options = array(
            \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
        );
        $this->_connection = new Adapter($dbConfig['dsn'], $dbConfig['username'], $dbConfig['password'], $options);
    }

    /**
     * Get the current connection
     * @return Adapter
     */
    public function getConnection()
    {
        return $this->_connection;
    }
}