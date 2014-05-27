<?php

namespace BFI\View\Helper;

use BFI\View\Helper;

class Meta extends Helper
{
    private $_allowedParams = array(
        'name',
        'content',
        'http-equiv'
    );

    public function render(array $params = array())
    {
        $outParams = array();
        foreach ($params[0] as $pKey => $pVal) {
            if (in_array($pKey, $this->_allowedParams)) {
                $outParams[$pKey] = $pVal;
            }
        }
        return $this->_buildTag('meta', $outParams);
    }
}