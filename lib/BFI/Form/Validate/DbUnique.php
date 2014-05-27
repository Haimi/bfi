<?php

namespace BFI\Form\Validate;

use BFI\Db\Table;
use BFI\Form\Validate;

class DbUnique extends Validate
{
    /**
     * Error message templates
     * @var array
     */
    public $errorTemplates = array(
        'not_unique' => 'Der Eintrag ist bereits in der Datenbank vorhanden!'
    );

    /**
     * @var Table
     */
    protected $_table = null;

    /**
     * @var string
     */
    protected $_col = null;

    /**
     * Additional conditions
     * @var array
     */
    protected $_conditions = array();

    /**
     * C'tor
     * @param Table $table
     * @param string $col
     */
    public function __construct(Table $table, $col, array $conditions = array())
    {
        $this->_table = $table;
        $this->_col = $col;
        $this->_conditions = $conditions;
    }

    /**
     * Validate an Element
     * @param mixed $value
     * @param array $context
     * @return bool
     */
    public function validate($value, array $context = array())
    {
        $sql = $this->_table->select()->where('`' . $this->_col . '` = ?', $value);
        foreach ($this->_conditions as $cond) {
            $sql->where($cond);
        }
        if ($this->_table->count($sql) > 0) {
            $this->_errors[] = $this->errorTemplates['not_unique'];
            return false;
        }
        return true;
    }
}