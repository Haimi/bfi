<?php

namespace BFI\Form;
use BFI\Form\Decorator\Error;
use BFI\Form\Decorator\Label;

/**
 * Class Element
 * @package BFI\Form
 */
abstract class Element implements IForm
{
    /**
     * Type of the element
     * @var string
     */
    public $type = '';

    /**
     * Name of the Element
     * @var string
     */
    protected $_name = '';

    /**
     * Element value
     * @var string
     */
    protected $_value = '';


    /**
     * The element Label
     * @var string
     */
    protected $_label = '';

    /**
     * HTML attributes of the element
     * @var array
     */
    protected $_attributes = array();

    /**
     * Array of filters
     * @var array
     */
    protected $_filters = array();

    /**
     * Array of validators
     * @var array
     */
    protected $_validators = array();

    /**
     * Array of error messages
     * @var array
     */
    protected $_errors = array();

    /**
     * @var array
     */
    protected $_decorators = array();

    /**
     * C'tor
     * @param string $name
     */
    public function __construct($name)
    {
        $this->_name = strval($name);
        $this->addDecorator(new Error($this));
    }

    /**
     * Get the element's name
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Set the element value
     * @param string $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->_value = strval($value);
        return $this;
    }

    /**
     * Get the element value
     * @return string
     */
    public function getValue()
    {
        return $this->_value;
    }

    /**
     * Add a Filter on an element
     * @param Filter $filter
     * @return $this
     */
    public function addFilter(Filter $filter)
    {
        $this->_filters[] = $filter;
        return $this;
    }

    /**
     * Clear all filters
     * @return $this
     */
    public function clearFilters()
    {
        $this->_filters = array();
        return $this;
    }

    /**
     * Add a validator
     * @param Validate $validate
     * @return $this
     */
    public function addValidator(Validate $validate)
    {
        $this->_validators[] = $validate;
        return $this;
    }

    /**
     * Clear the validator array
     * @return $this
     */
    public function clearValidators()
    {
        $this->_validators = array();
        return $this;
    }

    /**
     * Add a Decorator
     * @param Decorator $decorator
     */
    public function addDecorator(Decorator $decorator)
    {
        $this->_decorators[get_class($decorator)] = $decorator;
    }

    /**
     * Get all decorators
     * @return array
     */
    public function getDecorators()
    {
        return $this->_decorators;
    }

    /**
     * Get a specific decorator
     * @param string $className
     * @return \BFI\Form\Decorator\Element
     */
    public function getDecorator($className)
    {
        if (array_key_exists($className, $this->_decorators)) {
            return $this->_decorators[$className];
        }
        return null;
    }

    /**
     * Filter value
     * @param string $value
     * @return string
     */
    public function filter($value)
    {
        foreach ($this->_filters as $filter) {
            /** @var Filter $filter **/
            $value = $filter->filter($value);
        }
        return $value;
    }

    /**
     * Validate the Element
     * @param $value
     * @param array $context
     * @return bool
     */
    public function validate($value, array $context = array())
    {
        $valid = true;
        $value = $this->filter($value);
        foreach ($this->_validators as $validate) {
            /** @var Validate $validate **/
            $singleValid = $validate->validate($value, $context);
            if ($singleValid == false) {
                $valid = false;
                $this->_errors = array_merge($this->_errors, $validate->getErrors());
            }
        }
        return $valid;
    }

    /**
     * Get all error messages
     * @return array
     */
    public function getErrors()
    {
        return $this->_errors;
    }

    /**
     * Set a single attribute
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function setAttribute($name, $value = null)
    {
        $this->_attributes[strval($name)] = $value;
        return $this;
    }

    /**
     * Return a single attribute value (With fallback value)
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function getAttribute($name, $default = null)
    {
        $name = strval($name);
        if (array_key_exists($name, $this->_attributes)) {
            return $this->_attributes[$name];
        }
        return $default;
    }

    /**
     * Remove a single attribute
     * @param string $name
     * @return $this
     */
    public function removeAttribute($name)
    {
        $name = strval($name);
        if (array_key_exists($name, $this->_attributes)) {
            unset($this->_attributes[$name]);
        }
        return $this;
    }

    /**
     * Clear all attributes
     * @return $this
     */
    public function clearAttributes()
    {
        $this->_attributes = array();
        return $this;
    }

    /**
     * Return all attributes
     * @return array
     */
    public function getAttributes()
    {
        return $this->_attributes;
    }

    /**
     * Set the element's label
     * @param $label
     * @return $this
     */
    public function setLabel($label)
    {
        $this->_label = strval($label);
        return $this;
    }

    /**
     * Get the Element label
     * @return string
     */
    public function getLabel()
    {
        if ($this->getLabelEnabled()) {
            return $this->_label;
        } else {
            return null;
        }
    }


    /**
     * @param boolean $labelEnabled
     * @return $this
     */
    public function setLabelEnabled($labelEnabled)
    {
        $this->getDecorator('BFI\Form\Decorator\Label')->setEnabled($labelEnabled);
        return $this;
    }

    /**
     * @return boolean
     */
    public function getLabelEnabled()
    {
        return $this->getDecorator('BFI\Form\Decorator\Label')->isEnabled();
    }

    /**
     * Magic rendering
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * Render the Element
     * @return string
     */
    public function render()
    {
        $result = '';
        $errorDecorator = null;
        foreach ($this->_decorators as $decorator) {
            /** @var Decorator $decorator **/
            if (! $decorator instanceof Error && ! $decorator instanceof Label) {
                $result .= $decorator->render();
            } elseif ($decorator instanceof Error) {
                $errorDecorator = $decorator;
            }
        }
        if (! is_null($errorDecorator)) {
            $result .= $errorDecorator->render();
        }
        return $result;
    }
}