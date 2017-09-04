<?php

namespace App\Controllers;

use Library\Marlon\Modules\Controller;
use Library\Marlon\Http\Response;
use Library\Marlon\Http\Request;
use App\Models\User;

class BaseController extends Controller
{
    public function welcome()
    {
        $this->view("welcome");
    }

    public function loadModel($id, Response $response)
    {
        $model = $this->model->findByPk($id);
        if( empty($model) ){
            $this->error(404, 'Registro não encontrado!', $response);
        }

        return $model;
    }

    protected function storePrevious()
    {
        if( $this->isMethod('get') && empty($_SESSION['previousURL']) ){
            $this->setFlash("previousURL", $this->previousUrl());
        }
    }
}