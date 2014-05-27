<?php

namespace BFI;

use BFI\Acl\Role;

/**
 * Class Auth
 * @package BFI\View
 */
class Auth
{
    /**
     * Singleton
     */
    private function __clone()
    {}

    /**
     * Singleton
     */
    private function __construct()
    {}

    /**
     * @var Role
     */
    protected $_role = null;

    /**
     * @var array
     */
    protected $_data = array();

    /**
     * @return Auth
     */
    public static function getInstance()
    {
        if (! Session::exists('Auth')) {
            Session::set('Auth', new self());
        }
        return Session::get('Auth');
    }

    /**
     * @param \BFI\Acl\Role $role
     */
    public function setRole($role)
    {
        $this->_role = $role;
    }

    /**
     * @return \BFI\Acl\Role
     */
    public function getRole()
    {
        return $this->_role;
    }

    /**
     * @param array $data
     */
    public function setData($data)
    {
        $this->_data = $data;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * Test if logged in
     * @return bool
     */
    public function isLoggedIn()
    {
        if (! is_null($this->_role) && $this->_role->getName() !== 'guest')
        {
            return true;
        }
        return false;
    }

    /**
     * Test is user is allowed to access resource
     * @param string $controller
     * @param string $action
     * @return bool
     */
    public function isAllowed($controller, $action)
    {
        if (in_array($controller, array(
                'index',
                'login',
                'staticFiles',
                'error'
            ))) {
            return true;
        }
        if (is_null($this->_role)) {
            return false;
        }
        return $this->getRole()->isAllowed($controller, $action);
    }
} 