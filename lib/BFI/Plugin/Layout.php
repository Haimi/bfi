<?php

namespace BFI\Plugin;

use BFI\FrontController;
use BFI\Plugin\APlugin;
use BFI\Session;

class Layout extends APlugin
{
    /**
     * @var string
     */
    private $_layout = 'default';

    /**
     * C'tor
     * Load layout from session
     */
    public function __construct()
    {
        $layout = Session::get('Layout');
        if (!is_null($layout)) {
            $this->_layout = $layout;
        }
    }

    /**
     * Set the current layout in plugin and Session
     * @param string $layout
     */
    private function _setLayout($layout)
    {
        $this->_layout = $layout;
        Session::set('Layout', $layout);
    }

    /**
     * preDispatch Hook for the Controller
     * Find if new Layout is set in parameters and set it accordingly
     */
    public function preDispatch()
    {
        $newLayout = FrontController::getInstance()->getRouter()->getParam('layout');
        if (!is_null($newLayout)) {
            $this->_setLayout($newLayout);
        }
    }

    /**
     * Get the currently set Layout
     * @return string
     */
    public function getLayout()
    {
        return $this->_layout;
    }
}