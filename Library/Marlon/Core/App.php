<?php
/**
| ----------------------------------------------------------------------------
| Class with responsability for start the application 
| and defines the controler and action that be called by the url requested
| store the routes enabled to the application
| add params in the get request by the arguments stored in the path_info
| and inject the instance of 'Library\Marlon\Http\Request' in post request
| ----------------------------------------------------------------------------
* PHP version 7.1
*/

namespace Library\Marlon\Core;

use Exception;
use Library\Marlon\Http\Request;
use Library\Marlon\Http\Response;

class App
{
    use \Library\Marlon\Http\HttpResource;

    /**
    | ----------------------------------------------------------------------------
    | The current controller called in the request
    | ----------------------------------------------------------------------------
    * @return string
    */
    private $controller = NULL;
    /**
    | ----------------------------------------------------------------------------
    | The current action called in the request
    | ----------------------------------------------------------------------------
    * @return string $action
    */
    private $action = NULL;
    
    /**
    | ----------------------------------------------------------------------------
    | the arguments that needs to pass the controller loaded
    | ----------------------------------------------------------------------------
    * @return array $params
    */
    private $params = [];
    /**
    | ----------------------------------------------------------------------------
    | The list of the routes available to the application
    | ----------------------------------------------------------------------------
    * @return array $requestList
    */
    private $requestList = [];
    
    /**
    | ----------------------------------------------------------------------------
    | Store the currently url requested
    | ----------------------------------------------------------------------------
    * @return array $currentlyRoute
    */
    private $currentlyRoute = NULL;

    /**
    | ----------------------------------------------------------------------------
    | Stores the middlewares for validate the request before to be conclud response
    | ----------------------------------------------------------------------------
    * @return array $middleware
    */
    private $middleware = [];

    /**
    | ----------------------------------------------------------------------------
    | Call auxilar callback function
    | ----------------------------------------------------------------------------
    * @param \Closure $callback the function to be executed
    * @return void
    */
    public function addHelper($callback)
    {
        call_user_func($callback, $this);
    }

    /**
    | ----------------------------------------------------------------------------
    | Sets the requests by GET
    | ----------------------------------------------------------------------------
    * @param string $url the path of the request
    * @param mixed $module the controller@action or a callback function to be call
    */
    public function get($url, $module)
    {
        $this->storageRequest($url, $module, 'GET');
    }

    /**
    | ----------------------------------------------------------------------------
    | Sets the requests by POST
    | ----------------------------------------------------------------------------
    * @param string $url the path of the request
    * @param mixed $module the controller@action or a callback function to be call
    */
    public function post($url, $module)
    {
        $this->storageRequest($url, $module, 'POST');
    }

    /**
    | ----------------------------------------------------------------------------
    | Sets the requests by PUT
    | ----------------------------------------------------------------------------
    * @param string $url the path of the request
    * @param mixed $module the controller@action or a callback function to be call
    */
    public function put($url, $module)
    {
        $this->storageRequest($url, $module, 'PUT');
    }

    /**
    | ----------------------------------------------------------------------------
    | Sets the requests by PATCH
    | ----------------------------------------------------------------------------
    * @param string $url the path of the request
    * @param mixed $module the controller@action or a callback function to be call
    */
    public function patch($url, $module)
    {
        $this->storageRequest($url, $module, 'PATCH');
    }

    /**
    | ----------------------------------------------------------------------------
    | Sets the requests by DELETE
    | ----------------------------------------------------------------------------
    * @param string $url the path of the request
    * @param mixed $module the controller@action or a callback function to be call
    */
    public function delete($url, $module)
    {
        $this->storageRequest($url, $module, 'DELETE');
    }

    /**
    | ----------------------------------------------------------------------------
    | Boot The application
    | validate middlewares
    | Load the controller 
    | and finish call the action requested passing the params in request
    | ----------------------------------------------------------------------------
    */
    public function run()
    {
        (new Request())->validateSession();
        
        try{
            $this->loadModule();

            if( !empty($this->middleware) )
            {
                foreach($this->middleware as $event){
                    $eventName = "validate".ucfirst($event);
                    if( is_callable([$this, $eventName]))
                    {
                        $reason = $this->{$eventName}();
                        if( is_array($reason) ){
                            throw new Exception($reason[1], $reason[0]);
                        }
                    }
                    else{
                        throw new Exception("method not found '{$eventName}'", 500);
                    }
                }
            }

            $controller = $this->getController();
            $action = $this->getAction();
            $params = $this->getParams();
            
            if( is_string($controller) ){
                $model = str_replace('Controller','',$controller);
                $controller = "App\\Controllers\\{$controller}";
                $model = "App\\Models\\{$model}";
                
                if( class_exists($controller) ) {
                    $this->finisher($controller, $action, $params, $model);                
                }
                else{
                    throw new Exception("Class '{$controller}' Not Found!", 500);
                }
            } elseif(is_callable($controller) ) {
                call_user_func($controller, $params);
            } else{
                throw new Exception("Request Page Not found", 404);
            }
        } catch(\Exception $e){
            $this->packageFail($e->getCode(), $e->getMessage());
        }
    }

    /**
    | ----------------------------------------------------------------------------
    | Returns the current Controller
    | ----------------------------------------------------------------------------
    * @return string
    */
    private function getController()
    {
        return $this->controller;
    }

    /**
    | ----------------------------------------------------------------------------
    | Returns the current Action request
    | ----------------------------------------------------------------------------
    * @return string
    */
    private function getAction()
    {
        return $this->action;
    }

    /**
    | ----------------------------------------------------------------------------
    | Returns the current arguments sent in the request
    | ----------------------------------------------------------------------------
    * @return string
    */
    private function getParams()
    {
        return $this->params;
    }
    
    /**
    | ----------------------------------------------------------------------------
    | Store the url, module and method of the request
    | the standard of url is 'path/{argument}/to/{:optional}/page'
    | ----------------------------------------------------------------------------
    * @param string $url the path of the request
    * @param mixed $module the controller@action or a callback function to be call
    * @param string $method the HTTP method
    */
    private function storageRequest($url, $module, $method)
    {
        $this->requestList[] = [
            "url" => $url, 
            "module" => $module,
            "method" => $method,
            "params" => []
        ];
    }
    
    /**
    | ----------------------------------------------------------------------------
    | Finalize the validations an call the current action valid
    | inject by default the Request end Response in the params of the action call
    | ----------------------------------------------------------------------------
    * @param Controller $controller The current controller
    * @param string $action The current action
    * @param array $params The list of arguments sent in request
    * @return void
    */
    private function finisher($controller, $action, $params, $model = NULL)
    {
        $this->validateClassLoaded($controller, $action, $params);
        $model = ( class_exists($model) ? new $model() : NULL );
        $controller = new $controller($model);
        
        if (!is_callable(array($controller, $action))) {
            $this->packageFail(404, 'Request action Not found');
        }
        
        $params[] = new Request();
        $params[] = new Response();
        
        call_user_func_array(array($controller, $action), $params);
    }

    /**
    | ----------------------------------------------------------------------------
    | Validate the argument of the current '$controller->$action'
    | check if the argument of the route have the same name that arguments of $action
    | check if the $action has required arguments not sent in request
    | ----------------------------------------------------------------------------
    * @param Controller $controller The current controller
    * @param string $action The current action
    * @param array &$params The list of arguments referenced
    * @return void
    */
    private function validateClassLoaded($controller, $action, &$params)
    {
        $f = new \ReflectionMethod($controller, $action);
        $listArguments = array_filter($f->getParameters(), function($d){
            return !(preg_match("/(request|response)/",$d->name));
        });
        
        foreach ($listArguments as $argId => $param)
        {
            if( !isset($params[$param->name]) )
            {
                throw new Exception("Route:'{$this->currentlyRoute}', Argument(".($argId+1).") must have the name equal to '{$param->name}'", 404);
            }
            if( !$param->isDefaultValueAvailable() && ($params[$param->name] == '') )
            {
                throw new Exception("Route:'{$this->currentlyRoute}', Arguments '{$param->name}' is required In action '{$action}'!", 404);
            }
            
            $params[$param->name] = ($params[$param->name] == '' ? $param->getDefaultValue() : $params[$param->name]);
        }
    }
    
    /**
    | ----------------------------------------------------------------------------
    | By path_info gets the current request, 
    | and validate if it is stored in the list of requests defined, 
    | with the same path, params and http status code
    | if found load the controller, action and params
    | the arguments in path '{param}' id added in the global _get
    | ----------------------------------------------------------------------------
    * @return void
    */
    private function loadModule()
    {
        $currentURL = [];
        $currentPath = $this->urlToArray($this->pathinfo());
        
        foreach( $this->requestList as $request )
        {
            $current = [];
            $params = [];
            $routes = $this->urlToArray($request['url']);
            
            foreach($routes as $key => $item)
            {
                $value = $item;
                $valid = isset($currentPath[$key]);
                /**
                * @todo validate if the current item of the 'url' is a argument
                * should be an argument required or optional and the $key exists in $currentPath
                * or to be an optional argument and the $key not exists in $currentPath
                */
                if( (preg_match("/(\{:?\w{1,}\})/", $item) && $valid ) || ( preg_match("/(\{:\w{1,}\})/", $item) && !$valid ) )
                {
                    $value = ($valid ? $currentPath[$key] : "");                    
                    $params[preg_replace("/:|\{|\}/", '', $item)] = $value;
                }
                
                $current[] = $value;
            }
            $path = join("/", $currentPath);
            $linePath  = join('/', array_filter($current));
            
            if( ($linePath == $path) && $this->isMethod($request['method']) )
            {
                $_GET = array_merge($_GET, $params);
                $request['params'] = $params;
                $currentURL = $request;
                break;
            }
        }
        
        if( !empty($currentURL) ){
            $this->currentlyRoute = $currentURL['url'];
            $this->verifyModule($currentURL['module'], $request['params']);
        }
        else{
            throw new Exception("Request Page Not found", 404);
        }
    }

    /**
    | ----------------------------------------------------------------------------
    | Validate the type of data stored in current module, 
    | and load the controller and action
    | ----------------------------------------------------------------------------
    * @param mixed $string The current controller or callback to be call
    * @param array $params the params to be set in the action
    * @return void
    */
    private function verifyModule($string, $params = [])
    {
        $this->params = $params;
        
        if( is_array($string) )
        {
            if( array_key_exists("uses",$string) ){
                list($this->controller, $this->action) = explode("@", $string['uses']);
            }
            if( array_key_exists("middleware", $string)){
                $this->middleware = $string["middleware"];
            }
            // if( array_key_exists("module", $string) ){
            //     $this->controller = "{$string["module"]}\\{$this->controller}";
            // }
        }
        elseif(is_string($string)) {
            list($this->controller, $this->action) = explode("@", $string);
        }
        elseif( is_callable($string) ) {
            $this->controller = $string;
        }
    }

    /**
    | ----------------------------------------------------------------------------
    | Call the default controller with error method to show the request errors
    | ----------------------------------------------------------------------------
    * @param int $status the status http to set in the request
    * @param string $message the message custom to set in response
    * @return void
    */
    private function packageFail($status, $message = '')
    {
        $defaultController = new \App\Controllers\BaseController();
        call_user_func_array(array($defaultController, 'error'), [$status, $message, new Response()]);
    }
}