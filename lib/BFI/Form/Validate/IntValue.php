<?php

namespace BFI\Form\Validate;

use BFI\Form\Validate;

class IntValue extends Validate
{
    const INT_GREATER = '>';
    const INT_SMALLER = '<';

    /**
     * Error message templates
     * @var array
     */
    public $errorTemplates = array(
        'too_small' => '%d ist zu klein!',
        'too_big' => '%d ist zu groß!'
    );

    /**
     * NUmber to check
     * @var int
     */
    protected $_cmp = 0;

    /**
     * Comparision type
     * @var string
     */
    protected $_type = null;

    /**
     * C'tor
     * @param int $cmp
     * @param string $type
     */
    public function __construct($cmp = 0, $type = self::INT_GREATER)
    {
        $this->_cmp = $cmp;
        $this->_type = $type;
    }

    /**
     * Validate an Element
     * @param mixed $value
     * @param array $context
     * @return bool
     */
    public function validate($value, array $context = array())
    {
        if ($this->_type === self::INT_GREATER && intval($value) <= $this->_cmp) {
            $this->_errors[] = sprintf($this->errorTemplates['too_small'], $value);
            return false;
        }
        if ($this->_type === self::INT_SMALLER && intval($value) >= $this->_cmp) {
            $this->_errors[] = sprintf($this->errorTemplates['too_big'], $value);
            return false;
        }
        return true;
    }
}