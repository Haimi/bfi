<?php

namespace BFI\Form\Element;

use BFI\Form\Decorator\Value;
use BFI\Form\Element;

class Html extends Element
{
    /**
     * The Element type
     * @var string
     */
    public $type = 'html';

    /**
     * C'tor
     * @param string $name
     */
    public function __construct($name)
    {
        parent::__construct($name);
        $this->addDecorator(new Value($this));
    }
}