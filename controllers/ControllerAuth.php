<?php


namespace OC_Blog\Controllers;

use OC_Blog\Models\AuthManager;
use OC_Blog\Tools\ControllerFactory;
use OC_Blog\Tools\Session;


class ControllerAuth extends ControllerFactory {

	/**
	 * Afiche la page de connexion.
	 */
	public function login(): void {

		if (!empty($this->getPost())){
			$this->checkAuth();
		}

		echo $this->getTwig()->render('login.twig', ['server' => $this->getServer()]);

	}

	/**
	 * Permet de se déconnecter.
	 *
	 * Redirige sur la page d'accueil.
	 */
	public function logout(): void {
		session_destroy();
		header("Location: http://".$this->getServer());
	}

	/**
	 * Affiche la page d'enregistrement.
	 */
	public function register(): void {

		echo $this->getTwig()->render('register.twig', ['server' => $this->getServer()]);
	}

	/**
	 * Permet d'ajouter un utilisateur et renvoi sur la page de connexion.
	 *
	 * @param array $params
	 */
	public function addUser(array $params): void {

		(new AuthManager())->registerUser($params);
		echo $this->getTwig()->render('login.twig', ['valid' => TRUE,
		                                         'server' => $this->getServer()]);

	}

	/**
	 * Permet de vérifier les identifiants envoyé par l'utilisateur.
	 */
	public function checkAuth(): void {
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

	/**
	 * Permet de vérifier les information envoyé par $_POST.
	 */
	public function check(): void {
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

