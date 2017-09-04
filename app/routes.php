<?php
    /*
    | ------------------------------------------------------------------------------
    | REQUESTS WITHOUT AUTHENTICATION REQUIREMENT
    | ------------------------------------------------------------------------------
    */
    // CREATE ACCOUNT
    $app->get('/signup', ["uses"=>"UserController@signup", "middleware" => ["guest"]]);
    $app->post('/signup', ["uses"=>"UserController@signup", "middleware" => ["guest"]]);
    // LOGIN
    $app->get('/signin', ["uses"=>"UserController@signin", "middleware" => ["guest"]]);
    $app->post('/signin', ["uses"=>"UserController@signin", "middleware" => ["guest"]]);
    // LOGOUT
    $app->get('/signout', "UserController@signout");
    // FORGOT
    $app->get('/forgot', ["uses"=>"UserController@forgot", "middleware" => ["guest"]]);
    $app->post('/forgot', ["uses"=>"UserController@forgot", "middleware" => ["guest"]]);
    // RESET
    $app->get('/reset/{token}', ["uses"=>"UserController@reset", "middleware" => ["guest"]]);
    $app->patch('/reset/{token}', ["uses"=>"UserController@reset", "middleware" => ["guest"]]);
    
    /*
    | ------------------------------------------------------------------------------
    | REQUESTS WIT AUTHENTICATION REQUIREMENT
    | ------------------------------------------------------------------------------
    */
    $app->get('/', "BaseController@welcome");
    $app->get('/myData', [
        "uses" => "UserController@myData",
        "middleware" => ["auth"],
    ]);
    $app->patch('/myData', [
        "uses" => "UserController@myData",
        "middleware" => ["auth"],
    ]);

    $app->get('/notebook/{:letter}', [
        "uses" => "PersonController@index",
        "middleware" => ["auth"],
    ]);
    $app->get('/person/create', [
        "uses" => "PersonController@create",
        "middleware" => ["auth"],
    ]);
    $app->post('/person/create', [
        "uses"=>"PersonController@create",
        "middleware" => ["auth"],
    ]);
    $app->get('/person/update/{id}', [
        "uses"=>"PersonController@update",
        "middleware" => ["auth"],
    ]);
    $app->put('/person/update/{id}', [
        "uses"=>"PersonController@update",
        "middleware" => ["auth"],
    ]);
    $app->delete('/person/remove/{id}', [
        "uses"=>"PersonController@remove",
        "middleware" => ["auth"],
    ]);

    $app->get('/person/{personId}/emails/create', [
        "uses"=> "EmailController@create",
        "middleware" => ["auth"],
    ]);
    $app->post('/person/{personId}/emails/create', [
        "uses"=> "EmailController@create",
        "middleware" => ["auth"],
    ]);
    $app->get('/person/{personId}/emails/update/{id}', [
        "uses"=> "EmailController@update",
        "middleware" => ["auth"],
    ]);
    $app->put('/person/{personId}/emails/update/{id}', [
        "uses"=> "EmailController@update",
        "middleware" => ["auth"],
    ]);
    $app->delete('/person/{personId}/emails/remove/{id}', [
        "uses"=> "EmailController@remove",
        "middleware" => ["auth"],
    ]);
    
    $app->get('/person/{personId}/phones/create', [
        "uses"=> "PhoneController@create",
        "middleware" => ["auth"],
    ]);
    $app->post('/person/{personId}/phones/create', [
        "uses"=> "PhoneController@create",
        "middleware" => ["auth"],
    ]);
    $app->get('/person/{personId}/phones/update/{id}', [
        "uses"=> "PhoneController@update",
        "middleware" => ["auth"],
    ]);
    $app->put('/person/{personId}/phones/update/{id}', [
        "uses"=> "PhoneController@update",
        "middleware" => ["auth"],
    ]);
    $app->delete('/person/{personId}/phones/remove/{id}', [
        "uses"=> "PhoneController@remove",
        "middleware" => ["auth"],
    ]);