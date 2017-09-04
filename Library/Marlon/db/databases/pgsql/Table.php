<?php
/**
| ----------------------------------------------------------------------------
| Class with responsability of establish the connection with the database
| postgresql in your standard of connection
| ----------------------------------------------------------------------------
* PHP version 7.1
*
* @category Library\Marlon
* @package  Core\DB\Postgres
* @author   Marlon Cardoso <marlonrcardoso@yahoo.com.br>
* @license  http://opensource.org/licenses/gpl-license.php GNU Public License
*/
namespace Library\Marlon\DB\Databases\Pgsql;

use Library\Marlon\DB\Database; 

class Table extends Database
{
    public function __construct(Array $db, $table, $className = '')
    {
        try {
            $this->db = $db;
            $this->tableName = $table;
            $this->className = $className;
            
            $this->setInstance(new \PDO($this->prepareConnection()));
        } catch (\PDOException $err) {
            var_dump($err->getMessage());
            exit;
        }
    }

    protected function prepareConnection()
    {
        return "pgsql:".$this->host().$this->database().$this->username().$this->password();
    }

    protected function host()
    {
        return isset($this->db['server']) ? "host={$this->db['server']};" : "";
    }
    protected function database()
    {
        return isset($this->db['database']) ? "dbname={$this->db['database']};" : "";
    }
    protected function username()
    {
        return isset($this->db['user']) ? "user={$this->db['user']};" : "";
    }
    protected function password(){
        return isset($this->db['password']) ? "password={$this->db['password']}" : "";
    }
}