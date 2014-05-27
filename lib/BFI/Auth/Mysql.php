<?php

namespace BFI\Auth;

use BFI\Auth;
use BFI\Session;

/**
 * Class Mysql
 * @package BFI\Auth
 */
class Mysql implements Adapter
{
    /**
     * The hash algorithm
     * @var string
     */
    protected $_hashAlgorithm = 'md5';

    /**
     * The authentication table Object
     * @var ITable
     */
    protected $_table = null;

    /**
     * C'tor
     * @param ITable $table
     */
    public function __construct(ITable $table)
    {
        $this->_table = $table;
    }

    /**
     * Authenticate the User
     * @param string $user
     * @param string $password
     * @return mixed
     */
    public function authenticate($user, $password)
    {
        $hash = $this->hash($user, $password);
        return $this->_table->login($user, $hash);
    }

    /**
     * Log out user
     */
    public function logout()
    {
        Session::reset('Auth');
        return true;
    }

    /**
     * Hash the credentials
     * Secure Hash algorithm: H( H( <SALT> ':' <user> ) ':' H( <SALT> ':' <password> ) )
     * @param string $user
     * @param string $password
     * @return string
     */
    public function hash($user, $password)
    {
        $uHash = hash($this->_hashAlgorithm, self::PASSWORD_SALT . ':' . $user);
        $pHash = hash($this->_hashAlgorithm, self::PASSWORD_SALT . ':' . $password);
        return hash($this->_hashAlgorithm, $uHash . ':' . $pHash);
    }
}