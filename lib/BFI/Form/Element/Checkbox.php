<?php

namespace BFI\Form\Element;

use BFI\Form\Decorator\Label;
use BFI\Form\Element;

class Checkbox extends Element
{
    /**
     * The Element type
     * @var string
     */
    public $type = 'checkbox';

    /**
     * Set checkbox checked
     * @var bool
     */
    protected $_checked = false;

    /**
     * C'tor
     * @param string $name
     */
    public function __construct($name)
    {
        parent::__construct($name);
        $this->addDecorator(new \BFI\Form\Decorator\Checkbox($this));
        $this->addDecorator(new Label($this));
    }

    /**
     * Set checkbox checked
     * @param bool $checked
     * @return Checkbox
     */
    public function setChecked($checked = true)
    {
        $this->_checked = (bool) $checked;
        return $this;
    }

    /**
     * Get if Checkbox is Checked
     * @return bool
     */
    public function isChecked()
    {
        return $this->_checked;
    }
}