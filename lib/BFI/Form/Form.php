<?php

namespace BFI\Form;

use BFI\Form\Decorator\Form\Table;
use BFI\FrontController;
use BFI\Form\Element;

/**
 * Class Form
 * @package BFI\Form
 */
class Form implements IForm
{
    const METHOD_POST = 'POST';
    const METHOD_GET = 'GET';

    /**
     * The controller name
     * @var string
     */
    protected $_controller = 'index';

    /**
     * The action name
     * @var string
     */
    protected $_action = 'index';

    /**
     * The submit method
     * @var string
     */
    protected $_method = self::METHOD_POST;

    /**
     * Attributes for the Form
     * @var array
     */
    protected $_attribs = array();

    /**
     * The name of the form
     * @var string
     */
    protected $_name = 'form';

    /**
     * Form element values
     * @var array
     */
    protected $_values = array();

    /**
     * Custom config Options
     * @var array
     */
    protected $_options = array();

    /**
     * Array of Form elements
     * @var array
     */
    protected $_elements = array();

    /**
     * Array of types to be rendered invisible
     * @var array
     */
    protected $_invisibleElements = array();

    /**
     * C'tor
     * @param string $controller
     * @param string $action
     * @param string $method
     */
    public function __construct(array $options = array(), $controller = null, $action = null, $method = null)
    {
        $this->_options = (array) $options;
        if (!is_null($controller)) {
            $this->setController($controller);
        }
        if (!is_null($action)) {
            $this->setAction($action);
        }
        if (!is_null($method)) {
            $this->setMethod($method);
        }
        // Add default Decorator
        $this->_decorator = new Table($this);
        $this->init();
    }

    /**
     * Init method
     * May be user for abstractions
     */
    public function init()
    {
    }

    /**
     * Set the controller name
     * @param string $controller
     * @return Form
     */
    public function setController($controller)
    {
        $this->_controller = strval($controller);
        return $this;
    }

    /**
     * Get the controller name
     * @return string
     */
    public function getController()
    {
        return $this->_controller;
    }

    /**
     * Set the action name
     * @param string $action
     * @return Form
     */
    public function setAction($action)
    {
        $this->_action = strval($action);
        return $this;
    }

    /**
     * Get the action name
     * @return string
     */
    public function getAction()
    {
        return $this->_action;
    }

    /**
     * Set the method name
     * @param string $method
     * @return Form
     */
    public function setMethod($method)
    {
        $this->_method = $method;
        return $this;
    }

    /**
     * Get the method name
     * @return string
     */
    public function getMethod()
    {
        return $this->_method;
    }

    /**
     * Set the form name
     * @param string $name
     * @return Form
     */
    public function setName($name)
    {
        $this->_name = strval($name);
        return $this;
    }

    /**
     * Get the name
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Add an attribute
     * @param string $name
     * @param string $value
     * @return $this
     */
    public function addAttrib($name, $value)
    {
        $this->_attribs[strval($name)] = $value;
        return $this;
    }

    /**
     * Gat all Attribs
     * @return array
     */
    public function getAttribs()
    {
        return $this->_attribs;
    }

    /**
     * Set the form values
     * @param array $values
     * @return Form
     */
    public function setValues(array $values = array())
    {
        $this->_values = $values;
        return $this;
    }

    /**
     * Return a value by name or a fallback
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function getValue($name, $default = null)
    {
        if (array_key_exists(strval($name), $this->_values)) {
            return $this->_values[strval($name)];
        }
        return $default;
    }

    /**
     * Get an array of all form values
     * @return array
     */
    public function getValues()
    {
        return $this->_values;
    }

    /**
     * Add an Element to a Form
     * @param Element $elem
     * @return Form
     */
    public function addElement(Element $elem)
    {
        if ($elem->type == 'hidden') {
            $this->_invisibleElements[] = $elem;
        } else {
            $this->_elements[] = $elem;
        }
        return $this;
    }

    /**
     * Return all visible Elements
     * @return array
     */
    public function getElements()
    {
        return $this->_elements;
    }

    /**
     * Return all invisible Elements
     * @return array
     */
    public function getInvisibleElements()
    {
        return $this->_invisibleElements;
    }

    /**
     * Validate all Elements
     * @param array $values
     * @return bool
     */
    public function validate(array $values)
    {
        $valid = true;
        foreach ($this->_elements as $elem) {
            /** @var Element $elem **/
            if (array_key_exists($elem->getName(), $values) && $elem->validate($values[$elem->getName()], $values) === false) {
                $valid = false;
            }
        }
        return $valid;
    }

    /**
     * Translate a value
     * @param string $key
     * @return string
     */
    public function _($key)
    {
        if (is_null(FrontController::getInstance()->getPlugin('translate'))) {
            return $key;
        }
        return FrontController::getInstance()->getPlugin('translate')->_($key);
    }

    /**
     * Render the form
     * @return string
     */
    public function render()
    {
        return $this->_decorator->render();
    }

    /**
     * Render the form
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * Set the Default value for an element
     * @param Element $elem
     */
    public function setDefaultValue(Element $elem)
    {
        if(array_key_exists($elem->getName(), $this->_values)) {
            if($elem instanceof Element\Password) {

            } elseif($elem instanceof Element\Checkbox) {
                $elem->setChecked(true);
            }
            else {
                $elem->setValue($this->_values[$elem->getName()]);
            }
        }
    }
}