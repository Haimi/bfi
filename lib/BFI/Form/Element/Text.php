<?php

namespace BFI\Form\Element;

use BFI\Form\Decorator\Input;
use BFI\Form\Decorator\Label;
use BFI\Form\Element;

/**
 * Class Text
 * @package BFI\Form\Element
 */
class Text extends Element
{
    /**
     * The Element type
     * @var string
     */
    public $type = 'text';

    /**
     * C'tor
     * @param string $name
     */
    public function __construct($name)
    {
        parent::__construct($name);
        $this->addDecorator(new Input($this));
        $this->addDecorator(new Label($this));
    }
}