<?php

namespace BFI\Sql;


class Insert extends Query
{
    /**
     * The Data array to be inserted
     * @var array
     */
    protected $_data = array();

    /**
     * C'tor
     * set the table to be inserted into
     * @param string $table
     */
    public function __construct($table)
    {
        $this->into($table);
    }

    /**
     * Add a row of data
     * @param array $row
     * @return Insert
     */
    public function addRow(array $row)
    {
        $this->_data[] = $row;
        return $this;
    }

    /**
     * Set table to insert into
     * @param string $table
     * @return Insert
     */
    public function into($table)
    {
        $this->_tables = $this->quoteIdentifier($table);
        return $this;
    }

    /**
     * Assemble Query
     * @return string
     */
    public function assemble()
    {
        return 'INSERT INTO ' . $this->_tables
        . $this->_assembleDataValues()
        . $this->_assembleLimit();
    }

    /**
     * Assemble columns and Data
     * @return string
     */
    protected function _assembleDataValues()
    {
        $columns = array();
        $rows = array();
        foreach ($this->_data as $row) {
            $columns = array_unique(array_merge($columns, array_keys($row)));
        }
        foreach ($this->_data as $row) {
            $insertRow = array();
            foreach ($columns as $col) {
                if (! array_key_exists($col, $row)) {
                    $insertRow[$col] = $this->quote(null);
                } else {
                    $insertRow[$col] = $this->quote($row[$col]);
                }
            }
            $rows[] = implode(', ', $insertRow);
        }
        $columns = array_map(array($this, 'quoteIdentifier'), $columns);
        return '(' . implode(', ', $columns) . ') VALUES (' . implode('), (', $rows) . ')';
    }
}