<?php

namespace BFI\Form\Validate;

use BFI\Form\Validate;

class Email extends Validate
{
    //const EMAIL_REGEX = '/^(([A-Za-z0-9!#$%&\'*+\\/=?^_`{|}~-][A-Za-z0-9!#$%&\'*+\\/=?^_`{|}~\.-]{0,63})|("[^(\|")]{0,62}"))$/i';
    const EMAIL_REGEX = '/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,10}$/i';
    /**
     * Error message templates
     * @var array
     */
    public $errorTemplates = array(
        'invalid_email' => 'Die angegeben Emailadresse ist ungültig!'
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
        if (! preg_match(self::EMAIL_REGEX, $value)) {
            $this->_errors[] = $this->errorTemplates['invalid_email'];
            return false;
        }
        return true;
    }
}