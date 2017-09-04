<?php
/**
| ----------------------------------------------------------------------------
| trait with responsability for the validations of the models class
| ----------------------------------------------------------------------------
* PHP version 7.1
*/
namespace Library\Marlon\Modules;

trait Validator
{
    /**
    | ----------------------------------------------------------------------------
    | The list of validations to be executed before save a record of the model
    | ----------------------------------------------------------------------------
    * @var array $rules
    */
    protected $rules = [];
    
    /**
    | ----------------------------------------------------------------------------
    | The list of messages standard with typical errors
    | ----------------------------------------------------------------------------
    * @var array $messages
    */
    protected $messages = [];
    
    /**
    | ----------------------------------------------------------------------------
    | The list of errors result of the validations in $rules property
    | ----------------------------------------------------------------------------
    * @var array $errors
    */
    private $errors = [];

    /**
    | ----------------------------------------------------------------------------
    | Start the file with de message according the default language
    | ----------------------------------------------------------------------------
    */
    public function __construct()
    {
        $this->messages = require implode(DS, [ROOT,'public','templates','language',appConfig('language'),'validator']).".php";
    }
    
    /**
    | ----------------------------------------------------------------------------
    | Add a error in the model at the field that failed in the validation
    | ----------------------------------------------------------------------------
    * @param string $field
    * @param string $message
    * @return void
    */
    public function setErrors($field, $message)
    {
        $this->errors[$field][] = $message;
    }
    
    /**
    | ----------------------------------------------------------------------------
    | Check if exists error in the current validation
    | ----------------------------------------------------------------------------
    * @return bool is true when exist error
    */
    public function hasErrors()
    {
        return !empty($this->errors);
    }

    /**
    | ----------------------------------------------------------------------------
    | Returns the list with the errors(grouped by [field] => [list-of-possibilities])
    | ----------------------------------------------------------------------------
    * @return bool is true when exist error
    */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
    | ----------------------------------------------------------------------------
    | Gets the message from method/validator called
    | e.g:$key=required, returns the message at the key required, in $messages property 
    | ----------------------------------------------------------------------------
    * @param string $key the method called
    * @param array $arguments the list of arguments set to the method called
    * @return string the message
    */
    protected function processMessages($key, $arguments)
    {
        if( array_key_exists($key, $this->messages) )
        {
            $messager = $this->messages[$key];
            foreach($arguments as $replacement => $value)
            {
                $messager = str_replace(":{$replacement}", $value, $messager);
            }
            return $messager;
        }
        return "{$key} random Error!";
    }

    /**
    | ----------------------------------------------------------------------------
    | Run the validation configurated to the model
    | call the method with the validation in the standard stored in the $rules
    | standard: 
    | array(
    |    'field' => 'rules-devided-by-pipe:the dot two devided the argument set to rule'
    | )
    | ----------------------------------------------------------------------------
    * @return bool
    */
    public function validate()
    {
        $errors = [];
        foreach($this->rules as $field => $validations)
        {
            foreach(explode('|', $validations) as $validate)
            {
                $events = explode(':', $validate);
                if (is_callable(array($this, $events[0])))
                {
                    $argList = ['field' => $field];
                    if( isset($events[1]) ){
                        $argList[$events[0]] = $events[1];
                    }

                    $output = call_user_func_array(array($this, $events[0]), $argList);
                    if( !$output ){
                        $argList['table'] = $this->table;
                        $this->setErrors($field, $this->processMessages($events[0], $argList));
                    }
                }
                else{
                    $this->setErrors($field, $this->processMessages("unknownMethod", ['field' => $validate]));
                }
            }
        }
        
        if( $this->hasErrors() ){
            // set status Http 400 in the header of the response
            (new \Library\Marlon\Http\Response)->status(400);
            return FALSE;
        }
        return TRUE;
    }
    
    /**
    | ----------------------------------------------------------------------------
    | Validator for verify if the $attribute is not empty
    | ----------------------------------------------------------------------------
    * @param string $attribute the attribute validated
    * @return bool
    */
    protected function required($attribute)
    {
        return (bool) ( $this->{$attribute} != '' && !is_null($this->{$attribute}) );
    }

    /**
    | ----------------------------------------------------------------------------
    | Validator for verify if the $attribute is a integer
    | ----------------------------------------------------------------------------
    * @param string $attribute the attribute validated
    * @return bool
    */
    protected function number($attribute)
    {
        if( $this->required($attribute) )
            return (bool) preg_match("/[0-9]/", $this->{$attribute});
        return TRUE;
    }

    /**
    | ----------------------------------------------------------------------------
    | Validator the verify if the email address is valid
    | ----------------------------------------------------------------------------
    * @param string $attribute the attribute validated
    * @return bool
    */
    protected function email($attribute)
    {
        $regex = "/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/";
        return preg_match($regex, $this->{$attribute});
    }

    /**
    | ----------------------------------------------------------------------------
    | Validator the minimum size of the a string
    | ----------------------------------------------------------------------------
    * @param string $attribute the attribute validated
    * @param string $number the minimum size
    * @return bool
    */
    protected function min($attribute, $number = 10)
    {
        return (bool) (strlen($this->{$attribute}) >= $number);
    }

    /**
    | ----------------------------------------------------------------------------
    | Validator the maximum size of the a string
    | ----------------------------------------------------------------------------
    * @param string $attribute the attribute validated
    * @param string $number the maximum size
    * @return bool
    */
    protected function max($attribute, $number = 200)
    {
        return (bool) (strlen($this->{$attribute}) <= $number);
    }

    /**
    | ----------------------------------------------------------------------------
    | Validator if the $attribute already exists in the $table
    | ----------------------------------------------------------------------------
    * @param string $attribute the attribute validated
    * @param string $table the table reference
    * @return bool
    */
    protected function unique($attribute, $table)
    {
        $query = $this->db->select(['id'], $table)->where($attribute, "=", $this->{$attribute});
        
        if( !empty($this->id) )
            $query->andWhere("id", "<>", $this->id);
        
        return (bool) $query->count() == 0;
    }

    /**
    | ----------------------------------------------------------------------------
    | Validator to verify if the attribute value is between the $options
    | ----------------------------------------------------------------------------
    * @param string $attribute the attribute validated
    * @param array $options the list of options availables split by ','
    * @return bool
    */
    protected function enum($attribute, $options)
    {
        return (bool) in_array($this->{$attribute}, explode(',', $options));
    }

    /**
    | ----------------------------------------------------------------------------
    | Validator cannot repeat in the roles stored in $options argument
    | $options: the firts index is the value to be compared
    | and next indexes are additions condition by another fields of the table
    | e.g:('status','1, name') exists the status = 1, and name = $this->name
    | ----------------------------------------------------------------------------
    * @param string $attribute the attribute validated
    * @param string $options the list of filters split by ','
    * @return bool
    */
    protected function uniqueValue($attribute, $options)
    {
        $addOptions = explode(',', $options);
        // field to be compare and find
        $compare = $addOptions[0];
        // remove the first index, becouse is not a valid field
        array_shift($addOptions);

        if( $this->{$attribute} == $compare )
        {
            $conditions = [$attribute => $compare];
            // the other fields of the table to add in the filter
            if( !empty($addOptions)){
                foreach($addOptions as $f){
                    $conditions[$f] = $this->{$f};
                }
            }
            
            $query = $this->db->select(['id'])->where($conditions);
    
            if( !empty($this->id) ){
                // to enabled the update of the field
                $query->andWhere("id", "<>", $this->id);
            }
            
            return (bool) $query->count() == 0;
        }
        return TRUE;
    }
}