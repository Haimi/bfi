<?php

namespace BFI\Form\Element;

use BFI\Form\Decorator\Label;
use BFI\Form\Element;

class Wysiwyg extends Text
{
    /**
     * The Element type
     * @var string
     */
    public $type = 'textarea';

    /**
     * C'tor
     * @param string $name
     */
    public function __construct($name)
    {
        parent::__construct($name);
        $this->addDecorator(new \BFI\Form\Decorator\Wysiwyg($this));
        $this->addDecorator(new Label($this));
    }
}