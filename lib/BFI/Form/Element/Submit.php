<?php

namespace BFI\Form\Element;

use BFI\Form\Decorator\Input;
use BFI\Form\Element;

class Submit extends Element
{
    /**
     * The Element type
     * @var string
     */
    public $type = 'submit';

    /**
     * C'tor
     * @param string $name
     */
    public function __construct($name)
    {
        parent::__construct($name);
        $this->addDecorator(new Input($this));
    }
}