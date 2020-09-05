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
		$contact  = $this->getPostForm();
		$confirm  = false;
		$key      = ( new Session )->getKey( 'user' );

		if ( ! empty( $contact ) ) {
			$confirm = $this->contact();
		}


		if ( ! empty( $key ) ) {
			$this->render( 'home.twig', [
				'allPosts' => $allPosts,
				'logged'   => true,
				'confirm'  => $confirm,
				'user'     => $key['pseudo'],
				'admin'    => $key['admin'],
				'server'   => $this->getServer()
			] );
		} else {
			$this->render( 'home.twig', [
				'allPosts' => $allPosts,
				'logged'   => false,
				'confirm'  => $confirm,
				'server'   => $this->getServer()
			] );
		}

	}

	/**
	 * Envoi un mail si le formulaire contact est utilisé.
	 *
	 * @return bool
	 */
	public function contact(): bool {
		$post = $this->getPostForm();

		foreach ( $post as $value ) {
			trim( $value );
			htmlentities( $value );
		}

		$nom            = ucfirst( strtolower( $post['nom'] ) );
		$prenom         = ucfirst( strtolower( $post['nom'] ) );
		$mail           = $post['email'];
		$contactMessage = $post['message'];

		$to      = 'patopenclassrom@gmail.com';
		$subject = "Message de contact du Blog";
		$message = 'Bonjour !' . "\r\n" .
		           "Vous avez reçu un message de votre blog de $nom $prenom !" . "\r\n" .
		           "Son Email est $mail" . "\r\n" .
		           "Son message est : " . "\r\n" . $contactMessage;
		$headers = "From: $mail <$mail>\r\n" .
		           "MIME-Version: 1.0" . "\r\n" .
		           "Content-type: text/html; charset=UTF-8" . "\r\n";

		return mail( $to, $subject, $message, $headers );
	}
}
