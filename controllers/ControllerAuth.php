<?php


namespace OC_Blog\Controllers;

use OC_Blog\Models\AuthManager;
use OC_Blog\Tools\ControllerFactory;
use OC_Blog\Tools\Session;


class ControllerAuth extends ControllerFactory {

	public function login() {

		if (!empty($this->getPost())){
			$this->checkAuth();
		}

		echo $this->getTwig()->render('login.twig', ['server' => $this->getServer()]);

	}

	public function logout(){
		session_destroy();
		header("Location: http://".$this->getServer());
	}

	public function register(){

		echo $this->getTwig()->render('register.twig', ['server' => $this->getServer()]);
	}

	public function addUser($params){

		(new AuthManager())->registerUser($params);
		echo $this->getTwig()->render('login.twig', ['valid' => TRUE,
		                                         'server' => $this->getServer()]);

	}

	public function checkAuth(){
		$params = $this->getPost();
		$user = (new AuthManager())->checkLogin($params);

		if ($user === false){
			echo $this->getTwig()->render('login.twig', ['notValid' => TRUE,
			                                         'server' => $this->getServer()]);
			exit();
		}else{
			( new Session )->setKey('user', $user);
			header("Location: http://".$this->getServer());
		}


	}

	public function check(){
		$params = $this->getPost();
		$userManager = new AuthManager;

		foreach ($params as $key => $value){

			if ($value != preg_replace('/\s+/', '', $value)){
				echo $this->getTwig()->render('register.twig', ['space' => TRUE,
				                                            'server' => $this->getServer(),
				                                            'key' => $params]);
				exit();
			}

			if (strlen($value) < 3){
				echo $this->getTwig()->render('register.twig', ['noValid' => TRUE,
				                                            'server' => $this->getServer(),
				                                            'key' => $params]);
				exit();
			}
		}

		if($params['password'] != $params['confirme']){
			echo $this->getTwig()->render('register.twig', ['valid' => TRUE,
			                                            'server' => $this->getServer(),
			                                            'key' => $params]);
			exit();
		}

		if ($userManager->checkUser($params['pseudo'])){
			echo $this->getTwig()->render('register.twig', ['noPseudo' => TRUE,
			                                            'server' => $this->getServer(),
			                                            'key' => $params]);
			exit();
		}

		if ($userManager->checkEmail($params['identifiant'])){
			echo $this->getTwig()->render('register.twig', ['noEmail' => TRUE,
			                                            'server' => $this->getServer(),
			                                            'key' => $params]);
			exit();
		}

		$this->addUser($params);
	}


}
