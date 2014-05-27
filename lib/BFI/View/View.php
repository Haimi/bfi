<?php

namespace BFI\View;

use \BFI\Exception;
use \BFI\FrontController;

class View
{
    /**
     * Default view path
     */
    const DEFAULT_VIEW_PATH = 'views';

    /**
     * Default layouts path
     */
    const DEFAULT_LAYOUT_PATH = 'views/layouts';

    /**
     * Default layout name
     */
    const DEFAULT_LAYOUT_FILENAME = 'layout';

    /**
     * Default filename extension
     */
    const DEFAULT_FILENAME_EXTENSTION = 'phtml';

    /**
     * Default content key to use
     */
    const DEFAULT_CONTENT_KEY = 'content';

    /**
     * View data list
     * @var array
     */
    private $_values = array();

    /**
     * The base path where the views are located
     * @var string
     */
    private $_basePath = '';

    /**
     * The base path where the layouts are located
     * @var string
     */
    private $_layoutPath = '';

    /**
     * The layout to be used
     * @var string
     */
    private $_layout = '';

    /**
     * The filename extension
     * @var string
     */
    private $_extension = '';

    /**
     * The content kay in the layout
     * @var string
     */
    private $_contentKey = '';

    /**
     * Determines if a layout should be used
     * @var bool
     */
    private $_useLayout =  true;

    /**
     * Array of view helpers
     * @var array
     */
    private $_helpers = array();

    /**
     * @var string
     */
    private $_alternateViewScript = null;

    /**
     * Create a view instance
     * @param string $viewPath
     * @param bool $useLayout
     * @param string $layoutPath
     * @param string $layoutName
     * @param string $ext
     * @param string $contentKey
     */
    public function __construct($viewPath = null, $useLayout = true, $layoutPath = null, $layoutName = null,
                                $ext = null, $contentKey = null)
    {
        $this->setBasePath($viewPath);
        $this->_useLayout = $useLayout;
        $this->setLayoutPath($layoutPath);
        $this->setLayout($layoutName);
        $this->setExtension($ext);
        $this->setContentKey($contentKey);
    }

    /**
     * Load and instantiate all one helper
     * @param string $name
     */
    private function _loadHelper($name)
    {
        if (array_key_exists($name, $this->_helpers)) {
            return;
        }
        $helperFileName = ucfirst($name);
        $helperFile = BASE_PATH . '/lib/BFI/View/Helper/' . $helperFileName . '.php';
        if (is_file($helperFile)) {
            $helperClassName = '\\BFI\\View\\Helper\\' . $helperFileName;
            $this->_helpers[$name] = new $helperClassName();
            $this->_helpers[$name]->view = $this;
        }
    }

    /**
     * Setting a view value
     * @param string $key
     * @param mixed $value
     * @return View
     */
    public function assign($key, $value = null)
    {
        $this->_values[strval($key)] = $value;
        return $this;
    }

    /**
     * Used for shorthand-setting parameters
     * @param string $key
     * @param mixed $value
     * @return View
     */
    public function __set($key, $value)
    {
        return $this->assign($key, $value);
    }

    /**
     * Return a value by key
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        if (array_key_exists($key, $this->_values)) {
            return $this->_values[$key];
        }
        return '';
    }

    /**
     * Set the base path for the views
     * @param string $path
     * @return View
     * @throws Exception
     */
    public function setBasePath($path = null)
    {
        if (is_null($path)) {
            $this->_basePath = BASE_PATH . '/' . self::DEFAULT_VIEW_PATH;
        } else {
            if (!is_dir($path)) {
                $path = BASE_PATH . '/' . $path;
                if (!is_dir($path)) {
                    throw new Exception('Invalid base path provided (' . $path . ')');
                }
            }
            $this->_basePath = $path;
        }
        return $this;
    }

    /**
     * Set the base path for the layouts
     * @param string $path
     * @return View
     * @throws Exception
     */
    public function setLayoutPath($path = null)
    {
        if (is_null($path)) {
            $this->_layoutPath = BASE_PATH . '/' . self::DEFAULT_LAYOUT_PATH;
        } else {
            if (!is_dir($path)) {
                $path = BASE_PATH . '/' . $path;
                if (!is_dir($path)) {
                    throw new Exception('Invalid layout path provided (' . $path . ')');
                }
            }
            $this->_layoutPath = $path;
        }
        return $this;
    }

    /**
     * Set the layout to use
     * @param string $name
     * @return View
     */
    public function setLayout($name = null)
    {
        if (is_null($name)) {
            $this->_layout = self::DEFAULT_LAYOUT_FILENAME;
        } else {
            $this->_layout = strval($name);
        }
        return $this;
    }

    /**
     * Enable or disable layout
     * @param bool $enabled
     */
    public function enableLayout($enabled = true)
    {
        $this->_useLayout = (bool) $enabled;
    }

    /**
     * Set filename extension for views and layouts
     * @param string $ext
     * @return View
     */
    public function setExtension($ext = null)
    {
        if (is_null($ext)) {
            $this->_extension = self::DEFAULT_FILENAME_EXTENSTION;
        } else {
            $this->_extension = $ext;
        }
        return $this;
    }

    /**
     * Set the Layout's content key
     * @param string $key
     * @return View
     */
    public function setContentKey($key = null)
    {
        if (is_null($key)) {
            $key = self::DEFAULT_CONTENT_KEY;
        }
        $this->_contentKey = strval($key);
        return $this;
    }

    /**
     * Set an alternate View Script
     * @param string $script
     */
    public function overrideViewScript($script)
    {
        $this->_alternateViewScript = $script;
    }

    /**
     * Renders view to a specific template
     * @param string $view
     * @return string
     */
    public function render($view = null)
    {
        if (is_null($view)) {
            if (is_null($this->_alternateViewScript)) {
                $router = FrontController::getInstance()->getRouter();
                $view = $router->getControllerName() . '/' . $router->getActionName();
            } else {
                $view = $this->_alternateViewScript;
            }

        }
        // VIEW
        ob_start();
        include($this->_basePath . '/' . $view . '.' . $this->_extension);
        $content = ob_get_clean();

        // LAYOUT
        if ($this->_useLayout) {
            ob_start();
            $this->assign($this->_contentKey, $content);
            include($this->_layoutPath . '/' . $this->_layout . '.' . $this->_extension);
            $content = ob_get_clean();
        }
        return $content;
    }

    /**
     * Call helper - Fallback to empty return string
     * @param string $name
     * @param array $args
     * @return string
     */
    public function __call($name, $args = array())
    {
        $this->_loadHelper($name);
        if (array_key_exists($name, $this->_helpers)) {
            return $this->_helpers[$name]->render($args);
        }
        return '';
    }

    /**
     * Include a partial
     * @param string $name
     * @throws Exception
     */
    public function partial($name)
    {
        if (is_file($this->_basePath . '/partials/' . $name . '.phtml')) {
            include($this->_basePath . '/partials/' . $name . '.phtml');
            return;
        }
        throw new Exception(sprintf('Partial "%s" not found.', $name));
    }

    /**
     * Translate a string
     * @param string $str
     * @return string
     */
    public function _($str)
    {
        return FrontController::getInstance()->getPlugin('translate')->_($str);
    }
}