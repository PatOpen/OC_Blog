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

		$this->render('login.twig', ['server' => $this->getServer()]);

	}

	/**
	 * Permet de se déconnecter.
	 *
	 * Redirige sur la page d'accueil.
	 */
	public function logout(): void {
		session_destroy();
		$this->redirect($this->getServer());
	}

	/**
	 * Affiche la page d'enregistrement.
	 */
	public function register(): void {

		$this->render('register.twig', ['server' => $this->getServer()]);
	}

	/**
	 * Permet d'ajouter un utilisateur et renvoi sur la page de connexion.
	 *
	 * @param array $params
	 */
	public function addUser(array $params): void {

		(new AuthManager())->registerUser($params);
		$this->render('login.twig', ['valid' => TRUE,
		                                         'server' => $this->getServer()]);

	}

	/**
	 * Permet de vérifier les identifiants envoyé par l'utilisateur.
	 */
	public function checkAuth(): void {
		$params = $this->getPost();
		$user = (new AuthManager())->checkLogin($params);

		if ($user === null){
			$this->render('login.twig', ['notValid' => TRUE,
			                                         'server' => $this->getServer()]);
			exit(0);
		}else{
			( new Session )->setKey('user', $user);
			$this->redirect($this->getServer());
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
				$this->render('register.twig', ['space' => TRUE,
				                                            'server' => $this->getServer(),
				                                            'key' => $params]);
				exit(0);
			}

			if (strlen($value) < 3){
				$this->render('register.twig', ['noValid' => TRUE,
				                                            'server' => $this->getServer(),
				                                            'key' => $params]);
				exit(0);
			}
		}

		if($params['password'] != $params['confirme']){
			$this->render('register.twig', ['valid' => TRUE,
			                                            'server' => $this->getServer(),
			                                            'key' => $params]);
			exit(0);
		}

		if ($userManager->checkUser($params['pseudo'])){
			$this->render('register.twig', ['noPseudo' => TRUE,
			                                            'server' => $this->getServer(),
			                                            'key' => $params]);
			exit(0);
		}

		if ($userManager->checkEmail($params['identifiant'])){
			$this->render('register.twig', ['noEmail' => TRUE,
			                                            'server' => $this->getServer(),
			                                            'key' => $params]);
			exit(0);
		}

		$this->addUser($params);
	}
}

