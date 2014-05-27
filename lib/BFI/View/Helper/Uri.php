<?php

namespace BFI\View\Helper;


use BFI\FrontController;
use BFI\View\Helper;

class Uri extends Helper
{
    /**
     * Get an URI with current host and port
     * @param array $params
     * @return string
     */
    public function getAbsoluteUri(array $params = array())
    {
        $pageURL = 'http';
        if ($_SERVER['HTTPS'] == 'on') {
            $pageURL .= 's';
        }
        $pageURL .= '://' . $_SERVER['SERVER_NAME'];
        if ($_SERVER['SERVER_PORT'] != '80') {
            $pageURL .= ':'.$_SERVER['SERVER_PORT'];
        }
        return $pageURL . $this->render(array($params));
    }

    /**
     * Build an ajax Link
     * @param string $building
     * @param array $data
     * @return string
     */
    public function getAjaxLink($building, array $data = array())
    {
        return 'ajaxGebaeudeLink(\'' . $building . '\',\'' . json_encode($data) . '\')';
    }

    public function render(array $params = array())
    {
        $router = FrontController::getInstance()->getRouter();
        $controller = $router->getControllerName();
        $action = $router->getActionName();
        $urlParams =  array();
        foreach ($params[0] as $pName => $pVal) {
            switch ($pName) {
                case 'controller':
                    $controller = $pVal;
                    break;
                case 'action':
                    $action = $pVal;
                    break;
                default:
                    $urlParams[] = $pName . '/' . $pVal;
                    break;
            }
        }
        if (count($urlParams) > 0) {
            $urlParams = implode('/', $urlParams) . '/';
        } else {
            $urlParams = '';
        }
        return '/' . $controller . '/' . $action . '/' . $urlParams;
    }
}