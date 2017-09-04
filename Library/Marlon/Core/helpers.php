<?php
    /*
    | ----------------------------------------------------------------
    | Get all data stored in global variable appConfig
    | ----------------------------------------------------------------
    */
    if( !function_exists('appConfig') )
    {
        function appConfig($key = NULL)
        {
            if( $key != NULL && array_key_exists($key, $GLOBALS['appConfig']))
                return $GLOBALS['appConfig'][$key];
            
            return $GLOBALS['appConfig'];
        }
    }

    /*
    | ----------------------------------------------------------------
    | Returns the data of the authenticated user, when have a session booted
    | ----------------------------------------------------------------
    */
    if( !function_exists('auth') )
    {
        function auth($key = NULL)
        {
            if( isset($_SESSION) && isset($_SESSION['userData']) )
            {
                if( $key != NULL && array_key_exists($key, $_SESSION['userData']))
                    return $_SESSION['userData'][$key];
                
                return (Object) $_SESSION['userData'];
            }
            return NULL;
        }
    }

    /*
    | ----------------------------------------------------------------
    | Returns the environment variable of the system
    | ----------------------------------------------------------------
    */
    if( !function_exists('env') )
    {
        function env($key, $defaultValue = NULL)
        {
            $data = getenv($key);
            return $data === FALSE ? $defaultValue : $data;
        }
    }
    
    /*
    | ----------------------------------------------------------------
    | Send email with library php
    | ----------------------------------------------------------------
    */
    if( !function_exists('sendMail') )
    {
        function sendMail($title, $email, $layout = NULL, $params = [], $name = NULL)
        {
            $name = ($name == NULL ? $email : $name);
            
            if( $layout != NULL )
            {
                ob_start();
                require ROOT.DS.'public'.DS.'templates'.DS."{$layout}.html";
                $content = ob_get_clean();
                foreach($params as $key => $value ){
                    $content = str_replace("{{$key}}", $value, $content);
                }
            }
            else
            {
                $content = $title;
            }

            $mail = new \Library\Marlon\Core\MyMailer();
            $mail->from(env('MAIL_EMAIL', 'admin@virtual'), env('APP_MANAGER', 'noone'));
            $mail->to($email, $name);
            $mail->isHtml();
            $mail->subject($title);
            $mail->body($content, $title);
    
            return $mail->send();
        }
    }