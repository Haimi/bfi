<?php

namespace BFI\Form\Element;

use BFI\Form\Decorator\Label;
use BFI\Form\Element;

class Captcha extends Text
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
        $this->addDecorator(new \BFI\Form\Decorator\Captcha($this));
        $this->addDecorator(new Label($this));
        $this->addValidator(new \BFI\Form\Validate\Captcha($this->_name));
    }
}