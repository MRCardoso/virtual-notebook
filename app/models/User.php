<?php

namespace App\Models;

use Library\Marlon\Modules\Model;
use Library\Marlon\Core\MyCrypt;

class User extends Model
{    
    protected $fillables = ["name", "email", "username", "password", "status"];
    
    protected $rules = [
        "name"          => "required",
        "username"      => "required|unique:user",
        "email"         => "required|email|unique:user",
        "password"      => "required|min:8|max:255",
    ];

    protected $table = 'user';

    private $myCrypt = NULL;
    
    public function __construct()
    {
        parent::__construct();

        $this->myCrypt = new MyCrypt();
    }
    
    /**
    | ----------------------------------------------------------------------------
    | Create a new user in the system
    | ----------------------------------------------------------------------------
    * @param array $data
    * @return bool||\App\Models\User
    */
    public function createUser($data)
    {
        $this->name = $data["name"];
        $this->email = $data["email"];
        $this->username = $data["username"];
        $this->password = $data["password"];
        
        if( $this->validate() )
        {
            $data["password"] = $this->myCrypt->hash($data['password']);
            if( $this->create($data) )
            {
                return $this->findByPk($this->id);
            }
        }

        return FALSE;
    }

    /**
    | ----------------------------------------------------------------------------
    | Validate username and password and Authenticate the user found in the system
    | ----------------------------------------------------------------------------
    * @param array $data
    * @return bool||\App\Models\User
    */
    public function authenticate($data)
    {
        $this->username = $data['username'];
        $user = $this->findUsername($this->username);
        
        if( $user === FALSE ){
            $this->setErrors("username", $this->processMessages("unknownUser", ['field'=> $this->username]));
            return FALSE;
        }
        
        if( ($user->verifyPassword($data['password'])) === FALSE ){
            $this->setErrors("password", $this->processMessages("unknownPasswd", ['field'=> $data['password']]));
            return FALSE;
        }

        return $user;
    }

    /**
    | ----------------------------------------------------------------------------
    | Update the data of a user, all data or the basic data
    | ----------------------------------------------------------------------------
    * @param array $data
    * @return bool
    */
    public function updateUser(array $data)
    {
        if( empty($this->id) ) return FALSE;
        
        $allowed = $this->bindFillables($data);
        
        if( isset($data['password']) && isset($data['new_password']) )
        {
            $checkin = $this->verifyPassword($data['password']);
            if( empty($data['new_password']) ){
                $this->setErrors("password", $this->processMessages("required", ['field'=> "Nova Senha"]));
                return FALSE;
            }
            $this->password = $data['new_password'];
            $allowed['passwordChange'] = $this->passwordChange = date('Y-m-d H:i:s');
        }

        if( $this->validate() ){
            return $this->update($allowed);
        }
        return FALSE;
    }

    /**
    | ----------------------------------------------------------------------------
    | Sent an email with token to recovery password
    | validate if the email address exist in the system
    | create a token, update data of the user(resetToken,resetExpires,password-random passwd)
    | and send an email for email found
    | ----------------------------------------------------------------------------
    * @param string $email the user email
    * @return bool
    */
    public function sendTokenReset($email)
    {
        $this->email = $email;
        if( !$this->required('email') ){
            $this->setErrors('email', 'E-mail não informado!');
            return FALSE;
        }
        $user = $this->findEmail($email);
        if( $user === FALSE ){
            $this->setErrors("email", $this->processMessages("unknownUser", ['field'=> $this->email]));
            return FALSE;
        }
        
        $token = $this->myCrypt->token($user->email);
        $tempPasswd = $this->myCrypt->hash(time());
        $expires = date('Y-m-d H:i:s', strtotime('+20 minutes'));

        $save = $this->db->update([
            "password" => $tempPasswd,
            "resetToken" => $token,
            "resetExpires" => $expires,
        ], ['id' => $user->id]);

        if( $save )
        {
            $url = $GLOBALS['app']->baseUrl("reset/{$token}");
            $title = "Token de Recuperação de senha.";
            
            return sendMail($title, $user->email, "email".DS."reset", compact('url', 'token'), $user->name);
        }

        return FALSE;
    }

    /**
    | ----------------------------------------------------------------------------
    | Update the password of a user
    | ----------------------------------------------------------------------------
    * @param array $data
    * @return bool
    */
    public function passwordReset(array $data)
    {
        if( empty($data['password']) || empty($data['confirmation']) )
        {
            $this->setErrors("password", $this->processMessages("required", ['field'=> "Senha"]));
            return FALSE;
        }

        if( $data['password'] != $data['confirmation']){
            $this->setErrors("password", $this->processMessages("confirmation", []));
            return FALSE;
        }

        $allowed = [
            "resetToken" => null,
            "resetExpires" => null,
            "password" => $this->myCrypt->hash($data['password']),
            "passwordChange" => date('Y-m-d H:i:s')
        ];
        
        return $this->update($allowed);
    }
    
    /**
    | ----------------------------------------------------------------------------
    | Load a instance of the class user by username
    | ----------------------------------------------------------------------------
    * @param string $username
    * @return \App\Models\User
    */
    public function findUsername($username)
    {
        return $this->db->where(["username" => $username, "status" => 1])->findObject();
    }

    /**
    | ----------------------------------------------------------------------------
    | Load a instance of the class user by email
    | ----------------------------------------------------------------------------
    * @param string $email
    * @return \App\Models\User
    */
    public function findEmail($email)
    {
        return $this->db->where(["email" => $email, "status" => 1])->findObject();
    }

    /**
    | ----------------------------------------------------------------------------
    | Load a instance of the class user by token for recovery password
    | ----------------------------------------------------------------------------
    * @param string $token
    * @return \App\Models\User
    */
    public function findByToken($token)
    {
        return $this->db
            ->where("resetToken", "=", $token)
            ->andWhere("resetExpires", ">", date('Y-m-d H:i:s'))
            ->andWhere("status", "=", 1)
            ->findObject();
    }

    /**
    | ----------------------------------------------------------------------------
    | Check if the password informed is equal to the password of the loaded user
    | ----------------------------------------------------------------------------
    * @param string $password the password text without hash
    * @return bool
    */
    public function verifyPassword($password)
    {
        $checkin = $this->myCrypt->check($password, $this->password);
        
        if( $checkin === FALSE ){
            $this->setErrors("password", $this->processMessages("unknownPasswd", ['field'=> $password]));
            return FALSE;
        }
        return TRUE;
    }
}