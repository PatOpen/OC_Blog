<?php

namespace OC_Blog\Controllers;

use Exception;
use GuzzleHttp\Psr7\ServerRequest;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Psr7\UriNormalizer;
use OC_Blog\Tools\ConstantGlobal;


class Router {


	public function run($twig){

		$url = ( new ConstantGlobal(ServerRequest::fromGlobals()) )->getServerUri();
		$url = $this->cleanUri($url);
		$this->routeRequest($twig, $url);
	}

	public function routeRequest( $twig, $uri ) {
		try {
			$url = explode( '/', filter_var( $uri, FILTER_SANITIZE_URL ) );
			$slug = $this->slug($url);

			if ( isset($url)) {

				$controller      = ucfirst( strtolower( $url[1] ) );
				$controllerClass = "OC_Blog\\Controllers\\Controller" . $controller;
				$method = $this->getMethod($url, $controller);

				if (class_exists( $controllerClass)  && !empty($_POST)){
					$params = $_POST;
					(new $controllerClass( $twig, $slug, $params))->$method();
				}
				elseif ( class_exists( $controllerClass)  && isset($method)) {
					(new $controllerClass($twig, $slug, []))->$method();
				}

				else {
					(new ControllerHome($twig))->home();
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

	public function getMethod(array $method, $controller) {
		if (empty($controller)){
			$controller = 'Home';
			$method = "home";
		}
		$controllerClass = "OC_Blog\\Controllers\\Controller" . $controller;

		if (method_exists("$controllerClass", $method[2]) ) {
			return $method[2];
		}else{
			return null;
		}
	}

	public function slug(array $url) {
		if (isset($url[3])){
			return $url[3];
		}else{
			return 0;
		}
	}

}