<?php
/**
| ----------------------------------------------------------------------------
| Controller layer that gets the request of the views, communicate with the model appropriated
| ----------------------------------------------------------------------------
* PHP version 7.1
*/

namespace Library\Marlon\Modules;

use Library\Marlon\Http\Request;
use Library\Marlon\Http\Response;

abstract class Controller
{
    use \Library\Marlon\Http\HttpResource;

    private $view = NULL;
    private $arguments = [];
    private $defaultPath = NULL;
    private $containerArgs = [];
    protected $model = NULL;

    public function __construct($model = NULL){
        $this->model = $model;
    }

    /**
    | ----------------------------------------------------------------------------
    | Concluded the request and render the main view
    | ----------------------------------------------------------------------------
    */
    public function __destruct()
    {
        if( !$this->isAjaxRequest() )
        {
            require $this->path(ROOT.'.public.templates.layout').".php";
            exit;
        }
    }

    public function currentClass()
    {
        $reflect = new \ReflectionClass(get_class($this));
        return $reflect->getShortName();
    }

    /**
    | ----------------------------------------------------------------------------
    | Store the view to be called 
    | ----------------------------------------------------------------------------
    * @param string $path the view to be call
    * @param array $arguments the arguments to be sent to the view
    * @param string $defaultPath the default base path
    */
    public function view($path, $arguments = [], $defaultPath = "app.views")
    {
        $this->view = $path;
        $this->arguments = $arguments;
        $this->defaultPath = $defaultPath;
    }
    
    /**
    | ----------------------------------------------------------------------------
    | Render the view called
    | ----------------------------------------------------------------------------
    */
    public function content()
    {
        if( $this->view !== NULL )
        {
            extract($this->arguments);
            
            require_once $this->path(ROOT.".{$this->defaultPath}.{$this->view}").'.php';
        }
    }

    /**
    | ----------------------------------------------------------------------------
    | Render a partial view
    | ----------------------------------------------------------------------------
    * @param string $path the view to be call
    * @param array $arguments the arguments to be sent to the view
    * @param bool $isViewPath the default base path
    */
    public function partial($path, $arguments = [], $isViewPath = TRUE)
    {
        $defaultPath = ($isViewPath ? 'app.views' : 'public.templates');
        
        if( $path[0] != "/" ){
            $pathByCtrl = str_replace("controller", "", strtolower($this->currentClass()));
            $path = "{$pathByCtrl}.{$path}";
        }
        $path = str_replace('/', '', $path);
        extract($arguments);
        
        require $this->path(ROOT.".{$defaultPath}.{$path}").'.php';
    }
    
    /**
    | ----------------------------------------------------------------------------
    | render a file to the views the scripts and styles
    | ----------------------------------------------------------------------------
    * @param string $string the path of the file
    * @param array $ext the extension of the file
    * @param bool $spliter the default separator
    */
    public function assets($string, $ext, $spliter = '.')
    {
        return $this->baseUrl($this->path("public{$spliter}{$string}", DS, $spliter).".{$ext}");
    }

    /**
    | ----------------------------------------------------------------------------
    | create a request and redirect with params
    | ----------------------------------------------------------------------------
    * @param string $path the path to be called
    * @param array $params the arguments of the request
    */
    public function route($path, $params = [])
    {
        foreach($params as $key => $item)
        {
            if(preg_match("/{\w{1,}}/", $path) )
                $path = str_replace("{{$key}}", $item, $path); 
            else
                $path .= ".{$item}";
        }
        return $this->baseUrl($this->path($path, '/'));
    }

    /**
    | ----------------------------------------------------------------------------
    | Render an page with error setting the status http according the error
    | ----------------------------------------------------------------------------
    * @param string $status status code
    * @param array $message additional message with error
    * @param Response $response class with response methods
    */
    public function error($status, $message = '', Response $response)
    {
        $title = $response->getMessage($status);
        if( $this->isAjaxRequest() )
        {
            return $response->json($status, ['message' => $message], $title);
        }
        else{
            $response->status($status);
            $dump = debug_backtrace();
            $this->view('errors.page', compact('status','title','message','dump'), "public.templates");
            exit;
        }
    }

    /**
    | ----------------------------------------------------------------------------
    | Render a partial for pagination in the view
    | ----------------------------------------------------------------------------
    * @param string $data list of params to be sent in the view
    */
    public function pager($data)
    {
        $data['letter'] = (new Request)->query('letter');
        return $this->partial("/partials._pagination", $data, FALSE);
    }

    public function container($params, $defaultView = '_container')
    {
        ob_start();
        $this->containerArgs = $params;
        $this->containerArgs['view'] = $defaultView;
        $this->containerArgs['action'] = empty($params['model']->id) ? 'Create' : 'Update';
    }

    public function closeContainer()
    {
        $this->containerArgs['content'] = ob_get_clean();
        
        return $this->partial("/partials.{$this->containerArgs['view']}", $this->containerArgs, FALSE);
    }
}