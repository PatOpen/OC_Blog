<?php


namespace OC_Blog\Controllers;

use OC_Blog\Models\AuthManager;
use OC_Blog\Tools\Session;

class ControllerAuth extends AuthManager{

	private $params;
	private $method;
	private $twig;

	public function __construct($method, $twig, $params){
		$this->method = $method;
		$this->twig = $twig;
		$this->params = $params;
		$target = $method[2];
		if (method_exists(ControllerAuth::class, $target) ) {
			$this->$target();
		}else{
			echo $this->twig->render('404.twig');
		}

	}

	public function login() {

		echo $this->twig->render('login.twig');

	}

	public function logout(){
		session_destroy();
		header("Location: http://localhost:8000/Home");
	}

	public function register(){

		echo $this->twig->render('register.twig');
	}

	public function addUser($params){

		$this->registerUser($params);
		$valid = true;
		echo $this->twig->render('login.twig', ['valid' => $valid]);

	}

	public function checkAuth(){
		$params = $this->params['post'];
		$user = $this->checkLogin($params);
		$key = 'user';

		if ($user === false){
			$notValid = true;
			echo $this->twig->render('login.twig', ['notValid' => $notValid]);
		}else{
			( new Session )->setKey($key, $user);
			header("Location: http://localhost:8000/Home");
		}


	}

	public function check(){
		$params = $this->params['post'];

		foreach ($params as $key => $value){

			if ($value != preg_replace('/\s+/', '', $value)){
				$space = true;
				echo $this->twig->render('register.twig', ['space' => $space, 'key' => $params]);
				exit();
			}

			if (strlen($value) < 3){
				$noValid = true;
				echo $this->twig->render('register.twig', ['noValid' => $noValid, 'key' => $params]);
				exit();
			}
		}

		if($params['password'] != $params['confirme']){
			$valid = true;
			echo $this->twig->render('register.twig', ['valid' => $valid, 'key' => $params]);
			exit();
		}

		if ($this->checkUser($params['pseudo'])){
			$noPseudo = true;
			echo $this->twig->render('register.twig', ['noPseudo' => $noPseudo, 'key' => $params]);
			exit();
		}

		if ($this->checkEmail($params['identifiant'])){
			$noEmail = true;
			echo $this->twig->render('register.twig', ['noEmail' => $noEmail, 'key' => $params]);
			exit();
		}

		$this->addUser($params);
	}


}