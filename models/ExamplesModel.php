<?php

/**
 * Class ExamplesModel
 */
class ExamplesModel extends \BFI\Db\Table implements \BFI\Auth\ITable
{
    /**
     * C'tor
     */
    public function __construct()
    {
        parent::__construct();
        $this->_name = 'examples';
        $this->_primary = array('examples_id');
    }

    /**
     * Get the id by email adress
     * @param string $email
     * @return int
     */
    public function getIdByEmail($email)
    {
        $qry = $this->select(array($this->_primary[0]))
            ->where('`email_address` = ?', $email);
        return intval($this->getCol($qry));
    }
} 
