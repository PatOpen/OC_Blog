<?php

use Whoops\Handler\PrettyPageHandler;

require '../vendor/autoload.php';
require '../controllers/Router.php';

//Debug error
$whoops = new Whoops\Run;
$whoops->pushHandler(new PrettyPageHandler);
$whoops->register();

//Twig
$loader = new Twig\Loader\FilesystemLoader('../views');
$twig = new Twig\Environment($loader);

//Sessions
//OC_Blog\Tools\Session::newSession();

//Router
$router = new Router();
$router->routeRequest($twig);