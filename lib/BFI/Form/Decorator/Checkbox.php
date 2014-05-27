<?php

namespace BFI\Form\Decorator;

use BFI\Form\Decorator;

/**
 * Class Checkbox
 * @package BFI\Form\Decorator
 */
class Checkbox extends Element
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
        if($this->_element instanceof \BFI\Form\Element\Checkbox && $this->_element->isChecked() == true) {
            $attribs['checked'] = 'checked';
        }
        return $this->_buildTag('input', $attribs);
    }
} 