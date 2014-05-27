<?php

namespace BFI\View\Helper;

use BFI\View\Helper;

class Script extends Helper
{
    const DEFAULT_TYPE = 'text/javascript';

    private $_defaultParams = array(
        'type' => self::DEFAULT_TYPE,
        'src' => ''
    );

    public function render(array $params = array())
    {
        $outParams = $this->_defaultParams;
        foreach ($params[0] as $pKey => $pVal) {
            $outParams[$pKey] = $pVal;
        }
        return $this->_buildTag('script', $outParams);
    }
}