<?php
/**
| ----------------------------------------------------------------------------
| Class with responsability for serve the back-end and front-end
| with data post and get created in the request
| create, regenerate and destroy a session for the user
| ----------------------------------------------------------------------------
* PHP version 7.1
*/

namespace Library\Marlon\Http;

class Request
{
    /**
    | ----------------------------------------------------------------------------
    | Get all POST, PUT or PATCH data in the request
    | ----------------------------------------------------------------------------
    * @return array the post data
    */
    public function all()
    {
        if( preg_match("/(put|patch)/", strtolower($_SERVER['REQUEST_METHOD'])) ){
            parse_str(file_get_contents("php://input"),$post);
            return $post;
        }
        return $_POST;
    }

    /**
    | ----------------------------------------------------------------------------
    | Gets all or specific GET data in the request
    | ----------------------------------------------------------------------------
    * @param string $key the index of the GET
    * @param string $default the default value in case of the $key not found
    * @return mixed the get data
    */
    public function query($key = NULL, $default = '')
    {
        $get = $_GET;

        if( $key == NULL ) return $get;
        
        return ( array_key_exists($key, $get) ? $get[$key] : $default );
    }

    /**
    | ----------------------------------------------------------------------------
    | Gets the specific POST index
    | ----------------------------------------------------------------------------
    * @param string $key the index of the POST
    * @param string $default the default value in case of the $key not found
    * @return mixed the POST item
    */
    public function input($key, $default = '')
    {
        $post = $this->all();
        
        return ( array_key_exists($key, $post) ? $post[$key] : $default );
    }

    /**
    | ----------------------------------------------------------------------------
    | Validate if the has a session started
    | ----------------------------------------------------------------------------
    */
    public function validateSession()
    {
        session_start();
        #verifica se o usuario esta logado, se nã estive redireciona para página de login
        if( !isset($_SESSION['userData']) )
        {
            session_destroy();
        }
    }

    /**
    | ----------------------------------------------------------------------------
    | Create a session with data of the user find
    | ----------------------------------------------------------------------------
    */
    public function login($user)
    {
        session_start();

        $_SESSION['userData'] = $user->getAttributes();
        $_SESSION['userData']["sessionId"] = md5(session_id());
    }

    /**
    | ----------------------------------------------------------------------------
    | Destroy the current session
    | ----------------------------------------------------------------------------
    */
    public function logout()
    {
        unset($_SESSION['userData']);
        session_destroy();
    }

    /**
    | ----------------------------------------------------------------------------
    | Regenerate the session data when the auth user change your data
    | ----------------------------------------------------------------------------
    */
    public function regenerate($user)
    {
        foreach( $user->getAttributes() as $key => $item)
        {
            if( array_key_exists($key, $_SESSION['userData']))
            {
                $_SESSION['userData'][$key] = $item;
            }
        }
    }
}