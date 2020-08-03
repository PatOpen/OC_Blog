<?php

namespace OC_Blog\Controllers;

use Exception;


class Router {

	private $_ctrl;

	public function routeRequest( $twig) {
		try {

			$url = explode( '/', filter_var( $_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL ) );

			if ( isset($url)) {

				$controller      = ucfirst( strtolower( $url[1] ) );
				$controllerClass = "OC_Blog\\Controllers\\Controller" . $controller;

				if ( class_exists( $controllerClass)  && empty($url[2])) {
					$this->_ctrl = new $controllerClass($twig);
				}elseif (isset($_POST)){
					$params['post'] = $_POST;
					$this->_ctrl = new $controllerClass($url[2], $twig, $params);
				}
				elseif ( class_exists( $controllerClass)  && isset($url[2])) {
					$this->_ctrl = new $controllerClass($url[2],$twig);
				}

				else {
					$this->_ctrl = new ControllerHome($twig);
				}

			}


		} catch ( Exception $e ) {
			$errorMsg = $e->getMessage();
			var_dump($errorMsg);
			echo $twig->render( '404.twig' );
		}
	}
}