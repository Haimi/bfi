<?php

namespace BFI\Form\Decorator;

use BFI\Form\Decorator;
use BFI\FrontController;

/**
 * Class Input
 * @package BFI\Form\Decorator
 */
class Captcha extends Element
{
    /**
     * Render the Input Element
     * @return string
     */
    public function render()
    {
        $alt = 'Captcha';
        if (FrontController::getInstance()->getPlugin('translate')) {
            $alt = FrontController::getInstance()->getPlugin('translate')->_($alt);
        }
        $attribs = array_merge($this->_element->getAttributes(), array(
            'type' => $this->_element->type,
            'name' => $this->_element->getName(),
            'value' => $this->_element->getValue()
        ));
        return $this->_buildTag('script', array(
            'type' => 'text/javascript'
            ),'function captchaReload(){document.getElementById("captcha_img").src="/static-files/captcha/seed/"+(1000000001 + (Math.random() * 8999999999));}'
        ) .
        $this->_buildTag('img', array(
            'id' => 'captcha_img',
            'alt' => $alt,
            'src' => '/static-files/captcha/seed/' . rand(1000000000, 9999999999)
        )) .
        $this->_buildTag('img', array(
            'src' => '/images/icons/refresh.png',
            'onclick' => 'captchaReload()'
        )) .
        $this->_buildTag('br') .
        $this->_buildTag('input', $attribs);
    }
} 