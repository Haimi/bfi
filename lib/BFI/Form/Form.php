<?php

namespace BFI\Form;

use BFI\FrontController;
use BFI\Form\Element;
use BFI\Html\Tag;

/**
 * Class Form
 * @package BFI\Form
 */
class Form extends Tag
{
    const METHOD_POST = 'POST';
    const METHOD_GET = 'GET';

    /**
     * Use table to render a form
     * @var string
     */
    private $_formTplType = 'table';

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
     * @todo call the decorator
     * @return string
     */
    public function render()
    {
        $renderMethod = '_render' . ucfirst($this->_formTplType);
        return call_user_func(array($this, $renderMethod));
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
     * @param Element $elem
     */
    protected function _setDefaultValue(Element $elem)
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

    /**
     * Render the form as a table
     * @return string
     */
    protected function _renderTable()
    {
        // TODO: Auslagern in Decorator?
        // Render hidden elements
        $invisibleElems = '';
        foreach ($this->_invisibleElements as $elem) {
            /** @var Element $elem */
            $this->_setDefaultValue($elem);
            $invisibleElems .= $elem->render();
        }
        // Render visible Elements to table rows
        $visibleElements = '';
        foreach ($this->_elements as $elem) {
            $this->_setDefaultValue($elem);
            if ($elem->type !== 'html') {
                $visibleElements .= $this->_buildTag('tr', array(),
                    $this->_buildTag('td', array(),
                        $this->_buildTag('label', array(), $this->_($elem->getLabel()))
                    )
                    .
                    $this->_buildTag('td', array(),
                        $elem->render()
                    )
                );
            } else {
                $visibleElements .= $this->_buildTag('tr', array(),
                    $this->_buildTag('td', array('colspan' => 2),
                        $elem->render()
                    )
                );
            }
        }
        // Build form
        $formAttribs = array_merge($this->_attribs, array(
            'name' => $this->_name,
            'action' => sprintf('/%s/%s/', $this->_controller, $this->_action),
            'method' => $this->_method
        ));
        $tableAttribs = array(
            'width' => '100%',
            'border' => '0',
            'cellspacing' => '0',
            'cellpadding' => '0'
        );
        return $this->_buildTag('form', $formAttribs,
            $invisibleElems
            .
            $this->_buildTag('table', $tableAttribs, $visibleElements)
        );
    }
}