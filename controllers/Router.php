<?php

namespace OC_Blog\Controllers;

use Exception;
use GuzzleHttp\Psr7\ServerRequest;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Psr7\UriNormalizer;
use OC_Blog\Config\ConstantGlobal;


class Router {


	public function run($twig){

		$url = ( new ConstantGlobal(ServerRequest::fromGlobals()) )->getServerUri();
		$url = $this->cleanUri($url);

		$this->routeRequest($twig, $url);
	}

	public function routeRequest( $twig, $uri ) {
		try {
			$url = explode( '/', filter_var( $uri, FILTER_SANITIZE_URL ) );
			if ( isset($url)) {

				$controller      = ucfirst( strtolower( $url[1] ) );
				$controllerClass = "OC_Blog\\Controllers\\Controller" . $controller;

				if ( class_exists( $controllerClass)  && empty($url[2])) {
					new $controllerClass($twig);
				}elseif (class_exists( $controllerClass)  && !empty($_POST)){
					$params = $_POST;
					new $controllerClass($url, $twig, $params);
				}
				elseif ( class_exists( $controllerClass)  && isset($url[2])) {
					new $controllerClass($url, $twig, []);
				}

				else {
					new ControllerHome($twig);
				}

			}


		} catch ( Exception $e ) {
			$errorMsg = $e->getMessage();
			echo $twig->render( '404.twig', ['error'=> $errorMsg] );
		}
	}

	public function cleanUri($url): string {
		$uri = new Uri($url);
		return UriNormalizer::normalize($uri,UriNormalizer::REMOVE_DUPLICATE_SLASHES);
	}

}