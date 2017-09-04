<?php

namespace Library\Marlon\DB;

class Connection
{
    private $sgbd = NULL;
    private $database = NULL;
    private $baseNamespace = "Library\\Marlon\\DB\\Databases";

    public function __construct()
    {
        $this->database = require ROOT.DS.'config'.DS.'database.php';
        $this->sgbd = ucfirst($this->database['default']);
    }

    public function loadSchema()
    {
        $schemaModel = "{$this->baseNamespace}\\{$this->sgbd}\\Schema";

        return new $schemaModel();
    }

    public function loadSgbd($table, $className = '')
    {
        $db = $this->database['connections'][$this->database['default']];
        $dbModel = "{$this->baseNamespace}\\{$this->sgbd}\\Table";
        
        return new $dbModel($db, $table, $className);
    }
}