<?php


namespace OC_Blog\Controllers;

use OC_Blog\Models\AuthManager;
use OC_Blog\Tools\Session;


class ControllerAuth extends AuthManager{

	private array $_params;
	private array $_method;
	private object $_twig;

	public function __construct($method, $twig, $params){
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
		$valid = true;
		echo $this->_twig->render('login.twig', ['valid' => $valid,
		                                         'server' => $_SERVER['SERVER_NAME']]);

	}

	public function checkAuth(){
		$params = $this->_params['post'];
		$user = $this->checkLogin($params);
		$key = 'user';

		if ($user === false){
			$notValid = true;
			echo $this->_twig->render('login.twig', ['notValid' => $notValid,
			                                         'server' => $_SERVER['SERVER_NAME']]);
		}else{
			( new Session )->setKey($key, $user);
			header("Location: http://".$_SERVER['SERVER_NAME']."/Home");
		}


	}

	public function check(){
		$params = $this->_params['post'];

		foreach ($params as $key => $value){

			if ($value != preg_replace('/\s+/', '', $value)){
				$space = true;
				echo $this->_twig->render('register.twig', ['space' => $space,
				                                            'server' => $_SERVER['SERVER_NAME'],
				                                            'key' => $params]);
				exit();
			}

			if (strlen($value) < 3){
				$noValid = true;
				echo $this->_twig->render('register.twig', ['noValid' => $noValid,
				                                            'server' => $_SERVER['SERVER_NAME'],
				                                            'key' => $params]);
				exit();
			}
		}

		if($params['password'] != $params['confirme']){
			$valid = true;
			echo $this->_twig->render('register.twig', ['valid' => $valid,
			                                            'server' => $_SERVER['SERVER_NAME'],
			                                            'key' => $params]);
			exit();
		}

		if ($this->checkUser($params['pseudo'])){
			$noPseudo = true;
			echo $this->_twig->render('register.twig', ['noPseudo' => $noPseudo,
			                                            'server' => $_SERVER['SERVER_NAME'],
			                                            'key' => $params]);
			exit();
		}

		if ($this->checkEmail($params['identifiant'])){
			$noEmail = true;
			echo $this->_twig->render('register.twig', ['noEmail' => $noEmail,
			                                            'server' => $_SERVER['SERVER_NAME'],
			                                            'key' => $params]);
			exit();
		}

		$this->addUser($params);
	}


}