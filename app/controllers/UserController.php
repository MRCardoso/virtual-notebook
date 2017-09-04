<?php

namespace App\Controllers;

use Library\Marlon\Http\Response;
use Library\Marlon\Http\Request;
use App\Models\User;

class UserController extends BaseController
{
    public function signin(Request $request, Response $response)
    {
        $model = new User();
        if( !empty($post = $request->all()) )
        {
            if( ($user = $model->authenticate($post)) !== FALSE )
            {
                $request->login($user);
                return $response->redirect("notebook");
            }
        }
        $this->view("user.signin", compact('model'));
    }

    public function signup(Request $request, Response $response)
    {
        $model = new User();
        if( !empty($post = $request->all()) )
        {
            if( ($user = $model->createUser($post)) !== FALSE )
            {
                $request->login($user);
                return $response->redirect("notebook");
            }
        }
        $this->view("user.signup", compact('request', 'model'));
    }

    public function signout(Request $request, Response $response)
    {
        $request->logout();
        $response->redirect();
    }

    public function myData(Request $request, Response $response)
    {
        $model = $this->loadModel(auth('id'), $response);

        if( !empty($post = $request->all()) )
        {
            if( $model->updateUser($post) ){
                $request->regenerate($model);
                $this->setFlash("alert-success", "Dados Atualizados com sucesso!");
            }
        }
        $this->view("user.save", compact('request', 'model'));
    }
    
    public function forgot(Request $request, Response $response)
    {
        $model = new User();
        if( !empty($post = $request->all()) )
        {
            if( ($user = $model->sendTokenReset($post['email'])) !== FALSE )
            {
                $this->setFlash("alert-success", "E-mail encaminhado com sucesso!");
                return $response->redirect();
            }
        }
        $this->view("user.forgot", compact('request', 'model'));
    }

    public function reset($token, Request $request, Response $response)
    {
        $model = $this->model->findByToken($token);
        
        if( $model === FALSE){
            return $this->error(404, 'Este token já expirou!', $response);
        }
        
        if( !empty($post = $request->all()) )
        {
            if( ($model->passwordReset($post)) !== FALSE )
            {
                $request->login($model);
                return $response->redirect("myData");
            }
        }
        $this->view("user.reset", compact('token', 'model'));
    }
}