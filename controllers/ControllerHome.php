<?php

namespace OC_Blog\Controllers;

use OC_Blog\Models\PostsManager;
use OC_Blog\Tools\ControllerFactory;
use OC_Blog\Tools\Session;


class ControllerHome extends ControllerFactory {

	/**
	 * Affiche la page d'accueil.
	 */
	public function home(): void {
		$posts    = new PostsManager();
		$allPosts = $posts->listPosts();
		$key      = ( new Session )->getKey( 'user' );


		if ( ! empty( $key ) ) {
			$this->render( 'home.twig', [
				'allPosts' => $allPosts,
				'logged'   => true,
				'user'     => $key['pseudo'],
				'admin'    => $key['admin'],
				'server'   => $this->getServer()
			] );
		} else {
			$this->render( 'home.twig', [
				'allPosts' => $allPosts,
				'logged'   => false,
				'server'   => $this->getServer()
			] );
		}

	}
}
