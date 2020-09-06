<?php

namespace OC_Blog\Controllers;

use OC_Blog\Models\CommentsManager;
use OC_Blog\Tools\ControllerFactory;
use OC_Blog\Tools\Session;


class ControllerComment extends ControllerFactory {

	/**
	 *Ajoute un commentaire à post en BDD.
	 *
	 * Si l'utilisateur n'est pas connecté, renvoi sur la page de connexion.
	 */
	public function addCommentPost(): void {
		$key = ( new Session )->getKey( 'user' );


		if ( ! isset( $key ) ) {
			$path = $this->getServer() . "/Auth/login";
			$this->redirect( $path );
		} else {

			$userId  = $key['id'];
			$comment = $this->getPostForm()['message'];
			$postId  = ( new Session )->getKey( 'post' );

			$good = ( new CommentsManager() )->addComment( $userId, $comment, $postId['id'] );

			if ( $good ) {
				$path = $this->getServer() . "/Post/viewPost/" . $postId['id'];
				$this->redirect( $path );
			} else {
				$errorMsg = 'Une erreur c\'est produite veuillez recommencer !';
				$this->render( '404.twig', [ 'error' => $errorMsg ] );
			}
		}
	}

	/**
	 * Modification d'un commentaire en BDD.
	 *
	 * Renvoi sur la page du post concerné.
	 */
	public function updateComment(): void {

		$keyPost = ( new Session )->getKey( 'post' );
		$keyUser = ( new Session )->getKey( 'user' );
		$post    = $this->getPostForm();


		if ( isset( $post['message']) && !empty($keyPost['id']) ) {
			$this->checkComment( $post['message'], $this->getSlug(), $keyPost['id'] );
		}

		if ( isset( $post['message']) && empty($keyPost['id']) ) {
			$this->checkCommentAdmin( $post['message'], $this->getSlug());
		}

		$content = ( new CommentsManager() )->oneComment( (int) $this->getSlug() );

		$this->render( 'comment.twig', [
			'logged'  => true,
			'server'  => $this->getServer(),
			'id'      => $content['id'],
			'admin'   => $keyUser['admin'],
			'user'    => $keyUser['pseudo'],
			'content' => $content['content']
		] );

	}

	/**
	 *Vérification des informations $_POST et enregistrement du commentaire.
	 *
	 * @param string $comment
	 * @param int $commentId
	 * @param int $postId
	 */
	public function checkComment( string $comment, int $commentId, int $postId ): void {

		$content = trim( $comment );
		$update  = ( new CommentsManager() )->updateComment( $content, $commentId );

		if ( $update ) {
			$path = $this->getServer() . '/Post/viewPost/' . $postId;
			$this->redirect( $path );
		} else {
			$this->render( 'comment.twig', [
				'logged'  => true,
				'error'   => true,
				'server'  => $this->getServer(),
				'id'      => $commentId,
				'content' => $content
			] );
		}
	}

	/**
	 * Vérification des informations $_POST et enregistrement du commentaire.
	 *
	 * @param string $comment
	 * @param int $commentId
	 */
	public function checkCommentAdmin( string $comment, int $commentId): void {

		$content = trim( $comment );
		$update  = ( new CommentsManager() )->updateComment( $content, $commentId );

		if ( $update ) {
			$path = $this->getServer() . '/Admin/admin';
			$this->redirect( $path );
		} else {
			$this->render( 'comment.twig', [
				'logged'  => true,
				'error'   => true,
				'server'  => $this->getServer(),
				'id'      => $commentId,
				'content' => $content
			] );
		}
	}

	/**
	 * Supprime un commentaire via l'auteur du commentaire.
	 *
	 * Renvoi sur la page du post concerné.
	 */
	public function deleteComment(): void {

		$key = ( new Session )->getKey( 'post' );
		( new CommentsManager() )->deleteComment( $this->getSlug() );
		$path = $this->getServer() . '/Post/viewPost/' . $key['id'];
		$this->redirect( $path );
	}
}

