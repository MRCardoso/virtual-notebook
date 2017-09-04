<?php
/**
| ----------------------------------------------------------------------------
| Class with responsability for serve the application 
| with data of server, request and session
| ----------------------------------------------------------------------------
* PHP version 7.1
*/

namespace Library\Marlon\Http;

trait HttpResource
{
    
    /**
    | ----------------------------------------------------------------------------
    | Get the data of the server
    | ----------------------------------------------------------------------------
    * @param mixed $key the index of the object
    * @param mixed $default the default value in case the index not exists
    * @return array the server data
    */
    public function server($key = NULL, $default = FALSE)
    {
        $server = $_SERVER;
        
        if( $key == NULL ) return $server;
        
        return ( array_key_exists($key, $server) ? $server[$key] : $default);
    }
    
    /**
    | ----------------------------------------------------------------------------
    | Get the path_info(used to define the MVC to be use)
    | ----------------------------------------------------------------------------
    * @return string Retorna a path_info
    */
    public function pathInfo()
    {
        return $this->server("PATH_INFO", "");
    }
    
    /**
    | ----------------------------------------------------------------------------
    | Validate or return the request type
    | ----------------------------------------------------------------------------
    * @param string $method the request type
    *
    * @return bool, if $method equal to false return the method used
    */
    public function isMethod($method)
    {
        $request = strtolower($this->server('REQUEST_METHOD'));
        $method = strtolower($method);
        
        if( ($request === $method) || $this->isPutOrDelete($method) )
            return TRUE;
        
        return FALSE;
    }
    
    /**
    | ----------------------------------------------------------------------------
    | Validate if the method is put or delete
    | check if the current request is post and request method is patch, put or delete
    | and have post _METHOD with value put or delete
    | ----------------------------------------------------------------------------
    * @return bool
    */
    private function isPutOrDelete($method)
    {
        $request = strtolower($this->server('REQUEST_METHOD'));
        return (bool) ( 
            (preg_match("/(delete|put|patch)/i", strtolower($method)) && $request == 'post') && 
            (isset($_POST['_METHOD']) && preg_match("/(delete|put|patch)/i", strtolower($_POST['_METHOD'])))
        );
    }

    /**
    | ----------------------------------------------------------------------------
    | Split the url in the $string and converts in an array filtered
    | ----------------------------------------------------------------------------
    * @param string $string the string to convert
    * @return array
    */
    public function urlToArray($string)
    {
        return array_values(array_filter(explode("/", $string)));
    }

    /**
    | ----------------------------------------------------------------------------
    | Create a pattern to get the path of files or urls the $string comes separated by dot
    | ----------------------------------------------------------------------------
    * @return string returns the path in $string concated by $separator
    */
    public function path($string, $separator = DS, $spliter = '.')
    {
        $array = explode($spliter, $string);
        return join($separator, $array);
    }

    /**
    | ----------------------------------------------------------------------------
    | Returns the previous url requeted
    | ----------------------------------------------------------------------------
    * @return string
    */
    public function previousUrl()
    {
        $previous = $this->server('HTTP_REFERER', NULL);
        if( $previous != NULL )
            return $previous;
        
        return $this->baseUrl();
    }
    /**
    | ----------------------------------------------------------------------------
    | Get the base url of framework(where he is installed)
    | ----------------------------------------------------------------------------
    * @return string base url of the framework
    */
    public function baseUrl($string = NULL, $atRoot=FALSE, $atCore=FALSE, $parse=FALSE)
    {
        if ($this->server('HTTP_HOST'))
        {
            $http = $this->server("HTTPS") && strtolower($this->server("HTTPS")) !== 'off' ? 'https' : 'http';
            $hostname = $this->server("HTTP_HOST");
            $dir =  str_replace(basename($this->server('SCRIPT_NAME')), '', $this->server('SCRIPT_NAME'));

            $core = preg_split('@/@', str_replace($this->server('DOCUMENT_ROOT'), '', realpath(dirname(__FILE__))), NULL, PREG_SPLIT_NO_EMPTY);
            $core = $core[0];

            $tmplt = $atRoot ? ($atCore ? "%s://%s/%s/" : "%s://%s/") : ($atCore ? "%s://%s/%s/" : "%s://%s%s");
            $end = $atRoot ? ($atCore ? $core : $hostname) : ($atCore ? $core : $dir);
            $baseUrl = sprintf( $tmplt, $http, $hostname, $end );
        }
        else
        {
            $baseUrl = 'http://localhost/';
        }

        if ($parse)
        {
            $baseUrl = parse_url($baseUrl);
            if (array_key_exists('path',$baseUrl))
            {
                if ($baseUrl['path'] == '/'){
                    $baseUrl['path'] = '';
                }
            }
        }
        if( $string == NULL )
            return $baseUrl;
        else
            return $baseUrl.$string;
    }

    /**
    | ----------------------------------------------------------------------------
    | Validate if the request is by ajax
    | ----------------------------------------------------------------------------
    * @return bool
    */
    public function isAjaxRequest()
    {
        if(!empty($this->server('HTTP_X_REQUESTED_WITH')) && strtolower($this->server('HTTP_X_REQUESTED_WITH')) == 'xmlhttprequest')
        {    
            return TRUE;
        }
        return FALSE;
    }

    /**
    | ----------------------------------------------------------------------------
    | Store session messages for system
    | ----------------------------------------------------------------------------
    * @return void
    */
    public function setFlash($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    /**
    | ----------------------------------------------------------------------------
    | Verify if the session exists
    | ----------------------------------------------------------------------------
    * @return bool
    */
    public function hasFlash($key)
    {
        return (bool) isset( $_SESSION[$key] );
    }
    
    /**
    | ----------------------------------------------------------------------------
    | Returns the data of session called in the $key
    | ----------------------------------------------------------------------------
    * @return bool
    */
    public function getFlash($key)
    {
        $data = $_SESSION[$key];
        unset($_SESSION[$key]);
        return $data;
    }

    /**
    | ----------------------------------------------------------------------------
    | Validate if has authetication running
    | ----------------------------------------------------------------------------
    * @return mixed
    */
    public function validateAuth()
    {
        if( isset($_SESSION) && isset($_SESSION['userData']) )
        {
            return TRUE;
        }
        return [401, "É preciso efetuar o login!"];
    }

    /**
    | ----------------------------------------------------------------------------
    | Validate if the user is not authenticated
    | ----------------------------------------------------------------------------
    * @return mixed
    */
    public function validateGuest()
    {
        if( !isset($_SESSION['userData']) )
        {
            return TRUE;
        }
        return [412, "Esta requisição não esta disponível quando ha uma sessão iniciada!"];
    }
}