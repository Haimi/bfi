<?php

namespace BFI\Form\Decorator\Form;

use BFI\Form\Decorator;
use BFI\Form\Element;
use BFI\Form\Form;

/**
 * Class Table
 * @package BFI\Form\Decorator
 */
class Table extends Decorator
{
    /**
     * @var Form
     */
    protected $_form = null;

    /**
     * C'tor
     * @param Form $form
     */
    public function __construct(Form $form)
    {
        $this->_form = $form;
    }

    /**
     * Render the Element
     * @return string
     */
    public function render()
    {
        // Render hidden elements
        $invisibleElems = '';
        foreach ($this->_form->getInvisibleElements() as $elem) {
            /** @var Element $elem */
            $this->_form->setDefaultValue($elem);
            $invisibleElems .= $elem->render();
        }
        // Render visible Elements to table rows
        $visibleElements = '';
        foreach ($this->_form->getElements() as $elem) {
            $this->_form->setDefaultValue($elem);
            if (array_key_exists('BFI\Form\Decorator\Label', $elem->getDecorators()) &&
                $elem->getDecorator('BFI\Form\Decorator\Label')->isEnabled()) {
                $visibleElements .= $this->_buildTag('tr', array(),
                    $this->_buildTag('td', array(),
                        $elem->getDecorator('BFI\Form\Decorator\Label')->render()
                    ) .
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
        $formAttribs = array_merge($this->_form->getAttribs(), array(
            'name' => $this->_form->getName(),
            'action' => sprintf('/%s/%s/', $this->_form->getController(), $this->_form->getAction()),
            'method' => $this->_form->getMethod()
        ));
        $tableAttribs = array(
            'width' => '100%',
            'border' => '0',
            'cellspacing' => '0',
            'cellpadding' => '0'
        );
        return $this->_buildTag('form', $formAttribs,
            $invisibleElems .
            $this->_buildTag('table', $tableAttribs, $visibleElements)
        );
    }
} 