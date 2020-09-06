<?php


namespace OC_Blog\Controllers;


use OC_Blog\Models\AdminManager;
use OC_Blog\Models\AuthManager;
use OC_Blog\Models\CommentsManager;
use OC_Blog\Models\PostsManager;
use OC_Blog\Tools\ControllerFactory;
use OC_Blog\Tools\Session;


class ControllerAdmin extends ControllerFactory {

	/**
	 * Affiche la page profil de l'utilisateur.
	 */
	public function profile() {

		$key    = ( new Session )->getKey( 'user' );
		$noPost = true;

		if ( isset( $this->getPostForm()['identifiant'] ) ) {
			$this->checkMailProfile();
			$noPost = false;
		} elseif ( isset( $this->getPostForm()['password'] ) ) {
			$this->checkPassProfile();
			$noPost = false;
		} elseif ( isset( $this->getUpFile()['avatar'] ) ) {
			$this->avatar();
			$noPost = false;
		}
		if ( $noPost ) {
			$this->render( 'profile.twig', [
				'logged' => true,
				'user'   => $key['pseudo'],
				'value'  => $key['email'],
				'admin'  => $key['admin'],
				'server' => $this->getServer(),
				'avatar' => $key['avatar']
			] );
		}
	}

	/**
	 * Affiche les commentaire à valider sur la page admin.
	 */

	public function admin() {

		$key          = ( new Session )->getKey( 'user' );
		$adminManager = new AdminManager();
		$admin        = $adminManager->checkAdmin( (int) $key['id'] );


		if ( $admin ) {
			$allComments = $adminManager->commentsPost();
			$allPosts    = ( new PostsManager )->listPosts();
			$this->render( 'admin.twig', [
				'logged'      => true,
				'user'        => $key['pseudo'],
				'allPosts'    => $allPosts,
				'allComments' => $allComments,
				'server'      => $this->getServer(),
			] );
		} else {
			$errorMsg = 'Une erreur est survenue';
			$this->render( '404.twig', [ 'error' => $errorMsg ] );
		}
	}

	/**
	 * Permet de valider un commentaire.
	 */

	public function validate() {

		$commentId    = $this->getSlug();
		$validComment = ( new CommentsManager )->validComments( (int) $commentId );

		if ( $validComment ) {
			$path = $this->getServer() . "/Admin/admin";
			$this->redirect( $path );
		} else {
			$errorMsg = 'Une erreur est survenue';
			$this->render( '404.twig', [ 'error' => $errorMsg ] );
		}
	}

	/**
	 * Vérifie si un email existe et met à jour le mail del'utilisateur dans la BDD.
	 *
	 * Page Profil
	 */

	public function checkMailProfile() {

		$params = $this->getPostForm();
		$key    = ( new Session )->getKey( 'user' );

		$this->checkForm( $params );

		$userManager = ( new AuthManager );
		$emailExist  = $userManager->checkEmail( $params['identifiant'] );


		if ( $params['identifiant'] != $key['email'] && $emailExist === true ) {
			$this->render( 'profile.twig', [
				'noEmail' => true,
				'server'  => $this->getServer(),
				'logged'  => true,
				'user'    => $key['pseudo'],
				'value'   => $key['email'],
				'admin'   => $key['admin'],
				'avatar'  => $key['avatar']
			] );
		} elseif ( $params['identifiant'] === $key['email'] ) {

			$this->render( 'profile.twig', [
				'logged' => true,
				'server' => $this->getServer(),
				'user'   => $key['pseudo'],
				'value'  => $key['email'],
				'admin'  => $key['admin'],
				'avatar' => $key['avatar']
			] );
		}

		if ( $params['identifiant'] != $key['email'] && $emailExist === false ) {
			$validChange = $userManager->updateUserMail( $params['identifiant'], $key );

			if ( $validChange ) {
				( new Session )->setValueKey( 'user', 'email', $params['identifiant'] );
				$key['email'] = $params['identifiant'];

				$this->render( 'profile.twig', [
					'confirm' => true,
					'server'  => $this->getServer(),
					'logged'  => true,
					'user'    => $key['pseudo'],
					'value'   => $key['email'],
					'admin'   => $key['admin'],
					'avatar'  => $key['avatar']
				] );

			}
		}
	}

	/**
	 * Vérifie que les mots de passe correspondent et le met à jour dans la BDD.
	 *
	 * Page Profil
	 */

	public function checkPassProfile() {
		$params = $this->getPostForm();
		$key    = ( new Session )->getKey( 'user' );

		$pass        = trim( $params['password'] );
		$passConfirm = trim( $params['confirme'] );

		if ( ( $pass != $passConfirm ) || strlen( $pass ) < 4 ) {

			$this->render( 'profile.twig', [
				'valid'  => true,
				'server' => $this->getServer(),
				'logged' => true,
				'user'   => $key['pseudo'],
				'value'  => $key['email'],
				'admin'  => $key['admin'],
				'avatar' => $key['avatar']
			] );
		} elseif ( $pass === $passConfirm ) {

			( new AuthManager )->updateUserPass( $pass, $key );

			$this->render( 'profile.twig', [
				'confirm' => true,
				'server'  => $this->getServer(),
				'logged'  => true,
				'user'    => $key['pseudo'],
				'value'   => $key['email'],
				'admin'   => $key['admin'],
				'avatar'  => $key['avatar']
			] );

		}
	}


	/**
	 * Vérifie les entrées d'utilisateur des formulaires
	 *
	 * @param array $params
	 */
	public function checkForm( array $params ) {

		foreach ( $params as $key => $value ) {

			if ( $value != preg_replace( '/\s+/', '', $value ) ) {
				$this->render( 'profile.twig', [
					'space'  => true,
					'server' => $this->getServer(),
					'logged' => true,
					'user'   => $key['pseudo'],
					'admin'  => $key['admin'],
					'value'  => $key['email']
				] );
			}

			if ( strlen( $value ) < 4 ) {
				$this->render( 'profile.twig', [
					'noValid' => true,
					'server'  => $this->getServer(),
					'logged'  => true,
					'user'    => $key['pseudo'],
					'admin'   => $key['admin'],
					'value'   => $key['email']
				] );
			}
		}
	}

	/**
	 * Vérifie les détails du fichier et enregistre le nom de l'avatar dans la BDD.
	 *
	 * L'avatar est ensuite enregistré dans le dossier public/images/avatar
	 */

	public function avatar() {

		$normalizeFile = $this->getUpFile();

		$maxSize   = 500000;
		$fileSize  = $normalizeFile['avatar']->getSize();
		$fileName  = $normalizeFile['avatar']->getClientFilename();
		$extension = [ 'jpg', 'jpeg', 'gif', 'png' ];

		$key = ( new Session )->getKey( 'user' );

		if ( $normalizeFile['avatar']->getError() > 0 ) {
			$this->render( 'profile.twig', [
				'error'  => true,
				'server' => $this->getServer(),
				'logged' => true,
				'user'   => $key['pseudo'],
				'value'  => $key['email'],
				'admin'  => $key['admin'],
				'avatar' => $key['avatar']
			] );
		}

		if ( $fileSize > $maxSize ) {
			$this->render( 'profile.twig', [
				'size'   => true,
				'server' => $this->getServer(),
				'logged' => true,
				'user'   => $key['pseudo'],
				'value'  => $key['email'],
				'admin'  => $key['admin'],
				'avatar' => $key['avatar']
			] );
		}

		$fileExtension = strtolower( substr( $fileName, - 3 ) );

		if ( ! in_array( $fileExtension, $extension ) ) {
			$this->render( 'profile.twig', [
				'ext'    => true,
				'server' => $this->getServer(),
				'logged' => true,
				'user'   => $key['pseudo'],
				'value'  => $key['email'],
				'admin'  => $key['admin'],
				'avatar' => $key['avatar']
			] );
		}

		$uniqName    = md5( uniqid( rand(), true ) );
		$newNameFile = $uniqName . "." . $fileExtension;
		$pathFile    = "../public/images/avatar/" . $newNameFile;
		$normalizeFile['avatar']->moveTo( $pathFile );
		$moveFile = $normalizeFile['avatar']->isMoved();
		$oldFile  = "../public/images/avatar/" . $key['avatar'];

		if ( $moveFile ) {
			$userManager = new AuthManager();
			$upload      = $userManager->updateAvatar( $newNameFile, $key );

			if (!empty($key['avatar'])){
				$this->deleteImage($oldFile);
			}

			if ( $upload ) {
				( new Session )->setValueKey( 'user', 'avatar', $newNameFile );
				$key['avatar'] = $newNameFile;
				$this->render( 'profile.twig', [
					'confirm' => true,
					'server'  => $this->getServer(),
					'logged'  => true,
					'user'    => $key['pseudo'],
					'value'   => $key['email'],
					'admin'   => $key['admin'],
					'avatar'  => $key['avatar']
				] );
			}
		}
	}
}

