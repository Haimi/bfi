<?php


namespace BFI;

/**
 * Class Router
 * @package BFI
 */
class Router
{
    /**
     * The default Controller name
     */
    const DEFAULT_CONTROLLER = 'index';

    /**
     * The default action name
     */
    const DEFAULT_ACTION = 'index';

    /**
     * The controller name
     * @var string
     */
    private $_controller = '';

    /**
     * The action name
     * @var string
     */
    private $_action = '';

    /**
     * The array of parameters
     * @var array
     */
    private $_params = array();

    /**
     * The array of post parameters
     * @var array
     */
    private $_postParams = array();

    /**
     * @var string
     */
    private $_method = null;

    /**
     * C'tor
     * Parse URI and get POST parameters
     * @param array $overrideUri
     */
    public function __construct(array $overrideUri = null)
    {
        $this->_parseURI($overrideUri);
        $this->_method = strtolower($_SERVER['REQUEST_METHOD']);
        $this->_postParams = $_POST;
    }

    /**
     * Build a classname-convention-like name from url "-"-Separated name
     * @param string $name
     * @return string
     */
    private function _normalizeControllerActionName($name)
    {
        return lcfirst(implode('', array_map('ucfirst', explode('-', $name))));
    }

    /**
     * Parse URI and extract controller, action and $_GET-parameters
     * @param array $overrideUri
     */
    private function _parseURI(array $overrideUri = null)
    {
        if (is_null($overrideUri)) {
            $uri = array_filter(explode('/', trim($_SERVER['REQUEST_URI'], '/')));
            if (count($uri) < 2) {
                $this->_action = self::DEFAULT_ACTION;
            }
            if (count($uri) < 1) {
                $this->_controller = self::DEFAULT_CONTROLLER;
            }
            for ($i = 0; $i < count($uri); $i++) {
                if ($i == 0) {
                    $this->_controller = $this->_normalizeControllerActionName($uri[$i]);
                } elseif ($i == 1) {
                    $this->_action = $this->_normalizeControllerActionName($uri[$i]);
                } else {
                    if (array_key_exists($i + 1, $uri)) {
                        $this->_params[$uri[$i]] = $uri[++$i];
                    } else {
                        $this->_params[$uri[$i]] = null;
                    }
                }
            }
        } else {
            $this->_controller = $overrideUri['controller'];
            $this->_action = $overrideUri['action'];
        }
        // Fallback for alternative GET params
        foreach ($_GET as $gKey => $gVal) {
            $this->_params[$gKey] = $gVal;
        }
        // POST params
        foreach ($_POST as $key => $val) {
            $this->_params[$key] = $val;
            $this->_postParams[$key] = $val;
        }
    }

    /**
     * Return the controller name
     * @return string
     */
    public function getControllerName()
    {
        return $this->_controller;
    }

    /**
     * Set the controller name
     * @param string $name
     */
    public function setControllerName($name)
    {
        $this->_controller = strval($name);
    }

    /**
     * Return the action name
     * @return string
     */
    public function getActionName()
    {
        return $this->_action;
    }

    /**
     * Set the action name
     * @param string $name
     */
    public function setActionName($name)
    {
        $this->_action = strval($name);
    }

    /**
     * Get the method of the call
     * @return string
     */
    public function getMethod()
    {
        return $this->_method;
    }

    /**
     * Get if it was a POST request
     * @return bool
     */
    public function isPost()
    {
        return ($this->_method === 'post');
    }

    /**
     * Get if it was a GET request
     * @return bool
     */
    public function isGet()
    {
        return ($this->_method === 'get');
    }

    /**
     * Get if it was a HEAD request
     * @return bool
     */
    public function isHead()
    {
        return ($this->_method === 'head');
    }

    /**
     * Get if it was a PUT request
     * @return bool
     */
    public function isPut()
    {
        return ($this->_method === 'put');
    }

    /**
     * Get all $_GET-parameters
     * @return array
     */
    public function getParams()
    {
        return $this->_params;
    }

    /**
     * Add params
     * @param array $params
     * @return Router
     */
    public function addParams(array $params)
    {
        $this->_params = array_merge($this->_params, $params);
        return $this;
    }

    /**
     * Get a single $_GET-parameter
     * Or a fallback value if not present
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function getParam($name, $default = null)
    {
        if (array_key_exists($name, $this->_params)) {
            return $this->_params[$name];
        }
        return $default;
    }

    /**
     * Return an array of all $_POST-parameters
     * @return array
     */
    public function getPostParams()
    {
        return $this->_postParams;
    }

    /**
     * Return a single $_POST-parameter identified by name
     * Or a fallback value if not present
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function getPostParam($name, $default = null)
    {
        if (array_key_exists($name, $this->_postParams)) {
            return $this->_postParams[$name];
        }
        return $default;
    }
}