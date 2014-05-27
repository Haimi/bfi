<?php

namespace BFI\Form\Decorator;

use BFI\Form\Decorator;

/**
 * Class Input
 * @package BFI\Form\Decorator
 */
class Input extends Element
{
    /**
     * Render the Input Element
     * @return string
     */
    public function render()
    {
        $attribs = array_merge($this->_element->getAttributes(), array(
            'type' => $this->_element->type,
            'name' => $this->_element->getName(),
            'value' => $this->_element->getValue()
        ));
        return $this->_buildTag('input', $attribs);
    }
} 