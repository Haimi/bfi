<?php

namespace BFI\View\Helper;

use BFI\View\Helper;

class Link extends Helper
{
    private $_allowedParams = array(
        'rel',
        'href',
        'type',
        'media'
    );

    public function render(array $params = array())
    {
        $outParams = array();
        foreach ($params[0] as $pKey => $pVal) {
            if (in_array($pKey, $this->_allowedParams)) {
                $outParams[$pKey] = $pVal;
            }
        }
        return $this->_buildTag('link', $outParams);
    }
}