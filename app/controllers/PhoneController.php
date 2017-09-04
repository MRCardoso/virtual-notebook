<?php

namespace App\Controllers;

use Library\Marlon\Http\Request;
use Library\Marlon\Http\Response;
use App\Models\Phone;
use App\Models\Person;

class PhoneController extends BaseController
{
    protected $baseRoute = "person.{personId}.phones";

    public function create($personId, Request $request, Response $response)
    {
        $this->storePrevious();
        $model = new Phone();
        $model->status = 1;
        if( !empty($post = $request->all()) )
        {
            $post['personId'] = $personId;
            
            if( $model->save($post) )
            {
                $this->setFlash("alert-success", "Telefone Criado com Sucesso!");
                return $response->redirect("{$this->baseRoute}.update.{id}",[
                    "personId" => $personId, 
                    "id" => $model->id
                ]);
            }
        }
        $route = $this->route("{$this->baseRoute}.create", compact('personId'));
        $this->view('phone.save', compact('request', 'model', 'route'));
    }

    public function update($personId, $id, Request $request, Response $response)
    {
        $this->storePrevious();
        $model = $this->loadModel(compact('id', 'personId'), $response);

        if( !empty($post = $request->all()) )
        {
            if( $model->save($post) ){
                $this->setFlash("alert-success", "Telefone Atualizado com Sucesso!");
            }
        }
        $route = $this->route("{$this->baseRoute}.update.{id}", compact('personId', 'id'));
        $this->view('phone.save', compact('request', 'model', 'route'));
    }

    public function remove($personId, $id, Request $request, Response $response)
    {
        $model = $this->loadModel(compact('id', 'personId'), $response);
        
        if($model->delete())
            $this->setFlash("alert-success", "Telefone Removido com Sucesso!");
        
        $response->redirect('notebook');
    }
}