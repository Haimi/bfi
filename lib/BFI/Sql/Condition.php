<?php

namespace BFI\Sql;

/**
 * Class Condition
 * @package BFI\Sql
 */
class Condition
{
    const LINK_AND = 0;
    const LINK_OR = 1;

    /**
     * @var string
     */
    protected $_conditionString = '';

    /**
     * Type of the link
     * @var int
     */
    protected $_linkType = self::LINK_AND;

    /**
     * C'tor
     * @param string $conditionString
     * @param array $values
     */
    public function __construct($conditionString, $linkType = self::LINK_AND)
    {
        $this->_conditionString = strval($conditionString);
        $this->_linkType = $linkType;
    }

    /**
     * Get the Condition String
     * @return string
     */
    public function getConditionString()
    {
        return $this->_conditionString;
    }

    /**
     * Get the link type
     * @return int
     */
    public function getLinkType()
    {
        return $this->_linkType;
    }

    /**
     * tostring of the Statement
     * @return string
     */
    public function __toString()
    {
        return $this->getConditionString();
    }
}