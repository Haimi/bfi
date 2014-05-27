<?php

namespace BFI\Form\Decorator;

use BFI\Form\Decorator;
use BFI\FrontController;

/**
 * Class Label
 * @package BFI\Form\Decorator
 */
class Label extends Element
{
    /**
     * @var bool
     */
    protected $_enabled = true;

    /**
     * Define if the Label is enabled
     * @param bool $enabled
     */
    public function setEnabled($enabled)
    {
        $this->_enabled = $enabled;
    }

    /**
     * Show if the Label is enabled
     * @return bool
     */
    public function isEnabled()
    {
        return $this->_enabled;
    }

    /**
     * Render the Input Element
     * @return string
     */
    public function render()
    {
        if ($this->_enabled === false) {
            return '';
        }
        $label = $this->_element->getLabel();
        if (FrontController::getInstance()->getPlugin('translate')) {
            $label = FrontController::getInstance()->getPlugin('translate')->_($label);
        }
        return $this->_buildTag('label', array('for' => $this->_element->getName()), $label);
    }
} 