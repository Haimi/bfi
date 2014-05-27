<?php

namespace BFI\Form\Decorator;

use BFI\Form\Decorator;

/**
 * Class Value
 * @package BFI\Form\Decorator
 */
class Value extends Element
{
    /**
     * Render only the value
     * @return string
     */
    public function render()
    {
        return $this->_element->getValue();
    }
} 