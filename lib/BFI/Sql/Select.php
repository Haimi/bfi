<?php

namespace BFI\Sql;

use BFI\Db\Adapter;

/**
 * Class Select
 * @package BFI\Sql
 */
class Select extends Query
{
    /**
     * Join Types
     */
    const JOIN_INNER = 1;
    const JOIN_OUTER = 2;
    const JOIN_LEFT = 4;
    const JOIN_RIGHT = 8;
    const JOIN_CROSS = 16;
    const JOIN_NATURAL = 32;

    /**
     * Order Types
     */
    const ORDER_ASC = 'ASC';
    const ORDER_DESC = 'DESC';

    /**
     * All allowed join combinations
     * @var array
     */
    public static $ALLOWED_JOINS = array();

    /**
     * The Columns to select
     * @var array
     */
    protected $_columns = array();

    /**
     * The array of joins
     * @var array
     */
    protected $_joins = array();

    /**
     * Array of orders
     * @var array
     */
    protected $_orders = array();

    /**
     * Array of groupings
     * @var array
     */
    protected $_groups = array();

    /**
     * C'tor
     * @param array $columns
     */
    public function __construct(array $columns = array('*'))
    {
        $this->columns($columns);

        self::$ALLOWED_JOINS = array(
            self::JOIN_INNER,
            self::JOIN_LEFT,
            self::JOIN_RIGHT,
            self::JOIN_CROSS,
            self::JOIN_NATURAL,
            self::JOIN_LEFT | self::JOIN_OUTER,
            self::JOIN_NATURAL | self::JOIN_LEFT,
            self::JOIN_NATURAL | self::JOIN_LEFT | self::JOIN_OUTER,
            self::JOIN_RIGHT | self::JOIN_OUTER,
            self::JOIN_NATURAL | self::JOIN_RIGHT,
            self::JOIN_NATURAL | self::JOIN_RIGHT | self::JOIN_OUTER,
        );
    }

    /**
     * Set all Columns
     * @param array $columns
     * @return Select
     */
    public function columns(array $columns)
    {
        if (current($this->_columns) == '*') {
            $this->_columns = array();
        }
        foreach ($columns as $key => $value) {
            if (is_string($key)) {
                $this->addColumn(array($key => $value));
            } else {
                $this->addColumn($value);
            }
        }
        return $this;
    }

    /**
     * @param string|array $column
     * @return Select
     */
    public function addColumn($column)
    {
        if (is_array($column)) {
            $this->_columns[key($column)] = current($column); // With alias
        } else {
            $this->_columns[strval($column)] = $column; // Fake alias
        }
        return $this;
    }

    /**
     * Perform a default (INNER) join
     * @param string|array $table
     * @param string|Condition $cond
     * @return Select
     */
    public function join($table, $cond)
    {
        return $this->buildJoin($table, $cond, self::JOIN_INNER);
    }

    /**
     * Perform an OUTER LEFT join
     * @param string|array $table
     * @param string|Condition $cond
     * @return Select
     */
    public function joinLeft($table, $cond)
    {
        return $this->buildJoin($table, $cond, self::JOIN_OUTER | self::JOIN_LEFT);
    }

    /**
     * Perform a custom join
     * @param string|array $table
     * @param string|Condition $cond
     * @param int $type
     * @return Select
     */
    public function buildJoin($table, $cond, $type)
    {
        if (is_array($table)) {
            $alias = key($table);
            $table = current($table);
        } else {
            $alias = $table;
        }

        $this->_joins[$alias] = array(
            'table' => $table,
            'cond' => $cond,
            'type' => $type
        );
        return $this;
    }

    /**
     * Add an order statement
     * @param string $col
     * @param string $dir
     * @return Select
     */
    public function order($col, $dir = self::ORDER_ASC)
    {
        $this->_orders[$this->quoteIdentifier($col)] = $dir;
        return $this;
    }

    /**
     * Add a grouping statement
     * @param string $col
     * @return Select
     */
    public function group($col)
    {
        if (!$col instanceof Expression) {
            $this->_groups[] = $this->quoteIdentifier($col);
        } else {
            $this->_groups[] = $col->__toString();
        }
        return $this;
    }

    /**
     * Assemble Query
     * @return string
     */
    public function assemble()
    {
        return 'SELECT ' . $this->_assembleColumns()
        . ' FROM ' . $this->_assembleFrom()
        . $this->_assembleJoins()
        . $this->_assembleConditions()
        . $this->_assembleGroup()
        . $this->_assembleHaving()
        . $this->_assembleOrder()
        . $this->_assembleLimit();
    }

    /**
     * Build a String of all columns
     * @return string
     */
    protected function _assembleColumns()
    {
        $colArr = array();
        foreach ($this->_columns as $alias => $col) {
            if (!$col instanceof Expression) {
                $colStr = $this->quoteIdentifier($col);
            } else {
                $colStr = $col->__toString();
            }
            if (!is_int($alias) && $alias != $col) {
                $colStr .= ' AS ' . $this->quoteIdentifier($alias);
            }
            $colArr[] = $colStr;
        }
        return implode(', ', $colArr);
    }

    /**
     * Build a String of all tables
     * @return string
     */
    protected function _assembleFrom()
    {
        $colArr = array();
        foreach ($this->_tables as $alias => $tab) {
            $colStr = $this->quoteIdentifier($tab);
            if (!is_int($alias) && $alias != $tab) {
                $colStr .= ' AS ' . $this->quoteIdentifier($alias);
            }
            $colArr[] = $colStr;
        }
        return implode(', ', $colArr);
    }

    /**
     * Assemble the joins
     * @return string
     */
    protected function _assembleJoins()
    {
        $joinStr = '';
        foreach ($this->_joins as $alias => $config) {
            // Join type
            if ($this->_isBitSet(self::JOIN_NATURAL, $config['type'])) {
                $joinStr .= " NATURAL";
            }
            if ($this->_isBitSet(self::JOIN_LEFT, $config['type'])) {
                $joinStr .= " LEFT";
            }
            if ($this->_isBitSet(self::JOIN_RIGHT, $config['type'])) {
                $joinStr .= " RIGHT";
            }
            if ($this->_isBitSet(self::JOIN_OUTER, $config['type'])) {
                $joinStr .= " OUTER";
            }
            if ($this->_isBitSet(self::JOIN_INNER, $config['type'])) {
                $joinStr .= " INNER";
            }
            if ($this->_isBitSet(self::JOIN_CROSS, $config['type'])) {
                $joinStr .= " CROSS";
            }
            // Join table & alias
            $joinStr .= ' JOIN ' . $this->quoteIdentifier($config['table']);
            if ($alias != $config['table']) {
                $joinStr .= ' AS ' . $this->quoteIdentifier($alias);
            }
            // Condition for ON or column name for USING
            if (preg_match('/^[a-z0-9_]+$/i', $config['cond'])) {
                $joinStr .= ' USING (' . $config['cond'] . ')';
            } else {
                $joinStr .= ' ON (' . strval($config['cond']) . ')';
            }
        }
        return $joinStr;
    }

    /**
     * Test if a bit is set
     * @param int $flag
     * @param int $allFlags
     * @return bool
     */
    protected function _isBitSet($flag, $allFlags)
    {
        return ($allFlags & $flag) == $flag;
    }

    /**
     * Assemble order by statements
     * @return string
     */
    protected function _assembleOrder()
    {
        $orderStr = '';
        if (count($this->_orders) > 0) {
            $orderStr = ' ORDER BY ';
            $orderColumns = array();
            foreach ($this->_orders as $order => $dir) {
                $orderColumns[] = $order . ' ' . $dir;
            }
            $orderStr .= implode(', ', $orderColumns);
        }
        return $orderStr;
    }

    /**
     * Assemble group by statements
     * @return string
     */
    protected function _assembleGroup()
    {
        $groupStr = '';
        if (count($this->_groups) > 0) {
            $groupStr = ' GROUP BY ' . implode(', ', $this->_groups);
        }
        return $groupStr;
    }
}