<?php

namespace BFI\Controller;

use \BFI\FrontController;
use \BFI\View\View;
use \BFI\Auth;

/**
 * Class Controller
 * @package \BFI\Controller
 */
abstract class AController
{
    /**
     * All plugins
     * @var array
     */
    protected $_plugins = null;

    /**
     * The current router instance
     * @var \BFI\Router
     */
    protected $_router = null;

    /**
     * The current view instance
     * @var \BFI\View\View
     */
    protected $_view = null;

    /**
     * View enabled or not
     * @var bool
     */
    protected $_viewEnabled = true;

    /**
     * Optional redirect
     * @var \BFI\Controller\Redirect
     */
    protected $_redirect = null;

    /**
     * C'tor
     * @param mixed $prev
     * @param \BFI\View\View $prevView
     */
    public function __construct($prev = null, View $prevView = null)
    {
        if ($prev instanceof Redirect) {
            $this->_redirect = $prev;
        }
        $this->_plugins = FrontController::getInstance()->getPlugins();
        $this->_router = FrontController::getInstance()->getRouter();
        if (! is_null($prevView)) {
            $this->_view = $prevView;
        } else {
            $this->_view = new View();
        }

    }

    /**
     * Run the Controller
     * @return Redirect
     */
    public function run()
    {
        try {
            $this->_checkPermissions();
            $this->_preDispatch();
            // Run preDispatch of all Plugins
            foreach ($this->_plugins as $plugin) {
                /** @var \BFI\Plugin\APlugin $plugin **/
                $plugin->preDispatch();
            }
            $this->_dispatch();
            // Run postDispatch of all Plugins
            foreach ($this->_plugins as $plugin) {
                $plugin->postDispatch();
            }
            $this->_postDispatch();
            if ($this->_viewEnabled) {
                echo $this->_view->render();
            }
        } catch (\Exception $e) {
            if ($e instanceof Redirect) {
                return $e;
            }
            if (DEBUG) {
                echo '<h1>An error occured:</h1>';
                echo '<h2>' . $e->getMessage() . '</h2><pre>';
                echo $e->getTraceAsString();
                echo '</pre>';
            }
            return new Redirect('error', 'index', $e);
        }
        return null;
    }

    /**
     * Check is user is allowed to access this controller/Action
     * @return bool
     * @throws \BFI\Controller\Redirect
     */
    protected function _checkPermissions()
    {
        if (! Auth::getInstance()->isAllowed($this->_router->getControllerName(), $this->_router->getActionName())) {
            throw new Redirect('error', 'index', new \BFI\Exception(sprintf(
                'Not allowed to access Resource "/%s/%s/" with role "%s"',
                $this->_router->getControllerName(),
                $this->_router->getActionName(),
                (Auth::getInstance()->getRole()) ? Auth::getInstance()->getRole()->getName() : 'not_logged_in'
            )));
        }
        return true;
    }

    /**
     * PreDispatch Hook
     * Can be overridden
     */
    protected function _preDispatch()
    {
    }

    /**
     * Main dispatch
     */
    protected function _dispatch()
    {
        $actionMethod = $this->_router->getActionName() . 'Action';
        call_user_func(array($this, $actionMethod));
    }

    /**
     * PostDispatch Hook
     * can be overridden
     */
    protected function _postDispatch()
    {
    }

    /**
     * Enable/Disable view
     * @param bool $enabled
     * @return $this
     */
    protected function _enableView($enabled = true)
    {
        $this->_viewEnabled = (bool) $enabled;
        return $this;
    }

    /**
     * Redirect to another Controller/Action
     * @param string $controller
     * @param string $action
     * @param array $params
     * @throws \BFI\Controller\Redirect
     */
    protected function _redirect($controller, $action = 'index', array $params = array())
    {
        $redirect = new Redirect(strval($controller), strval($action));
        $redirect->params = $params;
        throw $redirect;
    }

    /**
     * Get the current View
     * @return View
     */
    public function getView()
    {
        return $this->_view;
    }
}