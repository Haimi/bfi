<?php
/**
 * Created by JetBrains PhpStorm.
 * User: haimi
 * Date: 26.09.13
 * Time: 12:55
 * To change this template use File | Settings | File Templates.
 */

namespace BFI\Acl;


class Acl
{
    /**
     * All roles
     * @var array
     */
    protected $_roles = array();

    /**
     * Add a role to ACL
     * @param Role $role
     */
    public function addRole(Role $role)
    {
        $this->_roles[$role->getName()] = $role;
    }

    /**
     * Get a Role by name
     * @param string $name
     * @return Role
     */
    public function getRole($name)
    {
        if (array_key_exists($name, $this->_roles)) {
            return $this->_roles[$name];
        }
        $dummy = new Role('dummy');
        $dummy->denyAll();
        return $dummy;
    }

    /**
     * Get all role names
     * @return array
     */
    public function getRoleNames()
    {
        return array_keys($this->_roles);
    }
}