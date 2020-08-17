<?php


namespace OC_Blog\Controllers;

use OC_Blog\Models\AuthManager;
use OC_Blog\Tools\Session;


class ControllerAuth extends AuthManager{

	private array $_params;
	private array $_method;
	private object $_twig;


	public function __construct( $method, $twig, $params){
		$this->_method = $method;
		$this->_twig = $twig;
		$this->_params = $params;
		$target = $method[2];

		if (method_exists(ControllerAuth::class, $target) ) {
			$this->$target();
		}else{
			echo $this->_twig->render('404.twig', ['server' => $_SERVER['SERVER_NAME']]);
		}

	}

	public function login() {

		if (!empty($this->_params)){
			$this->checkAuth();
		}

		echo $this->_twig->render('login.twig', ['server' => $_SERVER['SERVER_NAME']]);

	}

	public function logout(){
		session_destroy();
		header("Location: http://".$_SERVER['SERVER_NAME']."/Home");
	}

	public function register(){

		echo $this->_twig->render('register.twig', ['server' => $_SERVER['SERVER_NAME']]);
	}

	public function addUser($params){

		$this->registerUser($params);
		echo $this->_twig->render('login.twig', ['valid' => TRUE,
		                                         'server' => $_SERVER['SERVER_NAME']]);

	}

	public function checkAuth(){
		$params = $this->_params;
		$user = $this->checkLogin($params);
		$key = 'user';

		if ($user === false){
			echo $this->_twig->render('login.twig', ['notValid' => TRUE,
			                                         'server' => $_SERVER['SERVER_NAME']]);
			exit();
		}else{
			( new Session )->setKey($key, $user);
			header("Location: http://".$_SERVER['SERVER_NAME']."/Home");
		}


	}

	public function check(){
		$params = $this->_params;

		foreach ($params as $key => $value){

			if ($value != preg_replace('/\s+/', '', $value)){
				echo $this->_twig->render('register.twig', ['space' => TRUE,
				                                            'server' => $_SERVER['SERVER_NAME'],
				                                            'key' => $params]);
				exit();
			}

			if (strlen($value) < 3){
				echo $this->_twig->render('register.twig', ['noValid' => TRUE,
				                                            'server' => $_SERVER['SERVER_NAME'],
				                                            'key' => $params]);
				exit();
			}
		}

		if($params['password'] != $params['confirme']){
			echo $this->_twig->render('register.twig', ['valid' => TRUE,
			                                            'server' => $_SERVER['SERVER_NAME'],
			                                            'key' => $params]);
			exit();
		}

		if ($this->checkUser($params['pseudo'])){
			echo $this->_twig->render('register.twig', ['noPseudo' => TRUE,
			                                            'server' => $_SERVER['SERVER_NAME'],
			                                            'key' => $params]);
			exit();
		}

		if ($this->checkEmail($params['identifiant'])){
			echo $this->_twig->render('register.twig', ['noEmail' => TRUE,
			                                            'server' => $_SERVER['SERVER_NAME'],
			                                            'key' => $params]);
			exit();
		}

		$this->addUser($params);
	}


}