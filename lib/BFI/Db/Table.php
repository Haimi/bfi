<?php

namespace BFI\Db;

use BFI\Cache\CacheInterface;
use BFI\Exception;
use BFI\Sql\Condition;
use BFI\Sql\Delete;
use BFI\Sql\Insert;
use BFI\Sql\Query;
use BFI\Sql\Select;
use BFI\Sql\Update;

abstract class Table
{
    //////////////// Abstract Singleton START
    /**
     * Array of all model instances
     * @var array
     */
    protected static $_instances = array();

    /**
     * Get a model instance
     * @return $this
     */
    public static function getInstance()
    {
        $className = get_called_class();
        if (! array_key_exists($className, self::$_instances)) {
            self::$_instances[$className] = new $className();
        }
        return self::$_instances[$className];
    }

    /**
     * @var \PDO
     */
    protected $_db;

    /**
     * Reset all model instances
     */
    public static function resetInstances()
    {
        self::$_instances = array();
    }

    /**
     * Singleton: forbid C'tor
     */
    protected function __construct()
    {
        $this->_db = Adapter::$defaultConnection;
    }

    /**
     * Singleton: forbid Cloning
     */
    protected function __clone()
    {}
    //////////////// Abstract Singleton END


    /**
     * Table name
     * @var string
     */
    protected $_name;

    /**
     * Primary key of table
     * @var string|array
     */
    protected $_primary;

    /**
     * @var CacheInterface
     */
    public static $_cache = null;

    /**
     * Create a select statement from this table
     * @param array $columns
     * @return Select
     */
    public function select(array $columns = array('*'))
    {
        $select = new Select($columns);
        $select->from($this->_name);
        return $select;
    }

    /**
     * Get Cache Key
     * @param string $class
     * @param string $method
     * @param array $arguments
     * @return string
     */
    public function getCacheKey($class, $method, array $arguments = array())
    {
        $class = preg_replace('/[^a-z0-9_-]/i', '_', $class);
        return $class . '_' . $method . '_' . implode('|', $arguments);
    }

    /**
     * Formats an array to index by one column
     * @param string $indexCol
     * @param array $array
     * @return array
     * @throws \BFI\Exception
     */
    public function indexArray($indexCol, array $array)
    {
        if (! is_string($indexCol) || ! is_array($array) || ! is_array($array[0])) {
            throw new Exception('Invalid Data provided');
        }
        if (! array_key_exists($indexCol, $array)) {
            throw new Exception('Invalid Column name provided');
        }
        $newArray = array();
        foreach ($array as $row) {
            $newArray[$array[$indexCol]] = $row;
        }
        return $newArray;
    }

    /**
     * Call virtual Methods
     * @param string $method
     * @param array $arguments
     * @return mixed
     * @throws \BFI\Exception
     */
    public function __call($method, array $arguments)
    {
        $baseMethod = substr($method, 0, -6);
        if (substr($method, -6) === 'Cached') {
            if (! method_exists($this, $baseMethod)) {
               throw new Exception('Method "' . $baseMethod . '" is not a valid Method.');
            }
            $cacheKey = $this->getCacheKey(get_called_class(), $baseMethod, $arguments);
            if (! is_null(self::$_cache->get($cacheKey))) {
                return unserialize(self::$_cache->get($cacheKey));
            }
            $result = call_user_func_array(array($this, $baseMethod), $arguments);
            self::$_cache->set($cacheKey, serialize($result));
            return $result;
        }
        return null;
    }

    /**
     * Remove a value from the cache
     * @param string $class
     * @param string $method
     * @param array $arguments
     */
    public function clearCache($class, $method, array $arguments = array())
    {
        self::$_cache->remove($this->getCacheKey($class, $method, $arguments));
    }

    /**
     * Create new insert statement into this table
     * @return Insert
     */
    public function insert()
    {
        return new Insert($this->_name);
    }

    /**
     * Create new update statement on this table
     * @return Update
     */
    public function update()
    {
        return new Update($this->_name);
    }

    /**
     * Create new delete statement on this table
     * @return Delete
     */
    public function delete()
    {
        return new Delete($this->_name);
    }

    /**
     * Get Data by constraints
     * @param array $constraints
     * @param bool $singleRow
     * @return array
     */
    public function getData(array $constraints, $singleRow = false)
    {
        $select = $this->select();
        foreach ($constraints as $key => $value) {
            $select->where($select->quoteIdentifier($key) . ' = ?', $value);
        }
        if ($singleRow === false) {
            return $this->fetchAll($select);
        }
        return $this->getRow($select);
    }

    /**
     * Get all rows
     * @return array
     */
    public function getAll()
    {
        $select = $this->select();
        return $this->fetchAll($select);
    }

    /**
     * Fetch all rows of a query
     * @param Query $query
     * @return array
     */
    public function fetchAll(Query $query)
    {
        $qry = $query->query();
        $qry->execute();
        return $qry->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get one col of a query
     * @param Query $query
     * @param int $num
     * @return string
     */
    public function getCol(Query $query, $num = 0)
    {
        $qry = $query->query();
        $qry->execute();
        return $qry->fetchColumn(intval($num));
    }

    /**
     * Get one Row of a query result
     * @param Query $query
     * @return array|null
     */
    public function getRow(Query $query)
    {
        $qry = $query->query();
        $qry->execute();
        return $qry->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Count entries in DB
     * @param Condition|Select|string $cond
     * @param mixed $param
     * @return int
     */
    public function count($cond = null, $param = null)
    {
        if ($cond instanceof Select) {
            $stmt = $cond;
        } else {
            $stmt = $this->select();
            if (! is_null($cond)) {
                if ($cond instanceof Condition) {
                    $stmt->where($cond);
                } elseif (is_string($cond) && ! is_null($param)) {
                    $stmt->where($cond, $param);
                }
            }
        }
        $qry = $stmt->query();
        $qry->execute();
        return $qry->rowCount();
    }

    /**
     * Get a Row by primary
     * @param mixed $primaryVal
     * @return array|null
     */
    public function get($primaryVal)
    {
        $qry = $this->select();
        if (is_array($primaryVal)) {
            foreach ($this->_primary as $key) {
                if (array_key_exists($key, $primaryVal)) {
                    $qry->where('`' . $key . '` = ?', $primaryVal[$key]);
                }
            }
        } else {
            $qry->where('`' . $this->_primary[0] . '` = ?', $primaryVal);
        }
        return $this->getRow($qry);
    }

    /**
     * Wrap for fulltext
     * @param string $val
     * @return string
     */
    protected function _wrapLike($val)
    {
        return '%' . $val . '%';
    }
}