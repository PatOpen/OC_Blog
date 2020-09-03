<?php

namespace OC_Blog\Controllers;

use OC_Blog\Models\AuthManager;
use OC_Blog\Models\CommentsManager;
use OC_Blog\Models\PostsManager;
use OC_Blog\Tools\ControllerFactory;
use OC_Blog\Tools\Session;

class ControllerPost extends ControllerFactory {

	/**
	 * Récupère les commentaires d'un post avant d'affiche la page du post et ses commantaires.
	 */
	public function viewPost(): void {

		$post        = new PostsManager();
		$comments    = new CommentsManager();
		$thePost     = $post->getPost( $this->getSlug() );
		$allComments = $comments->postComment( $this->getSlug() );
		$keyPost     = 'post';

		( new Session )->setKey( $keyPost, [ 'id' => $this->getSlug() ] );
		$key = ( new Session )->getKey( 'user' );
		if ( ! empty( $key ) ) {
			$modifComment = $key['id'];
			$this->render( 'post.twig', [
				'thePost'      => $thePost,
				'allComments'  => $allComments,
				'logged'       => true,
				'user'         => $key['pseudo'],
				'server'       => $this->getServer(),
				'admin'        => $key['admin'],
				'modifComment' => $modifComment
			] );
		} else {
			$this->render( 'post.twig', [
				'thePost'     => $thePost,
				'allComments' => $allComments,
				'server'      => $this->getServer(),
				'logged'      => false
			] );
		}
	}

	/**
	 * Renvoi sur la page de création de d'article.
	 */
	public function viewCreatePost(): void {
		$key = ( new Session )->getKey( 'user' );

		$this->render( 'newPost.twig', [
			'logged' => true,
			'user'   => $key['pseudo'],
			'server' => $this->getServer(),
			'admin'  => $key['admin'],
			'create' => true
		] );
	}

	/**
	 * Récupère un post dans la BDD pour le modifier.
	 */
	public function viewUpdatePost(): void {
		$key     = new Session();
		$keyUser = $key->getKey( 'user' );
		$postId  = $this->getSlug();
		$post    = ( new PostsManager )->getPost( $postId );

		$key->setValueKey( 'post', 'image', $post[0]['image'] );
		$key->setValueKey( 'post', 'id', $postId );

		$this->render( 'newPost.twig', [
			'logged'      => true,
			'user'        => $keyUser['pseudo'],
			'server'      => $this->getServer(),
			'admin'       => $keyUser['admin'],
			'id'          => $post[0]['id'],
			'title'       => $post[0]['title'],
			'chapo'       => $post[0]['chapo'],
			'description' => $post[0]['description'],
			'image'       => $post[0]['image'],
			'create'      => false
		] );
	}

	/**
	 * Récupère les données envoyées et enregistre l'article en BDD.
	 */
	public function createPost(): void {
		$session  = new Session();
		$postForm = $this->getPostForm();
		$confim   = true;
		$error    = false;
		$size     = false;
		$ext      = false;

		$user = $session->getKey( 'user' );

		if ( isset( $postForm ) ) {
			foreach ( $postForm as $key => $value ) {
				$key = trim( $value );
			}
			$file = $this->checkImage();

			if ( $file === 'error' ) {
				$confim = false;
				$error  = true;
			}

			if ( $file === 'size' ) {
				$confim = false;
				$size   = true;
			}

			if ( $file === 'ext' ) {
				$confim = false;
				$ext    = true;
			}

			if ( $confim === false ) {
				$this->render( 'newPost.twig', [
					'logged'      => true,
					'confirm'     => $confim,
					'error'       => $error,
					'size'        => $size,
					'ext'         => $ext,
					'title'       => $postForm['titre'],
					'chapo'       => $postForm['chapo'],
					'description' => $postForm['description'],
					'user'        => $user['pseudo'],
					'server'      => $this->getServer(),
					'admin'       => $user['admin'],
					'create'      => true
				] );
			}

			if ( $confim === true ) {
				$post = ( new PostsManager() )->addPost( $postForm, $user['id'], $file );

				if ( $post ) {
					$this->render( 'newPost.twig', [
						'logged'  => true,
						'confirm' => $confim,
						'error'   => $error,
						'size'    => $size,
						'ext'     => $ext,
						'user'    => $user['pseudo'],
						'server'  => $this->getServer(),
						'admin'   => $user['admin'],
						'create'  => true
					] );
				}

				if ( $post === false ) {
					$error = [ 'error' => 'Malheurusement les informations n\'ont pas été pris en compte veuillez nous excusez' ];
					$this->render( '500.twig', $error );
				}
			}
		}
	}

	/**
	 * Récupère le post demandé pour le modifier.
	 */
	public function updatePost(): void {

		$postForm      = $this->getPostForm();
		$postId        = $this->getSlug();
		$normalizeFile = $this->getUpFile();
		$fileName      = $normalizeFile['image']->getClientFilename();

		$session = new Session();
		$keyPost = $session->getKey( 'post' );
		$user    = $session->getKey( 'user' );
		$confim  = true;
		$error   = false;
		$size    = false;
		$ext     = false;

		foreach ( $postForm as $key => $value ) {
			$key = trim( $value );
		}

		if ( empty( $fileName ) ) {
			$postForm['image'] = $keyPost['image'];
			$postUpdate        = ( new PostsManager() )->updatePost( $postId, $postForm );

			if ( $postUpdate ) {
				$this->render( 'newPost.twig', [
					'logged'      => true,
					'confirm'     => $confim,
					'error'       => $error,
					'size'        => $size,
					'ext'         => $ext,
					'id'          => $keyPost['id'],
					'title'       => $postForm['titre'],
					'chapo'       => $postForm['chapo'],
					'description' => $postForm['description'],
					'image'       => $postForm['image'],
					'user'        => $user['pseudo'],
					'server'      => $this->getServer(),
					'admin'       => $user['admin'],
					'create'      => false
				] );
			}

			if ( $postUpdate === false ) {
				$error = [ 'error' => 'Malheurusement les informations n\'ont pas été pris en compte veuillez nous excusez' ];
				$this->render( '500.twig', $error );
			}
		}

		if ( ! empty( $fileName ) ) {
			$file = $this->checkImage();

			if ( $file === 'error' ) {
				$confim = false;
				$error  = true;
			}

			if ( $file === 'size' ) {
				$confim = false;
				$size   = true;
			}

			if ( $file === 'ext' ) {
				$confim = false;
				$ext    = true;
			}

			if ( $confim === false ) {
				$this->render( 'newPost.twig', [
					'logged'      => true,
					'confirm'     => $confim,
					'error'       => $error,
					'size'        => $size,
					'ext'         => $ext,
					'id'          => $keyPost['id'],
					'title'       => $postForm['titre'],
					'chapo'       => $postForm['chapo'],
					'description' => $postForm['description'],
					'image'       => $postForm['image'],
					'user'        => $user['pseudo'],
					'server'      => $this->getServer(),
					'admin'       => $user['admin'],
					'create'      => false
				] );
			}

			if ( $confim === true ) {
				$postForm['image'] = $file;
				$postUpdate        = ( new PostsManager() )->updatePost( $postId, $postForm );

				if ( $postUpdate ) {
					$this->render( 'newPost.twig', [
						'logged'      => true,
						'confirm'     => $confim,
						'error'       => $error,
						'size'        => $size,
						'ext'         => $ext,
						'title'       => $postForm['titre'],
						'id'          => $keyPost['id'],
						'chapo'       => $postForm['chapo'],
						'description' => $postForm['description'],
						'image'       => $postForm['image'],
						'user'        => $user['pseudo'],
						'server'      => $this->getServer(),
						'admin'       => $user['admin'],
						'create'      => false
					] );
				}

				if ( $postUpdate === false ) {
					$error = [ 'error' => 'Malheurusement les informations n\'ont pas été pris en compte veuillez nous excusez' ];
					$this->render( '500.twig', $error );
				}
			}
		}
	}

	/**
	 * Supprime un post de la BDD.
	 */
	public function deletePost() {
		$postId    = $this->getSlug();
		$session   = new Session();
		$user      = $session->getKey( 'user' );
		$postImage = $session->getKey( 'post' );
		if ( isset( $user['id'] ) ) {
			$admin = ( new AuthManager )->checkUserAdmin( $user['id'] );

			if ( $admin ) {
				( new PostsManager )->deletePost( $postId );
				$this->deleteImage( '../public/images/' . $postImage['image'] );
				$this->redirect( $this->getServer() . '/Admin/admin' );
			}
		}
	}

	/**
	 * Récupère l'image et vérifie si elle a:
	 *      - La bonne extension;
	 *      - Une taille inférieur à 5 Mo;
	 *
	 * @return string
	 */
	public function checkImage(): string {
		$normalizeFile = $this->getUpFile();

		$maxSize   = 5000000;
		$fileSize  = $normalizeFile['image']->getSize();
		$fileName  = $normalizeFile['image']->getClientFilename();
		$extension = [ 'jpg', 'jpeg', 'png', 'svg' ];

		if ( $normalizeFile['image']->getError() > 0 ) {
			return 'error';
		}

		if ( $fileSize > $maxSize ) {
			return 'size';
		}

		$fileExtension = strtolower( substr( $fileName, - 3 ) );

		if ( ! in_array( $fileExtension, $extension ) ) {
			return 'ext';
		}

		$uniqName    = md5( uniqid( rand(), true ) );
		$newNameFile = $uniqName . "." . $fileExtension;
		$pathFile    = "../public/images/" . $newNameFile;
		$normalizeFile['image']->moveTo( $pathFile );
		$moveFile = $normalizeFile['image']->isMoved();

		if ( $moveFile ) {
			$key      = new Session();
			$keyImage = $key->getKey( 'post' );
			if ( isset( $keyImage['image'] ) ) {
				$oldFile = "../public/images/" . $keyImage['image'];
				$this->deleteImage( $oldFile );
			}
			$key->setValueKey( 'post', 'image', $newNameFile );

			return $newNameFile;
		}
	}
}

