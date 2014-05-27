<?php

namespace BFI\Form\Element;

use BFI\Form\Element;

class Html extends Element
{
    /**
     * The Element type
     * @var string
     */
    public $type = 'html';

    /**
     * Render the Element
     * @return string
     */
    public function render()
    {
        return $this->_value;
    }
}