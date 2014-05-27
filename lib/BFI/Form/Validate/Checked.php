<?php

namespace BFI\Form\Validate;

use BFI\Form\Validate;

class Checked extends Validate
{
    /**
     * Error message templates
     * @var array
     */
    public $errorTemplates = array(
        'not_checked' => 'Bitte setze den Haken!'
    );

    /**
     * Validate an Element
     * @param mixed $value
     * @param array $context
     * @return bool
     */
    public function validate($value, array $context = array())
    {
        // Value only present if checkbox has been activated
        if (is_null($value)) {
            $this->_errors[] = $this->errorTemplates['not_checked'];
            return false;
        }
        return true;
    }
}