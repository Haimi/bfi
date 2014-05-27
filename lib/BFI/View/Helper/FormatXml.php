<?php

namespace BFI\View\Helper;

use BFI\View\Helper;

/**
 * Class FormatXml
 * @package BFI\View\Helper
 */
class FormatXml extends Helper
{
    public function render(array $params = array())
    {
        if (empty($params[1])) {
            $params[1] = '';
        } elseif (is_numeric($params[1])) {
            // Do nothing but no string exclusion
        } elseif (is_string($params[1])) {
            $params[1] = '<![CDATA[' . $params[1] . ']]>';
        }
        return sprintf('<%s>%s</%s>', $params[0], $params[1], $params[0]);
    }
}