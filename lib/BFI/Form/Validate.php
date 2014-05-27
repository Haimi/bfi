<?php

namespace BFI\Form;

abstract class Validate
{
    /**
     * Array of error messages
     * @var array
     */
    protected $_errors = array();

    /**
     * Validate an Element
     * @param mixed $value
     * @param array $context
     * @return bool
     */
    abstract public function validate($value, array $context = array());

    /**
     * Get an array of error messages
     * @return array
     */
    public function getErrors()
    {
        return $this->_errors;
    }
}