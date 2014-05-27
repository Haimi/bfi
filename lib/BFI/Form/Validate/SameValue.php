<?php

namespace BFI\Form\Validate;

use BFI\Form\Validate;

class SameValue extends Validate
{
    /**
     * Error message templates
     * @var array
     */
    public $errorTemplates = array(
        'not_same' => 'Die Werte stimmen nicht überein',
        'no_other_value' => 'Das Vergleichsfeld wurde nicht gefunden'
    );

    /**
     * Other field name
     * @var string
     */
    protected $_otherField = null;

    /**
     * C'tor
     * @param string $otherFieldName
     */
    public function __construct($otherFieldName)
    {
        $this->_otherField = strval($otherFieldName);
    }

    /**
     * Validate an Element
     * @param mixed $value
     * @param array $context
     * @return bool
     */
    public function validate($value, array $context = array())
    {
        if (! array_key_exists($this->_otherField, $context)) {
            $this->_errors[] = $this->errorTemplates['no_other_value'];
            return false;
        }
        if (strcmp($value, $context[$this->_otherField]) !== 0) {
            $this->_errors[] = $this->errorTemplates['not_same'];
            return false;
        }
        return true;
    }
}