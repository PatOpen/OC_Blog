<?php

//use DI\ContainerBuilder;
use OC_Blog\Controllers\Router;
use Whoops\Handler\PrettyPageHandler;

//Chargement de l'autoload de composer
require (dirname(__DIR__).DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php');

//Conteneur de dépendance
/*$builder = new ContainerBuilder();
try {
	$builder->addDefinitions(dirname(__DIR__).'/config/config.php');
	$container = $builder->build();
} catch ( Exception $e ) {
}*/

//Debug error
$whoops = new Whoops\Run;
$whoops->pushHandler(new PrettyPageHandler);
$whoops->register();

//Twig
$loader = new Twig\Loader\FilesystemLoader('../views');
$twig = new Twig\Environment($loader);

//Sessions
OC_Blog\Tools\Session::getSession();

//Instanciation du routeur
$router = new Router();
$router->run($twig);
