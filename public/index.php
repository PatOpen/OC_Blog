<?php

use OC_Blog\Controllers\Router;
use OC_Blog\Tools\Session;
use Twig\Extra\String\StringExtension;
use Whoops\Handler\PrettyPageHandler;

//Chargement de l'autoload de composer
require (dirname(__DIR__).DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php');


//Debug error
$whoops = new Whoops\Run;
$whoops->pushHandler(new PrettyPageHandler);
$whoops->register();

//Twig
$loader = new Twig\Loader\FilesystemLoader('../views');
$twig = new Twig\Environment($loader);
$twig->addExtension(new StringExtension());

//Sessions
Session::getSession();

//Instanciation du routeur
$router = new Router();
$router->run($twig);
