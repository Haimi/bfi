<?php

namespace BFI\Acl;

/**
 * Class Role
 * @package BFI\Acl
 */
class Role
{
    /**
     * For all resources
     */
    const RESOURCES_ALL = 0;

    /**
     * The role name
     * @var string
     */
    protected $_name;

    /**
     * Array of all allowed resources
     * @var int|array
     */
    protected $_allowedResources = array();

    /**
     * Array of all denied resources
     * @var array
     */
    protected $_deniedResources = array();

    /**
     * C'tor
     * Set role name and role to inherit from
     * @param string $name
     * @param Role $parent
     */
    public function __construct($name, Role $parent = null)
    {
        $this->_name = strval($name);
        if (! is_null($parent)) {
            $this->_allowedResources = $parent->getAllowed();
            $this->_deniedResources = $parent->getDenied();
        }
    }

    /**
     * Get role name
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Allow a single Resource
     * @param Resource $res
     */
    public function allow(Resource $res)
    {
        if (! is_array($this->_allowedResources)) {
            $this->_allowedResources = array();
        }
        $this->_allowedResources[] = $res;
    }

    /**
     * Deny a single resource
     * @param Resource $res
     */
    public function deny(Resource $res)
    {
        if (! is_array($this->_deniedResources)) {
            $this->_deniedResources = array();
        }
        $this->_deniedResources[] = $res;
    }

    /**
     * Allow all resources
     */
    public function allowAll()
    {
        $this->_allowedResources = self::RESOURCES_ALL;
    }

    /**
     * Deny all resources
     */
    public function denyAll()
    {
        $this->_deniedResources = self::RESOURCES_ALL;
    }

    /**
     * Return all allowed resources
     * @return array|int
     */
    public function getAllowed()
    {
        return $this->_allowedResources;
    }

    /**
     * Return all denied resources
     * @return array|int
     */
    public function getDenied()
    {
        return $this->_deniedResources;
    }

    /**
     * @param string $controller
     * @param string $action
     * @return bool
     */
    public function isAllowed($controller, $action)
    {
        if ($this->_deniedResources != self::RESOURCES_ALL) {
            // All denied resources
            foreach ($this->_deniedResources as $res) {
                if ($res->getController() == $controller &&
                    ($res->getAction() == Resource::ACTION_ALL || $res->getAction() == $action)) {
                    return false;
                }
            }
        } else {
            return false;
        }

        if ($this->_allowedResources != self::RESOURCES_ALL) {
            // All allowed resources
            foreach ($this->_allowedResources as $res) {
                if ($res->getController() == $controller &&
                    ($res->getAction() == Resource::ACTION_ALL || $res->getAction() == $action)) {
                    return true;
                }
            }
        } else {
            return true;
        }

        // If not sure: deny
        return false;
    }
}