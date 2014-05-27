<?php

namespace BFI\Plugin;

use \BFI\Config;
use \BFI\FrontController;

abstract class APlugin
{
    /**
     * Method to be invoked at pre-dispatch of controller.
     * May be overridden if needed
     */
    public function preDispatch()
    {

    }

    /**
     * Method to be invoked at post-dispatch of controller.
     * May be overridden if needed
     */
    public function postDispatch()
    {

    }

    public static function initPlugins()
    {
        foreach (Config::get('plugin') as $name => $config)
        {
            $confValues = array();
            foreach ($config as $cVal) {
                $confValues[] = $cVal;
            }
            FrontController::getInstance()->loadPlugin($name, $confValues);
        }
    }
}