<?php

namespace OC_Blog\Controllers;

use Exception;
use GuzzleHttp\Psr7\ServerRequest;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Psr7\UriNormalizer;
use OC_Blog\Tools\ConstantGlobal;
use OC_Blog\Tools\ControllerFactory;


class Router {

	/**
	 * Décompose l'url et instancie le controller demandé.
	 *
	 * @param object $twig
	 */
	public function run(object $twig): void {

		$constant = new ConstantGlobal(ServerRequest::fromGlobals());
		$url = $constant->getServerUri();
		$uri = $this->cleanUri($url);

		try {
			$url = explode( '/', filter_var( $uri, FILTER_SANITIZE_URL ) );
			$slug = $this->slug($url);

			if ( isset($url)) {

				$controller      = ucfirst( strtolower( $url[1] ) );
				$controllerClass = "OC_Blog\\Controllers\\Controller" . $controller;
				$method = $this->getMethod($url, $controller);

				if (class_exists( $controllerClass)  && !empty($constant->getPost())){
					$params = $constant->getPost();
					(new $controllerClass( $twig, $slug, $params, $constant))->$method();
				}
				elseif ( class_exists( $controllerClass)  && isset($method)) {
					(new $controllerClass($twig, $slug, [], $constant))->$method();
				}

				else {
					(new ControllerHome($twig, $slug, [],$constant))->home();
				}

			}


		} catch ( Exception $e ) {
			$error = new ControllerFactory($twig, $slug = 0, [], $constant);
			$errorMsg = $e->getMessage();
			$error->render( '404.twig', ['error'=> $errorMsg] );
		}
	}

	/**
	 * Supprime les doublons des slashes.
	 *
	 * @param $url
	 *
	 * @return string
	 */
	public function cleanUri($url): string {
		$uri = new Uri($url);
		return UriNormalizer::normalize($uri,UriNormalizer::REMOVE_DUPLICATE_SLASHES);
	}

	/**
	 * Récupère la méthode associé de la classe dans l'url.
	 *
	 * @param array $method
	 * @param string $controller
	 *
	 * @return string|null
	 */
	public function getMethod(array $method, string $controller): ?string {
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

	/**
	 * Récupère le slug si il existe dans l'url
	 *
	 * @param array $url
	 *
	 * @return int
	 */
	public function slug(array $url): int {
		if (isset($url[3])){
			return $url[3];
		}else{
			return 0;
		}
	}
}

