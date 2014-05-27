<?php

namespace BFI\Form\Element;

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
     * Render the Element
     * @return string
     */
    public function render()
    {
        $attribs = array_merge($this->_attributes, array(
            'name' => $this->_name
        ));
        $output = '';
        foreach ($this->_errors as $error) {
            $output .= '<div class="fehler">' . $error . '</div>';
        }
        $options = '';
        foreach ($this->_options as $key => $opt) {
            $optAttribs = array('value' => $key);
            if ($this->_value == $key) {
                $optAttribs['selected'] = 'selected';
            }
            $options .= $this->_buildTag('option', $optAttribs, $opt);
        }
        return $this->_buildTag('select', $attribs, $options) . $output;
    }
}