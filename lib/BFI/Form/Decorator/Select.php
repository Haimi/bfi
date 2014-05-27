<?php

namespace BFI\Form\Decorator;

use BFI\Form\Decorator;

/**
 * Class Select
 * @package BFI\Form\Decorator
 */
class Select extends Element
{
    /**
     * Render the Input Element
     * @return string
     */
    public function render()
    {
        $attribs = array_merge($this->_element->getAttributes(), array(
            'name' => $this->_element->getName()
        ));
        $options = '';
        if ($this->_element instanceof \BFI\Form\Element\Select) {
            foreach ($this->_element->getOptions() as $key => $opt) {
                $optAttribs = array('value' => $key);
                if ($this->_element->getValue() == $key) {
                    $optAttribs['selected'] = 'selected';
                }
                $options .= $this->_buildTag('option', $optAttribs, $opt);
            }
        }
        return $this->_buildTag('select', $attribs, $options);
    }
} 