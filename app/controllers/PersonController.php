<?php

namespace App\Controllers;

use Library\Marlon\Http\Request;
use Library\Marlon\Http\Response;
use App\Models\Person;

class PersonController extends BaseController
{
    public function index($letter = 'A', Request $request)
    {
        $_SESSION['previousURL'] = NULL;
        $letters = [];
        foreach ($this->model->where('userId', '=', auth('id'))->find(['nickname']) as $person) {
            $letters[] = strtoupper(substr($person['nickname'],0,1));
        }
        sort($letters);
        $letters = array_unique($letters);
        
        $people = Person::where('userId', '=', auth('id'));
        $people->andWhere('nickname', 'ilike', "{$letter}%");
        
        if( !empty($request->query('query')) ){
            $people->andWhere('nickname', 'ilike', "%{$request->query('query')}%");
        }

        $people = $people
            ->with(['emails', 'phones'])
            ->order(['person.id DESC'])
            ->paginate($request->query('page', 1));
        
        $this->view('person.index', compact('people', 'letters', 'request'));
    }

    public function create(Request $request, Response $response)
    {
        $this->storePrevious();
        $model = new Person();
        $model->sex = 'M';
        $model->status = 1;
        if( !empty($post = $request->all()) )
        {
            $post['userId'] = auth('id');
            if( $model->save($post) ) {
                $this->setFlash("alert-success", "Pessoa Criado com Sucesso!");
                
                return $response->redirect("person.update.{id}",["id" => $model->id]);
            }
        }
        
        $route = $this->route("person.create");
        $this->view('person.save', compact('request', 'model', 'route'));
    }

    public function update($id, Request $request)
    {
        $this->storePrevious();
        $model = $this->model->findByPk($id);
        
        if( !empty($post = $request->all()) )
        {
            if( $model->save($post) )
                $this->setFlash("alert-success", "Pessoa Atualizado com Sucesso!");
        }
        $route = $this->route("person.update.{id}", compact('id'));
        $this->view('person.save', compact('request', 'model', 'route'));
    }

    public function remove($id, Request $request, Response $response)
    {
        $model = $this->loadModel($id, $response);
        $name = strtoupper(substr($model->name,0,1));
        
        if($model->delete())
            $this->setFlash("alert-success", "Pessoa Removida com Sucesso!");
        
        $response->redirect('notebook',['letter' => $name]);
    }
}