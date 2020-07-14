<?php

class Router {

	private $_ctrl;

	public function routeRequest($twig) {
		try {

			$url = '';

			if ( isset( $_GET['url'] ) ) {
				$url = explode( '/', filter_var( $_GET['url'], FILTER_SANITIZE_URL ) );

				$controller      = ucfirst( strtolower( $url[0] ) );

				$controllerClass = "Controller" . $controller;
				$controllerFile  = "controllers/" . $controllerClass . ".php";

				if ( file_exists( $controllerFile ) ) {
					require_once( $controllerFile );
					$this->_ctrl = new $controllerClass($url,$twig);
				}
				else {
					throw new Exception( 'Page introuvable' );
				}

			}
			else {
				require_once( 'ControllerHome.php' );
				$this->_ctrl = new ControllerHome( $url, $twig);
			}


		} catch ( Exception $e ) {
			$errorMsg = $e->getMessage();
			var_dump($errorMsg);
			echo $twig->render( '404.twig' );
		}
	}
}