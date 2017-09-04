<?php
/**
| ----------------------------------------------------------------------------
| Class with responsability of manage and serve the front-end with the data
| of database, the basic CRUD
| ----------------------------------------------------------------------------
* PHP version 7.1
*
* @category Library\Marlon
* @package  Core\DB
* @author   Marlon Cardoso <marlonrcardoso@yahoo.com.br>
* @license  http://opensource.org/licenses/gpl-license.php GNU Public License
*/

namespace Library\Marlon\DB;

use PDO;
use \PDOException;

abstract class Database
{
    private $sql = NULL;
    private $params = [];
    protected $db;
    protected $tableName = NULL;
    protected $className = NULL;
    protected $execute;
    protected $pdoInstance;

    private $_where = [];
    private $_join = [];
    private $_select = NULL;
    private $_order = NULL;
    private $_group = NULL;
    private $_limit = NULL;

    protected abstract function prepareConnection();
    protected abstract function host();
    protected abstract function database();
    protected abstract function username();
    protected abstract function password();

    protected function setInstance(PDO $instance)
    {
        $this->pdoInstance = $instance;
        $this->pdoInstance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    
    /**
    | ----------------------------------------------------------------------------
    | Run the sql query command
    | ----------------------------------------------------------------------------
    * @param string the query string
    * @return bool
    */
    public function query($query)
    {
        return $this->pdoInstance->exec($query);
    }

    /**
    | ----------------------------------------------------------------------------
    | Returns the list of foregn keys of the current table
    | ----------------------------------------------------------------------------
    * @return array
    */
    public function getForeingKeys()
    {
        $this->pdo("
            SELECT 
                kcu.column_name foreign_id, 
                kcu.table_name foreign_table, 
                ccu.table_name main_table, 
                ccu.column_name main_id
            FROM information_schema.table_constraints tc
            JOIN information_schema.key_column_usage AS kcu ON tc.constraint_name = kcu.constraint_name
            JOIN information_schema.constraint_column_usage AS ccu ON ccu.constraint_name = tc.constraint_name
            WHERE tc.table_name = '{$this->tableName}'
            AND constraint_type = 'FOREIGN KEY'
        ", []);

        return $this->execute->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
    | ----------------------------------------------------------------------------
    | Fill the data used in the query, get the list of the fields 
    | and prepare in the pattern of the PDO
    | ----------------------------------------------------------------------------
    * @param array $fields the field of the query
    * @return array
    */
    protected function getFields(array $fields, $compare = '=', $concat = " ")
    {
        $save['sql'] = [];
        $addKey = (strtolower(trim($concat)) == "and" ? "cond" : "comp" );
        
        foreach ($fields as $key => $value)
        {
            $save['sql'][] = "\"{$key}\" {$compare} :{$addKey}_{$key}";
            $save['save'][":{$addKey}_{$key}"] = $value;
        }
        $save['sql'] = implode($concat, $save['sql']);
        return $save;
    }

    /**
    | ----------------------------------------------------------------------------
    | prepare the sql query, store the property of class 'execute'
    | ----------------------------------------------------------------------------
    * @param string $sql the query to be preperared
    * @return void
    */
    protected function pdoPrepare($sql)
    {
        $this->execute = $this->pdoInstance->prepare($sql);
    }

    /**
    | ----------------------------------------------------------------------------
    | prepare fields of the query
    | ----------------------------------------------------------------------------
    * @param array $values the list of field to be binding
    * @return void
    */
    protected function pdoBindValue($values)
    {
        foreach ($values as $k=>$v) 
        {
            $this->execute->bindValue($k, $v);
        }
    }

    /**
    | ----------------------------------------------------------------------------
    | Execute the query with values binding
    | ----------------------------------------------------------------------------
    * @param array $values the list of field
    * @return void
    */
    protected function pdoExecute(Array $values=[])
    {
        return $this->execute->execute($values);
    }

    /**
    | ----------------------------------------------------------------------------
    | Call the helper methods
    | ----------------------------------------------------------------------------
    * @param string $sql the query to be preperared
    * @param array $values the list of field to be binding
    * @return array
    */
    protected function pdo($sql, $values)
    {
        try {
            $this->pdoPrepare($sql);
            $this->pdoBindValue($values);
            return $this->pdoExecute($values);
        } catch (PDOException $err) {
            var_dump(['query' => $sql], $err->getMessage());
        }
    }

    /**
    | ----------------------------------------------------------------------------
    | INSERT INTO query
    | ----------------------------------------------------------------------------
    * @param array $values the list of field
    * @return void
    */
    public function insert(Array $fields)
    {
        $save = $this->getFields($fields);
        
        $fields = implode('","', array_keys($fields));
        $values = implode(',', array_keys($save['save']));
    
        $sql = "INSERT INTO \"{$this->tableName}\" (\"{$fields}\") VALUES({$values});";
        
        $save = $this->pdo($sql, $save['save']);
        
        return ( $save ? $this->pdoInstance->lastInsertId() : FALSE);
    }

    /**
    | ----------------------------------------------------------------------------
    | UPDATE query
    | ----------------------------------------------------------------------------
    * @param array $values the list of field
    * @param array $conditions the list of rules to update
    * @return void
    */
    public function update(Array $fields, Array $conditions)
    {
        $save = $this->getFields($fields, '=', ',');

        $conditions = $this->getFields($conditions, '=', " AND ");

        $save['save'] = array_merge($save['save'], $conditions['save']);

        $sql = "UPDATE \"{$this->tableName}\" SET {$save['sql']} WHERE {$conditions['sql']};";
        
        $save = $this->pdo($sql, $save['save']);
        return ($save ? $this->execute->rowCount() : FALSE);
    }

    /**
    | ----------------------------------------------------------------------------
    | DELETE query
    | ----------------------------------------------------------------------------
    * @param array $conditions the list of rules to update
    * @return void
    */
    public function delete(Array $conditions)
    {
        $save = $this->getFields($conditions, '=', " AND ");

        $sql = "DELETE FROM \"{$this->tableName}\" WHERE {$save['sql']};";

        return $this->pdo($sql, $save['save']);
    }

    /**
    | ----------------------------------------------------------------------------
    | Prepare the sql query
    | ----------------------------------------------------------------------------
    * @return string
    */
    public function prepareQueryBuilder()
    {
        $query = $this->_select;
        $this->_select = NULL;

        if( !empty($this->_join) ){
            $query .= join("\n", $this->_join);
            $this->_join = [];
        }
        
        if( !empty($this->_where) ){
            $query .= join("\n", $this->_where);
            $this->_where = [];
        }

        if( $this->_group != NULL ){
            $query .= $this->_group;
            $this->_group = NULL;
        }
        
        if( $this->_order != NULL ){
            $query .= $this->_order;
            $this->_order = NULL;
        }

        if( $this->_limit != NULL ){
            $query .= $this->_limit;
            $this->_limit = NULL;
        }

        return $query;
    }

    /**
    | ----------------------------------------------------------------------------
    | Execute the query defined
    | ----------------------------------------------------------------------------
    * @return PDO
    */
    private function run()
    {
        $query = $this->prepareQueryBuilder();

        $this->pdo($query, $this->params);

        return $this->execute;
    }

    /**
    | ----------------------------------------------------------------------------
    | GEt all itens founded in the query prepared
    | ----------------------------------------------------------------------------
    * @return array
    */
    public function find($dump = false)
    {
        return $this->run()->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
    | ----------------------------------------------------------------------------
    | Returns one item founded in the query prepared
    | ----------------------------------------------------------------------------
    * @return array
    */
    public function findOne()
    {
        return $this->run()->fetch(PDO::FETCH_ASSOC);
    }

    /**
    | ----------------------------------------------------------------------------
    | Returns one item founded in the query prepared
    | ----------------------------------------------------------------------------
    * @return instance of the classs
    */
    public function findObject()
    {
        return $this->run()->fetchObject($this->className);
    }

    /**
    | ----------------------------------------------------------------------------
    | Returns the total of itens founded
    | ----------------------------------------------------------------------------
    * @return int
    */
    public function count()
    {
        return $this->run()->rowCount();
    }

    /**
    | ----------------------------------------------------------------------------
    | prepare the SELECT rule
    | ----------------------------------------------------------------------------
    * @return Database instance
    */
    public function select($fields = ["*"], $table = NULL)
    {
        $fields = implode(',', $fields);
        $table = (empty($table) ? $this->tableName : $table);
        $this->_select = "SELECT\n{$fields}\nFROM \"{$table}\"";
        
        return $this;
    }

    /**
    | ----------------------------------------------------------------------------
    | prepare the INNER JOIN rule
    | ----------------------------------------------------------------------------
    * @return Database instance
    */
    public function join($table, $conditions)
    {
        $this->_join[] = "INNER JOIN {$table} ON($conditions)";
        return $this;
    }

    /**
    | ----------------------------------------------------------------------------
    | prepare the LEFT JOIN rule
    | ----------------------------------------------------------------------------
    * @return Database instance
    */
    public function leftJoin($table, $conditions)
    {
        $this->_join[] = "LEFT JOIN {$table} ON($conditions)";
        return $this;
    }

    /**
    | ----------------------------------------------------------------------------
    | prepare the RIGHT JOIN rule
    | ----------------------------------------------------------------------------
    * @return Database instance
    */
    public function rightJoin($table, $conditions)
    {
        $this->_join[] = "RIGHT JOIN {$table} ON($conditions)";
        return $this;
    }

    /**
    | ----------------------------------------------------------------------------
    | prepare the WHERE condition rule
    | ----------------------------------------------------------------------------
    * @return Database instance
    */
    public function where($conditions = NULL, $filter = "=", $value = NULL, $rule = "\nWHERE")
    {
        if( empty($this->_select) ) $this->select();
        
        if( !empty($conditions) ){
            if( is_string($conditions) ){
                $this->_where[] = "{$rule} \"{$conditions}\" {$filter} '{$value}'";
            }
            elseif( is_array($conditions) )
            {
                $data = $this->getFields($conditions, $filter, " AND ");
                $this->_where[] = "{$rule} {$data['sql']}";
                $this->params  = array_merge($this->params, $data['save']);
            }
        }

        return $this;
    }

    /**
    | ----------------------------------------------------------------------------
    | prepare extra condition in WHERE rule
    | ----------------------------------------------------------------------------
    * @return Database instance
    */
    public function andWhere($conditions = NULL, $filter = "=", $value = NULL)
    {
        $this->where($conditions, $filter, $value, " AND ");
        
        return $this;
    }

    /**
    | ----------------------------------------------------------------------------
    | prepare the GROUP BY rule
    | ----------------------------------------------------------------------------
    * @return Database instance
    */
    public function group(array $group)
    {
        $ret = $this->getFields($group);
        $this->_group = "\nGROUP BY {$ret['sql']}";
        
        return $this;
    }

    /**
    | ----------------------------------------------------------------------------
    | prepare the ORDER BY rule
    | ----------------------------------------------------------------------------
    * @return Database instance
    */
    public function order(array $order)
    {
        $this->_order = "\nORDER BY ".implode(',', $order);
        
        return $this;
    }

    /**
    | ----------------------------------------------------------------------------
    | prepare the LIMIT rule
    | ----------------------------------------------------------------------------
    * @return Database instance
    */
    public function limit($limit,$offset=0)
    {
        $this->_limit = "\nLIMIT {$limit} OFFSET {$offset}";
        
        return $this;
    }
}