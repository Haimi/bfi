<?php

namespace BFI\Form\Element;

use BFI\Form\Element;

class Submit extends Element
{
    /**
     * The Element type
     * @var string
     */
    public $type = 'submit';

    /**
     * Render the Element
     * @return string
     */
    public function render()
    {
        $attribs = array_merge($this->_attributes, array(
            'type' => $this->type,
            'name' => $this->_name,
            'value' => $this->_value
        ));
        $output = '';
        return $this->_buildTag('input', $attribs) . $output;
    }
}