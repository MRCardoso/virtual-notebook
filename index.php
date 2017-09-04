<?php
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(__FILE__));

// autoload do Composer 
require ROOT.DS.'vendor'.DS.'autoload.php'; 

// as duas linhas que carregam as variáveis do .env para variáveis de ambiente 
$dotenv = new Dotenv\Dotenv( __DIR__ ); 
$dotenv->load();

require ROOT.DS.'Library'.DS.'Marlon'.DS.'core'.DS.'helpers.php';

$appConfig = require __DIR__.'/config/app.php';
$mailConfig = require __DIR__.'/config/mail.php';

// Defines The tamezone in the data of the application
if( array_key_exists('timezone', $appConfig) ){
    date_default_timezone_set($appConfig['timezone']);
}
// Defines The locale in the application
if ( array_key_exists('locale',$appConfig) ){
    setlocale(LC_ALL, $appConfig['locale']);
}

require __DIR__.'/config/autoload.php';

$app = new Library\Marlon\Core\App();

$app->addHelper(function($app){
    require __DIR__.'/app/routes.php';
});

$app->run();