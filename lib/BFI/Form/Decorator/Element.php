<?php

namespace BFI\Form\Decorator;

use BFI\Form\Decorator;

/**
 * Class Element
 * @package BFI\Form\Decorator
 */
abstract class Element extends Decorator
{
    /**
     * @var \BFI\Form\Element
     */
    protected $_element;

    /**
     * C'tor
     * @param \BFI\Form\Element $element
     */
    public function __construct(\BFI\Form\Element $element)
    {
        $this->_element = $element;
    }

    /**
     * @var array
     */
    protected $_errors = array();

    /**
     * Get all error messages
     * @return array
     */
    public function getErrors()
    {
        return $this->_errors;
    }

} 