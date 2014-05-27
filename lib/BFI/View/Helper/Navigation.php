<?php

namespace BFI\View\Helper;


use BFI\Acl\Role;
use BFI\Auth;
use BFI\FrontController;
use BFI\Session;
use BFI\View\Helper;

/**
 * Class Navigation
 * @package BFI\View\Helper
 */
class Navigation extends Helper
{
    /**
     * @var Role
     */
    protected $_role = null;

    /**
     * Active Page with action and controller in array
     * @var array
     */
    private $_activePage = array();

    /**
     * C'tor
     */
    public function __construct()
    {
        $router = FrontController::getInstance()->getRouter();
        $this->_activePage = array(
            'controller' => $router->getControllerName(),
            'action' => $router->getActionName()
        );
        $auth = Session::get('Auth');
        if ($auth instanceof Auth) {
            $this->_role =$auth->getRole();
        }
    }

    /**
     * Check if current user may access Navi element
     * @param array $navi
     * @return bool
     */
    protected function _checkAcl(array $navi)
    {
        if (is_null($this->_role)) {
            // No ACL -> no Checks
            return true;
        }
        // Enabled for role
        if (array_key_exists('enabled', $navi)) {
            if ($navi['enabled'] == $this->_role->getName()) {
                return true;
            }
            return false;
        }
        // Disabled for role
        if (array_key_exists('disabled', $navi)) {
            if ($navi['disabled'] == $this->_role->getName()) {
                return false;
            }
            return true;
        }
        return true;
    }

    /**
     * Render the output
     * @param array $params
     * @return string
     */
    public function render(array $params = array())
    {
        $linkArray = array();
        foreach ($params[0] as $naviElement) {
            // Test if it is disabled by ACL
            if (! $this->_checkAcl($naviElement)) {
                continue;
            }
            if (array_key_exists('controller', $naviElement)) {
                $linkConfig = array();
                $listConfig = array();
                // MVC Link
                $mvcUrlConfig = array('controller' => $naviElement['controller']);
                if (array_key_exists('action', $naviElement)) {
                    $mvcUrlConfig['action'] = $naviElement['action'];
                }
                if (array_key_exists('params', $naviElement)) {
                    foreach ($naviElement['params'] as $pName => $pVal) {
                        $mvcUrlConfig[$pName] = $pVal;
                    }
                }
                $linkConfig['href'] = $this->view->uri($mvcUrlConfig);
                // Active Element
                if ($this->_activePage['controller'] == $mvcUrlConfig['controller'] &&
                    $this->_activePage['action'] == $mvcUrlConfig['action']) {
                    $listConfig['class'] = 'active';
                    $linkConfig['class'] = 'active';
                }
                // Prepare for assembly
                if (array_key_exists('title', $naviElement)) {
                    $linkConfig['title'] = $naviElement['title'];
                } elseif (array_key_exists('html', $naviElement)) {
                    $linkConfig['title'] = strip_tags($naviElement['html']);
                }
                $linkArray[] = $this->_buildTag('li', array(), $this->_buildTag('a', $linkConfig, $naviElement['html']));
            } elseif (array_key_exists('url', $naviElement)) {
                // HTTP Link
                $linkConfig = array();
                // MVC Link
                $linkConfig['href'] = $naviElement['url'];
                if (array_key_exists('title', $naviElement)) {
                    $linkConfig['title'] = $naviElement['title'];
                } elseif (array_key_exists('html', $naviElement)) {
                    $linkConfig['title'] = strip_tags($naviElement['html']);
                }
                $linkArray[] = $this->_buildTag('li', array(), $this->_buildTag('a', $linkConfig, $naviElement['html']));
            } else {
                // No link, just text
                $linkArray[] = $this->_buildTag('li', array(), $naviElement['html']);
            }
        }
        return $this->_buildTag('nav', array(), $this->_buildTag('ul', array(), implode(PHP_EOL, $linkArray)));
    }
}