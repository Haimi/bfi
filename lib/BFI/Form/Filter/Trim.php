<?php

namespace BFI\Form\Filter;

use BFI\Form\Filter;

class Trim extends Filter
{
    /**
     * List of Characters to trim
     * @var array
     */
    protected $_charlist = array(
        '\x20', // Space
        '\x09', // Tab
        '\x0A', // Line Feed
        '\x0D', // Carriage Return
        '\x00', // NUL-Byte
        '\x0B', // Vertical Tab
    );

    /**
     * C'tor
     * @param array $charlist
     */
    public function __construct(array $charlist = array())
    {
        $this->_charlist = array_merge($this->_charlist, $charlist);
    }

    /**
     * Trim a value
     * @param string $value
     * @return string
     */
    public function filter($value)
    {
        return trim($value, implode('', $this->_charlist));
    }
}