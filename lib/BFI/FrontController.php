<?php

namespace BFI;

use BFI\Controller\AController;
use BFI\Controller\Redirect;
use BFI\Db\Table;

/**
 * Class FrontController
 * @package BFI
 */
class FrontController
{
    /**
     * The FrontController instance
     * @var FrontController
     */
    private static $_instance = null;

    /**
     * The controller directory
     * @var string
     */
    private $_controllerDir = 'controllers';

    /**
     * The Controller instance
     * @var AController
     */
    private $_controller = null;

    /**
     * The router
     * @var Router
     */
    private $_router = null;

    /**
     * Array of loaded plugins
     * @var array
     */
    private $_plugins = array();

    /**
     * Singelton: this is the access method to the single instance
     * @return FrontController
     */
    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Do everything we need to start
     */
    private function __construct()
    {
        $this->_loadConfigs();
        $this->_router = new Router();
    }

    /**
     * Load all configs from config folder
     */
    private function _loadConfigs()
    {
        $configPath = BASE_PATH . '/config/';
        $configDir = opendir($configPath);
        while (false !== ($entry = readdir($configDir))) {
            $file = $configPath . $entry;
            if (is_file($file) && substr($file, -4) == '.php') {
                require_once($file);
            }
        }
    }

    /**
     * Get the router instance
     * @return Router
     */
    public function getRouter()
    {
        return $this->_router;
    }

    /**
     * Run the application
     */
    public function run()
    {
        $result = null;
        do {
            $controllerName = ucfirst($this->_router->getControllerName()) . 'Controller';
            require_once(BASE_PATH . '/' . $this->_controllerDir . '/' . $controllerName . '.php');
            if (! is_null($this->_controller)) {
                $this->_controller = new $controllerName($result, $this->_controller->getView());
            } else {
                $this->_controller = new $controllerName($result);
            }
            $result = $this->_controller->run();
            if ($result instanceof Redirect) {
                $this->_router = new Router(array(
                    'controller' => $result->controller,
                    'action' => $result->action
                ));
                $this->_router->addParams($result->params);
            }
            Table::resetInstances();
        } while ($result instanceof Redirect);
    }

    /**
     * Load a Plugin
     * @param string $name
     * @param array $params
     * @return FrontController
     */
    public function loadPlugin($name, array $params = array())
    {
        $pluginName = ucfirst($name);
        require_once('BFI/Plugin/' . $pluginName . '.php');
        $reflectionClass = new \ReflectionClass('\\BFI\\Plugin\\' . $pluginName);
        $this->_plugins[$name] = $reflectionClass->newInstanceArgs($params);
        return $this;
    }

    /**
     * Get a plugin instance
     * @param string $name
     * @return \BFI\Plugin\APlugin
     */
    public function getPlugin($name)
    {
        if (array_key_exists($name, $this->_plugins)) {
            return $this->_plugins[$name];
        }
        return null;
    }

    /**
     * Get all registered plugins
     * @return array
     */
    public function getPlugins()
    {
        return $this->_plugins;
    }
}