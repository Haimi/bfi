<?php

namespace BFI\View\Helper;

use BFI\View\Helper;

/**
 * Class TimerFormat
 * @package BFI\View\Helper
 */
class TimerFormat extends Helper
{
    public function render(array $params = array())
    {
        if (array_key_exists(0, $params)) {
            $intStunden = floor($params[0] / 3600);
            $params[0]  -= ($intStunden * 3600);
            $intMinuten = floor($params[0] /60);
            $params[0]  -= ($intMinuten * 60);
            return sprintf('%02d:%02d:%02d', $intStunden, $intMinuten, $params[0]);
        }
        return '';
    }
}