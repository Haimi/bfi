<?php

namespace BFI\Form\Decorator;

use BFI\Form\Decorator;

/**
 * Class Error
 * @package BFI\Form\Decorator
 */
class Error extends Element
{
    /**
     * @return string
     */
    public function render()
    {
        foreach ($this->_element->getErrors() as $error) {
            // TODO: Translate error messages
            return $this->_buildTag('div', array('class' => 'fehler'), $error);
        }
    }
} 