<?php

namespace BFI\Sql;

/**
 * Class Expression
 * @package BFI\Sql
 */
class Expression
{
    /**
     * @var string
     */
    protected $_expression = null;

    /**
     * C'tor
     * @param string $expression
     */
    public function __construct($expression)
    {
        $this->_expression = strval($expression);
    }

    /**
     * Get the expression
     * @return string
     */
    public function __toString()
    {
        return $this->_expression;
    }
}