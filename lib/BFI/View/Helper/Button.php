<?php

namespace BFI\View\Helper;

use BFI\View\Helper;

class Button extends Helper
{
    public function render(array $params = array())
    {
        if (! array_key_exists('class', $params[0])) {
            $params[0]['class'] = '';
        }
        if (array_key_exists('active', $params[0])) {
            if ($params[0]['active'] === false) {
                $params[0]['class'] .= 'button_inaktiv';
                $params[0]['href'] = 'javascript:void(0)';
            } else {
                $params[0]['class'] .= 'button';
            }
            unset($params[0]['active']);
        }
        $params[0]['class'] .= ' linkbutton';
        $content = $params[0]['content'];

        return $this->_buildTag('a', $params[0], $content);
    }
}