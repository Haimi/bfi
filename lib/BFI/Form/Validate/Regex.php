<?php

namespace BFI\Form\Validate;

use BFI\Form\Validate;

class Regex extends Validate
{
    protected $_regex = '/.*/i';

    /**
     * Error message templates
     * @var array
     */
    public $errorTemplates = array(
        'invalid_string' => 'Die angegebene Zeichenkette enthält ungültige Zeichen!'
    );

    public function __construct($regex = null)
    {
        $regex = strval($regex);
        if (mb_strlen($regex) > 0) {
            $this->_regex = $regex;
        }
    }

    /**
     * Validate an Element
     * @param mixed $value
     * @param array $context
     * @return bool
     */
    public function validate($value, array $context = array())
    {
        // Value only present if checkbox has been activated
        if (! preg_match($this->_regex, $value)) {
            $this->_errors[] = $this->errorTemplates['invalid_string'];
            return false;
        }
        return true;
    }
}