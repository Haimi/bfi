<?php

namespace BFI\Form\Element;

use BFI\Form\Element;

class Password extends Element
{
    /**
     * The Element type
     * @var string
     */
    public $type = 'password';

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
        foreach ($this->_errors as $error) {
            // TODO: Translate error messages
            $output .= '<div class="fehler">' . $error . '</div>';
        }
        return $this->_buildTag('input', $attribs) . $output;
    }
}