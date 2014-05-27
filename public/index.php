<?php

define('BASE_PATH', realpath(__DIR__ . '/..'));
define('DEBUG', true);

if (DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

set_include_path(get_include_path() .
PATH_SEPARATOR . BASE_PATH . '/lib' .
PATH_SEPARATOR . BASE_PATH . '/forms');
spl_autoload_extensions(".php");
spl_autoload_register(function($name) {
    $name = str_replace('\\', '/', $name);
    $paths = array('forms', 'models', 'lib');
    foreach ($paths as $path) {
        $file = BASE_PATH . '/' . $path . '/' . $name . '.php';
        if (is_file($file)) require_once($file);
    }
});

\BFI\Config::loadXml(BASE_PATH . '/config/config.xml');

$frontController = \BFI\FrontController::getInstance();

// Language and Fallback
if (! is_null($frontController->getRouter()->getParam('lang'))) {
    \BFI\Session::set('lang', $frontController->getRouter()->getParam('lang')); // Browser language
} elseif (is_null(\BFI\Session::get('lang'))) {
    \BFI\Session::set('lang', 'en'); // default lanuage
}

// Load Plugins
\BFI\Plugin\APlugin::initPlugins();


// Set Auth to guest if not defined
if (is_null(\BFI\Session::get('Auth'))) {
    \BFI\Auth::getInstance()->setRole(\BFI\Config::get('ACL')->getRole('guest'));
    \BFI\Session::set('Auth', \BFI\Auth::getInstance());
}

// Set db cache
\BFI\Db\Table::$_cache = new \BFI\Cache\Redis();

// Start the Front controller loop
$frontController->run();
