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
		$valid = true;

		if ( ! empty( $this->getPostForm() ) ) {
			$valid = $this->checkAuth();
		}

		if ( $valid ) {
			$this->render( 'login.twig', [ 'server' => $this->getServer() ] );
		}
	}

	/**
	 * Permet de se déconnecter.
	 *
	 * Redirige sur la page d'accueil.
	 */
	public function logout(): void {
		session_destroy();
		$this->redirect( $this->getServer() );
	}

	/**
	 * Affiche la page d'enregistrement.
	 */
	public function register(): void {

		$valid = true;

		if ( ! empty( $this->getPostForm() ) ) {
			$valid = $this->check();
		}

		if ( $valid ) {
			$this->render( 'register.twig', [ 'server' => $this->getServer() ] );
		}
	}

	/**
	 * Permet d'ajouter un utilisateur et renvoi sur la page de connexion.
	 *
	 * @param array $params
	 */
	public function addUser( array $params ): void {

		( new AuthManager() )->registerUser( $params );
		$this->render( 'login.twig', [
			'valid'  => true,
			'server' => $this->getServer()
		] );

	}

	/**
	 * Permet de vérifier les identifiants envoyé par l'utilisateur.
	 *
	 * @return bool|null
	 */
	public function checkAuth(): ?bool {
		$params = $this->getPostForm();
		$user   = ( new AuthManager() )->checkLogin( $params );

		if ( $user === null ) {
			$this->render( 'login.twig', [
				'notValid' => true,
				'server'   => $this->getServer()
			] );

			return false;
		} else {
			( new Session )->setKey( 'user', $user );
			$this->redirect( $this->getServer() );

			return null;
		}
	}

	/**
	 * Permet de vérifier les information envoyé par $_POST.
	 *
	 * @return bool|null
	 */
	public function check(): ?bool {
		$params      = $this->getPostForm();
		$userManager = new AuthManager;

		foreach ( $params as $value ) {

			if ( $value != preg_replace( '/\s+/', '', $value ) ) {
				$this->render( 'register.twig', [
					'space'  => true,
					'server' => $this->getServer(),
					'key'    => $params
				] );

				return false;
			}

			if ( strlen( $value ) < 3 ) {
				$this->render( 'register.twig', [
					'noValid' => true,
					'server'  => $this->getServer(),
					'key'     => $params
				] );

				return false;
			}
		}

		if ( $params['password'] != $params['confirme'] ) {
			$this->render( 'register.twig', [
				'valid'  => true,
				'server' => $this->getServer(),
				'key'    => $params
			] );

			return false;
		}

		if ( $userManager->checkUser( $params['pseudo'] ) ) {
			$this->render( 'register.twig', [
				'noPseudo' => true,
				'server'   => $this->getServer(),
				'key'      => $params
			] );

			return false;
		}

		if ( $userManager->checkEmail( $params['identifiant'] ) ) {
			$this->render( 'register.twig', [
				'noEmail' => true,
				'server'  => $this->getServer(),
				'key'     => $params
			] );

			return false;
		}

		$this->addUser( $params );
	}
}

