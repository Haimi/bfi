<?php

namespace BFI\Form\Validate;

use BFI\Form\Validate;
use BFI\Session;

class Captcha extends Validate
{
    /**
     * Error message templates
     * @var array
     */
    public $errorTemplates = array(
        'invalid_captcha' => 'Der Eingegebene Sicherheitscode ist ungültig!',
    );

    /**
     * Name of the captcha input
     * @var string
     */
    protected $_elemName = null;

    /**
     * C'tor
     * @param string $name
     */
    public function __construct($name)
    {
        $this->_elemName = strval($name);
    }

    /**
     * Validate an Element
     * @param mixed $value
     * @param array $context
     * @return bool
     */
    public function validate($value, array $context = array())
    {
        if (strcmp(mb_strtoupper($value), Session::get('captcha')) != 0) {
            $this->_errors[] = $this->errorTemplates['invalid_captcha'];
            return false;
        }
        return true;
    }
}