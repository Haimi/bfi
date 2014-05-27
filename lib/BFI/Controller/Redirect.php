<?php

namespace BFI\Controller;

/**
 * Class Redirect
 * @package BFI\Controller
 */
class Redirect extends \Exception
{
    /**
     * @var string
     */
    public $controller;

    /**
     * @var string
     */
    public $action;

    /**
     * @var array
     */
    public $params = array();

    /**
     * C'tor
     * @param string $controller
     * @param string $action
     */
    public function __construct($controller, $action = 'index', \Exception $prev = null)
    {
        parent::__construct("Redirecting to /" . $controller . '/' . $action . '/...', -1, $prev);
        $this->controller = $controller;
        $this->action = $action;
    }
}