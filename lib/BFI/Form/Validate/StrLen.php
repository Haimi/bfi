<?php

namespace BFI\Form\Validate;

use BFI\Form\Validate;

class StrLen extends Validate
{
    /**
     * Error message templates
     * @var array
     */
    public $errorTemplates = array(
        'too_short' => 'Mindestens %d Zeichen!',
        'too_long' => 'Maximal %d Zeichen!'
    );

    /**
     * Minimal length
     * @var int
     */
    protected $_lenMin = 0;

    /**
     * Maximal length
     * @var int
     */
    protected $_lenMax = null;

    /**
     * C'tor
     * @param int $min
     * @param int $max
     */
    public function __construct($min = 0, $max = null)
    {
        $this->_lenMin = intval($min);
        if (!is_null($max)) {
            $this->_lenMax = intval($max);
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
        $len = mb_strlen($value);
        if ($len < $this->_lenMin) {
            $this->_errors[] = sprintf($this->errorTemplates['too_short'], $this->_lenMin);
            return false;
        }
        if (! is_null($this->_lenMax) && $len > $this->_lenMax) {
            $this->_errors[] = sprintf($this->errorTemplates['too_long'], $this->_lenMax);
            return false;
        }
        return true;
    }
}