<?php
/**
| ----------------------------------------------------------------------------
| Model layer that make the connection with the database
| INSERT, UPDATE, DELETE and SELECT
| ----------------------------------------------------------------------------
* PHP version 7.1
*/

namespace Library\Marlon\Modules;

use Library\Marlon\DB\Connection;

use \Exception;
use \ReflectionObject;

abstract class Model
{
    use Validator{
        Validator::__construct as ValidatorConstruct;
    }

    public $id;
    public $createdAt;
    public $updatedAt;

    protected $db;
    protected $fillables = [];
    protected $rules = [];
    protected $relations = [];
    protected $withTimestamps = TRUE;

    protected $listErrors = [];

    private static $foreignInstance = [];
    private static $findOne = [];
    private static $find = [];
    
    public function __construct()
    {
        $this->ValidatorConstruct();
        
        $connection = new Connection();

        foreach($this->fillables as $default)
        {
            if( !property_exists($this, $default) )
                $this->{$default} = NULL;
        }
        
        $this->db = $connection->loadSgbd($this->table, get_class($this));
    }

    
    /**
    | ----------------------------------------------------------------------------
    | DELETE a Record
    | ----------------------------------------------------------------------------
    * @param array $data the fields to be deleted
    * @return bool
    */
    public function delete(array $conditions = [])
    {
        return $this->db->delete(array_merge(['id' => $this->id], $conditions));
    }

    /**
    | ----------------------------------------------------------------------------
    | INSERT Record
    | ----------------------------------------------------------------------------
    * @param array $data the fields to be inserting
    * @return bool
    */
    protected function create($data)
    {
        if( $this->withTimestamps ){
            $data["createdAt"] = date('Y-m-d H:i:s');
            $this->createdAt = date('Y-m-d H:i:s');
        }
        $lastId = $this->db->insert($data);
        
        if( $lastId )
        {
            $this->id = $lastId;
            return TRUE;
        }
        return FALSE;
    }

    /**
    | ----------------------------------------------------------------------------
    | UPDATE Record
    | ----------------------------------------------------------------------------
    * @param array $data the fields to be updated
    * @return bool
    */
    protected function update($data)
    {
        if( $this->withTimestamps ){
            $data["updatedAt"] = date('Y-m-d H:i:s');
            $this->updatedAt = date('Y-m-d H:i:s');
        }
        return $this->db->update($data, ['id' => $this->id]);
    }

    /**
    | ----------------------------------------------------------------------------
    | CREATE OR UPDATE a Record
    | validate the fields sent in $data and check in fillables to set the data
    | ----------------------------------------------------------------------------
    * @param array $data the fields to be updated
    * @return bool
    */
    public function save(array $data)
    {
        $allowed = $this->bindFillables($data);
        
        if( $allowed === FALSE ){
            return FALSE;
        }
        if( $this->validate() )
        {
            if( empty($this->id) )
                return $this->create($allowed);
            else
                return $this->update($allowed);
        }
        return FALSE;
    }

    /**
    | ----------------------------------------------------------------------------
    | Fill the properties of the class, according the post sent that exists in fillables
    | ----------------------------------------------------------------------------
    * @param array $data the data sent
    * @return array||bool
    */
    protected function bindFillables(array $data)
    {
        $allowed = [];
        foreach($data as $field => $item)
        {
            if( in_array($field, $this->fillables) )
            {
                $this->{$field} = $item;
                $allowed[$field] = $item;
            }
        }
        
        if( empty($allowed) ){
            return FALSE;
        }
        return $allowed;
    }

    /**
    | ----------------------------------------------------------------------------
    | Get all public properties of the class
    | ----------------------------------------------------------------------------
    * @return array
    */
    public function getAttributes()
    {
        $properties = [];
        $reflection = new ReflectionObject($this);
        foreach($reflection->getProperties(\ReflectionProperty::IS_PUBLIC) as $item){
            $properties[$item->name] = $item->getValue($this);
        }
        return $properties;
    }
    
    /**
    | ----------------------------------------------------------------------------
    | Define the list of dependencies that class have, relation by foregn keys
    | ----------------------------------------------------------------------------
    * @return array
    */
    protected function relations()
    {
        return [
            // "alias" => [nameClass::class, Type, foreign, reference]
        ];
    }

    /**
    | ----------------------------------------------------------------------------
    | load the relations default of the class, according the relations informed
    | ----------------------------------------------------------------------------
    * @return Library\Marlon\Modules\Model instance
    */
    public function with(array $relations)
    {
        foreach($this->relations() as $alias => $models){
            if( in_array($alias, $relations) ){
                $this->relations[$alias] = $models;
            }
        }
        return $this;
    }

    /**
    | ----------------------------------------------------------------------------
    | Create a list of records with paginations
    | ----------------------------------------------------------------------------
    * @param string $currentPage the page currently
    * @param array $fields list of fields
    * @return array
    */
    public function paginate($currentPage, $fields = ["*"])
    {
        $cloned = clone $this->db;
        $total = $cloned->count();
        // How many items to list per page
        $limit = 5;
        // How many pages will there be
        $pages = ceil($total / $limit);
        // Calculate the offset for the query
        $offset = ($currentPage - 1)  * $limit;
        
        $items = ["links" => compact('pages', 'total','limit', 'currentPage')];
        
        $items["data"] = $this->limit($limit, $offset)->find($fields);
        
        return $items;
    }

    /**
    | ----------------------------------------------------------------------------
    | Find the results of the query, and fill the relations with used 'with'
    | ----------------------------------------------------------------------------
    * @param $field the list of field returned in the query
    * @return array
    */
    public function find($fields = ['*'])
    {
        $modelArray = $this->db->select($fields)->find(true);
        
        $data = [];
        foreach($modelArray as $item)
        {
            $this->findForeign($item);
            
            $data[] = $item;
        }
        $this->relations = [];
        return $data;
    }

    /**
    | ----------------------------------------------------------------------------
    | Find the results of the query, and fill the relations with used 'with'
    | add to $item, the results of the foreign relation stored in relations property
    | ----------------------------------------------------------------------------
    * @param &$item line reference of the result in the find method
    * @return void
    */
    private function findForeign(&$item)
    {
        foreach($this->relations as $alias => $relation)
        {
            list($foreign, $fk, $pk) = $relation;
            if( !isset(self::$foreignInstance[$alias]) )
                self::$foreignInstance[$alias] = new $foreign();
            
            $foreign = self::$foreignInstance[$alias];
            $item[$alias] = $foreign::all([$fk => $item[$pk]]);
        }
    }

    /**
    | ----------------------------------------------------------------------------
    | Append additional where conditions in the query
    | ----------------------------------------------------------------------------
    * @param string|array $conditions string or array with field in condition
    * @param string $filter the character to compare in the query(e:g: =, like, ilike)
    * @param string $value the value to be compared when $conditions is string
    * @return Library\Marlon\Modules\Model instance
    */
    public function andWhere($conditions, $filter = '=', $value = NULL)
    {
        $this->db->andWhere($conditions, $filter, $value);

        return $this;
    }
    
    /**
    | ----------------------------------------------------------------------------
    | Add the order By in query rule
    | ----------------------------------------------------------------------------
    * @param mixed $order the fields to be ordered
    * @return Library\Marlon\Modules\Model instance
    */
    public function order($order)
    {
        $this->db->order($order);
        return $this;
    }

    public function limit($limit = 1, $offset=0)
    {
        $this->db->limit($limit, $offset);
        return $this;
    }

    public function count()
    {
        return $this->db->count();
    }

    /**
    | ----------------------------------------------------------------------------
    | Find a record, and return a instance of the current class,
    | to easily the usage of the method save and delete
    | ----------------------------------------------------------------------------
    * @param int|array $id the id of the record, or the list of where conditions
    * @return Library\Marlon\Modules\Model instance
    */
    public function findByPk($id)
    {
        if( !is_array($id) ){
            $id = [ 'id' => $id ];
        }
        
        return $this->db->where($id)->findObject();
        
    }
    
    /**
    | ----------------------------------------------------------------------------
    | Add the base where conditions in the query
    | ----------------------------------------------------------------------------
    * @param string|array $conditions string or array with field in condition
    * @param string $filter the character to compare in the query(e:g: =, like, ilike)
    * @param string $value the value to be compared when $conditions is string
    * @return Library\Marlon\Modules\Model instance
    */
    public static function where($conditions, $filter = '=', $value = NULL)
    {
        $model = new static;
        $model->db->where($conditions, $filter, $value);
        return $model;
    }

    /**
    | ----------------------------------------------------------------------------
    | Find a record, and return a simples array
    | ----------------------------------------------------------------------------
    * @param int|array $id the id of the record, or the list of where conditions
    * @return Library\Marlon\Modules\Model instance
    */
    public static function get($id)
    {
        $model = new static;
        $cacheId = $model->table.'-'.$id;
        
        if( !isset(self::$findOne[$cacheId]) )
            self::$findOne[$cacheId] = $model->db->where(['id'=>$id])->findOne();
        
        return self::$findOne[$cacheId];
    }

    /**
    | ----------------------------------------------------------------------------
    | Find all a record of the current model
    | ----------------------------------------------------------------------------
    * @param array $where the where condition params
    * @return array
    */
    public static function all($where = [])
    {
        $model = new static;
        $cacheId = $model->table.'-'.implode("|", $where);
        
        if( !isset(self::$find[$cacheId]) )
            self::$find[$cacheId] = $model->db->where($where)->find();
        
        return self::$find[$cacheId];
    }
}