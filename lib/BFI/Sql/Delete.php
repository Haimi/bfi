<?php

namespace BFI\Sql;


class Delete extends Query
{
    /**
     * C'tor
     * Prepare delete statement from table
     * @param string $table
     */
    public function __construct($table)
    {
        $this->_tables = $this->quoteIdentifier($table);
    }

    /**
     * Assemble Query
     * @return string
     */
    public function assemble()
    {
        if (is_array($this->_tables)) {
            reset($this->_tables);
            $this->_tables = current($this->_tables);
        }
        return 'DELETE FROM ' . $this->_tables
        . $this->_assembleConditions()
        . $this->_assembleLimit();
    }

}