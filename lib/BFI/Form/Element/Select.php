<?php

namespace BFI\Form\Element;

use BFI\Form\Decorator\Label;
use BFI\Form\Element;
use BFI\FrontController;

class Select extends Element
{
    /**
     * The Options
     * @var array
     */
    protected $_options = array();

    /**
     * The Element type
     * @var string
     */
    public $type = 'select';

    /**
     * C'tor
     * @param string $name
     */
    public function __construct($name)
    {
        parent::__construct($name);
        $this->addDecorator(new \BFI\Form\Decorator\Select($this));
        $this->addDecorator(new Label($this));
    }

    /**
     * Add an Option
     * @param string $key
     * @param string $value
     * @param bool $translate
     * @return $this
     */
    public function addOption($key, $value, $translate = true)
    {
        if ($translate && FrontController::getInstance()->getPlugin('translate')) {
            $value = FrontController::getInstance()->getPlugin('translate')->_($value);
        }
        $this->_options[$key] = $value;
        return $this;
    }

    /**
     * Add Options
     * @param array $options
     * @param bool $translate
     * @return $this
     */
    public function addOptions(array $options, $translate = true)
    {
        foreach ($options as $key => $val) {
            $this->addOption($key, $val, $translate);
        }
        return $this;
    }

    /**
     * Get all options
     * @return array
     */
    public function getOptions()
    {
        return $this->_options;
    }
}