<?php
/**
| ----------------------------------------------------------------------------
| Class with responsability of manage the structure of the field and dataType
| set contraint, primary keys, and rules in the standard of the SGBD 
| postgres sql database manager
| ----------------------------------------------------------------------------
* PHP version 7.1
*
* @category Library\Marlon
* @package  Core\DB\Postgres
* @author   Marlon Cardoso <marlonrcardoso@yahoo.com.br>
* @license  http://opensource.org/licenses/gpl-license.php GNU Public License
*/

namespace Library\Marlon\DB\Databases\Pgsql;

class Schema
{
    /**
    | ----------------------------------------------------------------------------
    | The list of the field for the current table
    | ----------------------------------------------------------------------------
    * @var array $dataFields 
    */
    private $dataFields = [];
    /**
    | ----------------------------------------------------------------------------
    | Use when required, use in this moment to set the contraint name of the foreing key
    | ----------------------------------------------------------------------------
    * @var string $tableName the name of the table
    */
    private $tableName = NULL;
    
    /**
    | ----------------------------------------------------------------------------
    | to recovery the field, used in the null,notNull,default,check methods
    | ----------------------------------------------------------------------------
    * @var string $current the name of the current field
    */
    private $current = NULL;

    /**
    | ----------------------------------------------------------------------------
    | store the query to create enum type in create table
    | ----------------------------------------------------------------------------
    * @var array $createEnum the query for create enum
    */
    private $createEnum = [];

    /**
    | ----------------------------------------------------------------------------
    | Returns the query for create enum
    | ----------------------------------------------------------------------------
    * @return string
    */
    public function getCreateEnum()
    {
        return $this->createEnum;
    }

    /**
    | ----------------------------------------------------------------------------
    | Clean data of the scheme to resolve problem of the multiple migration
    | ----------------------------------------------------------------------------
    * @return void
    */
    public function clean()
    {
        $this->dataFields = [];
        $this->tableName = NULL;
        $this->current = NULL;
    }

    /**
    | ----------------------------------------------------------------------------
    | Sets the table name used
    | ----------------------------------------------------------------------------
    * @param string $tableName  the name of the table
    * @return void
    */
    public function setTable($tableName)
    {
        $this->tableName = $tableName;
    }

    /**
    | ----------------------------------------------------------------------------
    | Returns the all field defined to use in the migration
    | ----------------------------------------------------------------------------
    * @return array list of fields
    */
    public function getDataFields()
    {
        return $this->dataFields;
    }

    /**
    | ----------------------------------------------------------------------------
    | store the fields of the table, and define the current field
    | ----------------------------------------------------------------------------
    * @param string $tableName  the name of the table
    * @return Schema
    */
    private function fillData($string, $dataType)
    {
        $this->current = $string;
        $this->dataFields[$string] = "\"{$string}\" {$dataType}";
        return $this;
    }
    
    /**
    | ----------------------------------------------------------------------------
    | store the fields of the table, with dataType 'datetime' create and update
    | ----------------------------------------------------------------------------
    * @return void
    */
    public function timestamps()
    {
        $this->dataFields["createdAt"] = "\"createdAt\" timestamp without time zone";
        $this->dataFields["updatedAt"] = "\"updatedAt\" timestamp without time zone";
    }

    public function enum($string, array $options)
    {
        $opts = implode("','", $options);
        $stringEnum = [
            "DROP TYPE {$this->tableName}_{$string}_mood;",
            "CREATE TYPE {$this->tableName}_{$string}_mood AS ENUM ('{$opts}');"
        ];
        $this->createEnum[$string] = join("\n", $stringEnum);
        $this->dataFields[$string] = "{$string} {$this->tableName}_{$string}_mood";
        return $this;
    }

    /**
    | ----------------------------------------------------------------------------
    | store the primary key if the table
    | ----------------------------------------------------------------------------
    * @param string $string the field
    * @return void
    */
    public function increment($string)
    {
        $this->current = $string;
        $this->dataFields[$string] = "{$string} SERIAL NOT NULL PRIMARY KEY";
    }

    /**
    | ----------------------------------------------------------------------------
    | Store the constraint for foreing key table, to add the relation
    | ----------------------------------------------------------------------------
    * @param string $string the data field
    * @param string $id the id of table reference
    * @param string $reference the table reference
    * @param string $delete the delete action
    * @param string $update the update action
    * @return void
    */
    public function foreign($string, $id, $reference, $delete = 'NO ACTION', $update = 'NO ACTION')
    {
        $fk = "fk_{$this->tableName}_{$reference}";
        $constraint = "CONSTRAINT {$fk} FOREIGN KEY (\"{$string}\")";
        $constraint .= " REFERENCES \"{$reference}\" (\"{$id}\")";
        $constraint .= " ON DELETE {$delete} ON UPDATE {$update}";
        $this->dataFields[$fk] = $constraint;
    }

    /**
    | ----------------------------------------------------------------------------
    | Sets 'not null' to the current field
    | ----------------------------------------------------------------------------
    * @return void
    */
    public function notNull()
    {
        $this->dataFields[$this->current] .= " NOT NULL";
    }

    /**
    | ----------------------------------------------------------------------------
    | Sets 'null' to the current field
    | ----------------------------------------------------------------------------
    * @return void
    */
    public function null()
    {
        $this->dataFields[$this->current] .= " NULL";
    }

    /**
    | ----------------------------------------------------------------------------
    | Sets default value information in the current field
    | ----------------------------------------------------------------------------
    * @return void
    */
    public function default($value)
    {
        $this->dataFields[$this->current] .= " DEFAULT {$value}";
    }

    /**
    | ----------------------------------------------------------------------------
    | Sets check condition in the current field
    | ----------------------------------------------------------------------------
    * @return void
    */
    public function check($value)
    {
        $this->dataFields[$this->current] .= " CHECK ({$value})";
    }    

    /**
    | ----------------------------------------------------------------------------
    | store the fields of the table, with dataType 'character varying'
    | ----------------------------------------------------------------------------
    * @param string $tableName the name of table field
    * @param int $length the size of the field
    * @return Schema
    */
    public function string($string, $length = 255)
    {
        return $this->fillData($string, "CHARACTER VARYING({$length})");
    }

    /**
    | ----------------------------------------------------------------------------
    | store the fields of the table, with dataType 'text'
    | ----------------------------------------------------------------------------
    * @param string $tableName the name of table field
    * @return Schema
    */
    public function text($string)
    {
        return $this->fillData($string, "TEXT");
    }

    /**
    | ----------------------------------------------------------------------------
    | store the fields of the table, with dataType 'intenger'
    | ----------------------------------------------------------------------------
    * @param string $tableName the name of table field
    * @return Schema
    */
    public function integer($string)
    {
        return $this->fillData($string, "INTEGER");
    }
    
    /**
    | ----------------------------------------------------------------------------
    | store the fields of the table, with dataType 'date'
    | ----------------------------------------------------------------------------
    * @param string $tableName the name of table field
    * @return Schema
    */
    public function date($string)
    {
        return $this->fillData($string, "DATE");
    }

    /**
    | ----------------------------------------------------------------------------
    | store the fields of the table, with dataType 'datetime'
    | ----------------------------------------------------------------------------
    * @param string $tableName the name of table field
    * @param bool $withTimeZone create with or without timestamp
    * @return Schema
    */
    public function datetime($string, $withTimeZone = FALSE)
    {
        $timeZone = ($withTimeZone ? "with time zone" : "without time zone");
        return $this->fillData($string, "timestamp {$timeZone}");
    }

    public function dump()
    {
        var_dump($this->dataFields);
    }
}