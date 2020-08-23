<?php


namespace OC_Blog\Controllers;

use GuzzleHttp\Psr7\ServerRequest;
use OC_Blog\Tools\ConstantGlobal;
use OC_Blog\Models\AuthManager;
use OC_Blog\Tools\Session;


class ControllerAuth extends AuthManager{

	private array $_params;
	private int $_slug;
	private object $_twig;
	private string $_server;


	public function __construct($twig, $slug, $params){
		$this->_slug = $slug;
		$this->_twig = $twig;
		$this->_params = $params;
		$this->_server = ( new ConstantGlobal(ServerRequest::fromGlobals()) )->getServerName()['SERVER_NAME'];

	}

	public function login() {

		if (!empty($this->_params)){
			$this->checkAuth();
		}

		echo $this->_twig->render('login.twig', ['server' => $this->_server]);

	}

	public function logout(){
		session_destroy();
		header("Location: http://".$this->_server);
	}

	public function register(){

		echo $this->_twig->render('register.twig', ['server' => $this->_server]);
	}

	public function addUser($params){

		$this->registerUser($params);
		echo $this->_twig->render('login.twig', ['valid' => TRUE,
		                                         'server' => $this->_server]);

	}

	public function checkAuth(){
		$params = $this->_params;
		$user = $this->checkLogin($params);

		if ($user === false){
			echo $this->_twig->render('login.twig', ['notValid' => TRUE,
			                                         'server' => $this->_server]);
			exit();
		}else{
			( new Session )->setKey('user', $user);
			header("Location: http://".$this->_server);
		}


	}

	public function check(){
		$params = $this->_params;

		foreach ($params as $key => $value){

			if ($value != preg_replace('/\s+/', '', $value)){
				echo $this->_twig->render('register.twig', ['space' => TRUE,
				                                            'server' => $this->_server,
				                                            'key' => $params]);
				exit();
			}

			if (strlen($value) < 3){
				echo $this->_twig->render('register.twig', ['noValid' => TRUE,
				                                            'server' => $this->_server,
				                                            'key' => $params]);
				exit();
			}
		}

		if($params['password'] != $params['confirme']){
			echo $this->_twig->render('register.twig', ['valid' => TRUE,
			                                            'server' => $this->_server,
			                                            'key' => $params]);
			exit();
		}

		if ($this->checkUser($params['pseudo'])){
			echo $this->_twig->render('register.twig', ['noPseudo' => TRUE,
			                                            'server' => $this->_server,
			                                            'key' => $params]);
			exit();
		}

		if ($this->checkEmail($params['identifiant'])){
			echo $this->_twig->render('register.twig', ['noEmail' => TRUE,
			                                            'server' => $this->_server,
			                                            'key' => $params]);
			exit();
		}

		$this->addUser($params);
	}


}
