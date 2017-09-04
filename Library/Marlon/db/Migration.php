<?php
/**
| ----------------------------------------------------------------------------
| Class with responsability of load the SGDB used in the application
| that is responsable to populate of the fields in pattern each database
| with the basic method create and drop table
| ----------------------------------------------------------------------------
* PHP version 7.1
*
* @category Library\Marlon
* @package  Core\DB
* @author   Marlon Cardoso <marlonrcardoso@yahoo.com.br>
* @license  http://opensource.org/licenses/gpl-license.php GNU Public License
*/

namespace Library\Marlon\DB;

abstract class Migration
{
    /**
    | ----------------------------------------------------------------------------
    | The database connection
    | ----------------------------------------------------------------------------
    * @var Table||null $db the instance of the class table 
    */
    private $db = NULL;

    /**
    | ----------------------------------------------------------------------------
    | The database connection
    | ----------------------------------------------------------------------------
    * @var Schema||null $schema the instance of the class Schema 
    */
    private $schema = NULL;

    /**
    | ----------------------------------------------------------------------------
    | Method implement in the children class to create a table
    | ----------------------------------------------------------------------------
    * @param string $table the table name
    * @return void
    */
    public abstract function up();
    /**
    | ----------------------------------------------------------------------------
    | Method implement in the children class to drop a table
    | ----------------------------------------------------------------------------
    * @return void
    */
    public abstract function down();

    public function __construct()
    {
        $connection = new Connection();
        
        $this->db = $connection->loadSgbd('migrations');
        $this->schema = $connection->loadSchema();
    }

    /**
    | ----------------------------------------------------------------------------
    | restart the schema before run the new migration
    | ----------------------------------------------------------------------------
    * @param string $table the table name
    * @return void
    */
    public function before($table)
    {
        $this->schema->clean();
        $this->schema->setTable($table);
    }

    /**
    | ----------------------------------------------------------------------------
    | Use the $callback function to fill the fields of the table 
    | to after run the query to create table
    | ----------------------------------------------------------------------------
    * @param string $table the table name
    * @param \Closure anonimous function
    * @return string the message
    */
    protected function create($table, \Closure $callback)
    {
        $this->before($table);

        call_user_func($callback, $this->schema);
        
        $return = $this->schema->getDataFields();
        $enumQuery = $this->schema->getCreateEnum();
        
        $dataString = implode(",\n", $return);
        $createQuery = "";
        if( count($enumQuery) ){
            foreach($enumQuery as $q){
                $createQuery .= "$q\n";
            }
        }
        
        $createQuery .= "CREATE TABLE IF NOT EXISTS \"{$table}\" (\n{$dataString}\n);";
        
        return $this->db->query($createQuery);
    }

    /**
    | ----------------------------------------------------------------------------
    | Run the query to drop table
    | ----------------------------------------------------------------------------
    * @param string $table the table name
    * @return string the message
    */
    protected function drop($table, $action = "")
    {
        $this->before($table);

        return $this->db->query("DROP TABLE IF EXISTS \"{$table}\" {$action};");
    }
}