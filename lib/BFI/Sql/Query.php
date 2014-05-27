<?php

namespace BFI\Sql;

use BFI\Db\Adapter;

/**
 * Class Query
 * @package BFI\Sql
 */
abstract class Query
{
    /**
     * The conditions
     * @var array
     */
    protected $_conditions = array();

    /**
     * The having conditions
     * @var array
     */
    protected $_havingConditions = array();

    /**
     * The tables to query on
     * @var array
     */
    protected $_tables = array();

    /**
     * Limit start value
     * @var int
     */
    protected $_limitStart = 0;

    /**
     * Limit count
     * @var int
     */
    protected $_limitCount = null;

    /**
     * Add a where statement with AND-link
     * @param string $cond
     * @param mixed $value
     * @return $this
     */
    public function where($cond, $value = null)
    {
        $this->_conditions[] = $this->_buildCondition($cond, $value, Condition::LINK_AND);
        return $this;
    }

    /**
     * Add a where statement with OR-link
     * @param string $cond
     * @param mixed $value
     * @return $this
     */
    public function orWhere($cond, $value = null)
    {
        $this->_conditions[] = $this->_buildCondition($cond, $value, Condition::LINK_OR);
        return $this;
    }

    /**
     * Add a having statement with AND-Link
     * @param string $cond
     * @param mixed $value
     * @return $this
     */
    public function having($cond, $value = null)
    {
        $this->_havingConditions[] = $this->_buildCondition($cond, $value, Condition::LINK_AND);
        return $this;
    }

    /**
     * Add a table to select from
     * @param string|array $table
     * @return $this
     */
    public function from($table)
    {
        if (is_array($table)) {
            $tableName = current($table);
            if (array_key_exists($tableName, $this->_tables) && $this->_tables[$tableName] == $tableName) {
                unset($this->_tables[$tableName]);
            }
            $this->_tables[key($table)] = current($table); // With alias
        } else {
            if (array_key_exists($table, $this->_tables) && $this->_tables[$table] == $table) {
                unset($this->_tables[$table]);
            }
            $this->_tables[$table] = $table; // Fake alias
        }
        return $this;
    }

    /**
     * Set limits
     * @param int $count
     * @param int $start
     * @return $this
     */
    public function limit($count, $start = 0)
    {
        $this->_limitCount = intval($count);
        $this->_limitStart = intval($start);
        return $this;
    }

    /**
     * Build a condition
     * @param string $cond
     * @param mixed $value
     * @param int $linkType
     * @return Condition
     */
    protected function _buildCondition($cond, $value = null, $linkType = Condition::LINK_AND)
    {
        if ($cond instanceof Condition) {
            return $cond;
        }
        if (! is_null($value)) {
            $cond = $this->quoteInto($cond, $value);
        }
        return new Condition($cond, $linkType);
    }

    /**
     * Quote a value into a string
     * @param string $str
     * @param mixed $value
     * @return string
     */
    public function quoteInto($str, $value)
    {
        return str_replace('?', $this->quote($value), $str);
    }

    /**
     * Quote a value
     * @param mixed $value
     * @return string
     */
    public function quote($value)
    {
        if ($value instanceof Expression) {
            return $value->__toString();
        }
        $type = \PDO::PARAM_STR;
        if (is_null($value)) {
            return 'NULL'; // PDO quoting does not work
        } elseif (is_numeric($value)) {
            $type = \PDO::PARAM_INT;
        }
        return Adapter::$defaultConnection->quote($value, $type);
    }

    /**
     * Quote identifiers
     * @param string $ident
     * @return string
     */
    public function quoteIdentifier($ident)
    {
        if (strpos($ident, '.') !== false) {
            $ident = explode('.', $ident);
        } else {
            $ident = array($ident);
        }
        foreach ($ident as &$id) {
            if ($id != '*' && strpos($id, '`') !== 0) {
                $id = '`' . $id . '`';
            }
        }
        return implode('.', $ident);
    }

    /**
     * Assemble query
     * @return string
     */
    public function __toString()
    {
        return $this->assemble();
    }

    /**
     * Assemble query
     * @return string
     */
    abstract public function assemble();

    /**
     * Assemble the conditions
     * @return string
     */
    protected function _assembleConditions()
    {
        $condStr = '';
        if (count($this->_conditions) > 0) {
            $condStr = ' WHERE ';
            foreach ($this->_conditions as $key => $cond) {
                if ($key > 0) {
                    if ($cond->getLinkType() == Condition::LINK_AND) {
                        $condStr .= ' AND ';
                    } else {
                        $condStr .= ' OR ';
                    }
                }
                $condStr .= $cond->getConditionString();
            }
        }
        return $condStr;
    }

    /**
     * Assemble the having conditions
     * @return string
     */
    protected function _assembleHaving()
    {
        $condStr = '';
        if (count($this->_havingConditions) > 0) {
            $condStr = ' Having ';
            foreach ($this->_havingConditions as $key => $cond) {
                if ($key > 0) {
                    if ($cond->getLinkType() == Condition::LINK_AND) {
                        $condStr .= ' AND ';
                    } else {
                        $condStr .= ' OR ';
                    }
                }
                $condStr .= $cond->getConditionString();
            }
        }
        return $condStr;
    }
    /**
     * Assemble the Limit Statement
     * @return string
     */
    protected function _assembleLimit()
    {
        $limitStr = '';
        if (! is_null($this->_limitCount)) {
            $limitStr = ' LIMIT ';
            if ($this->_limitStart > 0) {
                $limitStr .= $this->_limitStart . ', ';
            }
            $limitStr .= $this->_limitCount;
        }
        return $limitStr;
    }

    /**
     * Execute query
     * @return int
     */
    public function execute()
    {
        $query = $this->assemble();
        return Adapter::$defaultConnection->exec($query);
    }

    /**
     * Prepare the statement
     * @return \PDOStatement
     */
    public function query()
    {
        $query = $this->assemble();
        return Adapter::$defaultConnection->prepare($query);
    }
}