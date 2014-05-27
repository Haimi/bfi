<?php
/**
 * Created by PhpStorm.
 * User: sascha
 * Date: 17.11.13
 * Time: 15:09
 */

namespace BFI\Db;


use BFI\Session;

class Paging
{

    /**
     * Default entries per page
     */
    const ENTRIES_PER_PAGE = 50;

    /**
     * Possible entry selections
     * @var array
     */
    public static $pagingOptions = array(
        25,
        50,
        100,
        200
    );

    /**
     * @var int
     */
    protected $_page = 1;

    /**
     * @var int
     */
    protected $_entriesPerPage = null;

    /**
     * @var string
     */
    protected $_keyword = null;

    /**
     * C'tor
     */
    private function __construct()
    {
        $this->setEntriesPerPage(self::ENTRIES_PER_PAGE);
    }

    /**
     * Get the current paging instance
     * @return Paging
     */
    public static function getInstance()
    {
        if (! Session::exists(__CLASS__)) {
            Session::set(__CLASS__, new self());
        }
        return Session::get(__CLASS__);
    }

    /**
     * @param string $keyword
     */
    public function setKeyword($keyword)
    {
        $this->_keyword = $keyword;
    }

    /**
     * @return string
     */
    public function getKeyword()
    {
        return $this->_keyword;
    }

    /**
     * @return string
     */
    public function getContainsKeyword()
    {
        return '%' . $this->_keyword . '%';
    }

    /**
     * @param int $entriesPerPage
     */
    public function setEntriesPerPage($entriesPerPage)
    {
        $this->_entriesPerPage = $entriesPerPage;
    }

    /**
     * @return int
     */
    public function getEntriesPerPage()
    {
        return $this->_entriesPerPage;
    }

    /**
     * @param int $page
     */
    public function setPage($page)
    {
        $this->_page = $page;
    }

    /**
     * @return int
     */
    public function getPage()
    {
        return $this->_page;
    }

    /**
     * get the limit start
     * @return int
     */
    public function getStart()
    {
        return ($this->_page - 1) * ($this->_entriesPerPage);
    }
}