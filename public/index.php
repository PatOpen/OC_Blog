<?php

require '../vendor/autoload.php';

//Debug error
$whoops = new Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();

//Twig
$loader = new Twig\Loader\FilesystemLoader( dirname( __DIR__ ) . '/views' );
$twig = new Twig\Environment($loader,[]);

//Router
$router = new Router();
$router->routeRequest();