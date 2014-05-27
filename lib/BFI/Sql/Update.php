<?php

namespace BFI\Sql;

/**
 * Class Update
 * @package BFI\Sql
 */
class Update extends Query
{
    /**
     * The Data array to be updated
     * @var array
     */
    protected $_data = array();

    /**
     * C'tor
     * Set table to be updated
     * @param string $table
     */
    public function __construct($table)
    {
        $this->_tables = $this->quoteIdentifier($table);
    }

    /**
     * Set Value to be updated
     * @param string $key
     * @param mixed $value
     * @return Update
     */
    public function set($key, $value)
    {
        if (! $value instanceof Expression) {
            $value = $this->quote($value);
        }
        $this->_data[$this->quoteIdentifier($key)] = $value;
        return $this;
    }

    /**
     * Assemble Query
     * @return string
     */
    public function assemble()
    {
        return 'UPDATE ' . $this->_tables
        . $this->_assembleData()
        . $this->_assembleConditions()
        . $this->_assembleLimit();
    }

    /**
     * Assemble the data to set
     * @return string
     */
    protected function _assembleData()
    {
        $updateStr = ' SET ';
        $updates = array();
        foreach ($this->_data as $key => $value) {
            $updates[] = $key . ' = ' . $value;
        }
        return $updateStr . implode(', ', $updates);
    }
}