<?php
/**
| ----------------------------------------------------------------------------
| Class with responsability for serve the back-end with responses
| and sets the http status code according the answers between the laywers of the application
| the request sent for the application and the app check the routes enables,
| the request method, the middlewares to be validated, 
| per ends resovle call the controller->action appripriated
| ----------------------------------------------------------------------------
* PHP version 7.1
*/

namespace Library\Marlon\Http;

class Response
{
    use \Library\Marlon\Http\HttpResource;
    
    const HTTP_OK = 200;
    const HTTP_BAD_REQUEST = 400;
    const HTTP_UNAUTHORIZED = 401;
    const HTTP_FORBIDDEN = 403;
    const HTTP_NOT_FOUND = 404;
    const HTTP_PRECONDITION_FAILE = 412;
    const HTTP_INTERNAL_SERVER_ERROR = 500;
    const HTTP_NOT_IMPLEMENTED = 501;

    private $statusText = [
        200 => "Ok",
        400 => "Bad Request",
        401 => "Unauthorized",
        403 => "Forbidden",
        404 => "Not Found",
        405 => "Method Not allowed",
        412 => "Pre-condition Failure",
        500 => "Internal Server Error",
        501 => "Not Impemented"
    ];
    
    /**
    | ----------------------------------------------------------------------------
    | Sets the status http in the header
    | ----------------------------------------------------------------------------
    * @param mixed $status the status code
    * @return \Library\Marlon\Http\Response
    */
    public function status($status)
    {
        $this->setHeader($status, $this->getMessage($status));

        return $this;
    }

    /**
    | ----------------------------------------------------------------------------
    | Sets the message in the header according the status code
    | ----------------------------------------------------------------------------
    * @param mixed $status the status code
    */
    public function getMessage($status)
    {
        return $this->checkStatus($status) ? $this->statusText[$status] : $this->statusText[500];
    }

    /**
    | ----------------------------------------------------------------------------
    | Response the request with an json data, and change the content-type to json
    | ----------------------------------------------------------------------------
    * @param mixed $status the status code
    */
    public function json($status, $data = [], $message = 'ok')
    {
        $this->setHeader($status, $message, 'application/json');
        echo json_encode($data);
        exit;
    }

    /**
    | ----------------------------------------------------------------------------
    | Create a redirect in the request
    | ----------------------------------------------------------------------------
    * @param string $path the path to be redirect
    * @param array $params de additional arguments
    */
    public function redirect($path = "", $params = [])
    {
        foreach($params as $key => $item)
        {
            if(preg_match("/{\w{1,}}/", $path) )
                $path = str_replace("{{$key}}", $item, $path); 
            else
                $path .= ".{$item}";
        }
        
        header('Location: '.$this->baseUrl($this->path($path)), TRUE, 302);
        session_write_close();
        exit();
    }

    /**
    | ----------------------------------------------------------------------------
    | Sets a status code in the response
    | ----------------------------------------------------------------------------
    * @param string $status
    * @param string $title
    * @param string $contentType
    * @return void
    */
    private function setHeader($status, $title, $contentType = 'text/html')
    {
        $status = $this->checkStatus($status) ? $status : 500;
        header("{$_SERVER["SERVER_PROTOCOL"]} {$status} {$title}", TRUE, $status);
        header("Content-type: $contentType");
    }
    private function checkStatus($status)
    {
        return (array_key_exists($status, $this->statusText) ? TRUE : FALSE);
    }
}