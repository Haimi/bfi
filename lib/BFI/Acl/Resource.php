<?php

namespace BFI\Acl;

/**
 * ACL Resource class
 * Class Resource
 * @package BFI\Acl
 */
class Resource
{
    /**
     * Represent all actions
     */
    const ACTION_ALL = 0;

    /**
     * The represented Controller
     * @var string
     */
    protected $_controller = 'index';

    /**
     * The represented action
     * @var string
     */
    protected $_action = 'index';

    /**
     * C'tor
     * @param string $controller
     * @param string $action
     */
    public function __construct($controller, $action = self::ACTION_ALL)
    {
        $this->_controller = strval($controller);
        $this->_action = strval($action);
    }

    /**
     * Returns the represented Controller
     * @return string
     */
    public function getController()
    {
        return $this->_controller;
    }

    /**
     * Returns the represented action
     * @return string
     */
    public function getAction()
    {
        return $this->_action;
    }
}