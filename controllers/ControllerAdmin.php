<?php


namespace OC_Blog\Controllers;


use OC_Blog\Models\AdminManager;
use OC_Blog\Models\AuthManager;
use OC_Blog\Models\CommentsManager;
use OC_Blog\Tools\ControllerFactory;
use OC_Blog\Tools\Session;


class ControllerAdmin extends ControllerFactory {

	/**
	 * Affiche la page profil de l'utilisateur.
	 */
	public function profile(){

		$key = (new Session)->getKey('user');

		if (isset($this->getPost()['identifiant'])){
			$this->checkMailProfile();
		}elseif (isset($this->getPost()['password'])){
			$this->checkPassProfile();
		}elseif (isset($this->getUpFile()['avatar'])){
			$this->avatar();
		}else{
			$this->render('profile.twig', ['logged' => TRUE,
			                                               'user'   => $key['pseudo'],
			                                               'value'  => $key['email'],
			                                               'admin' => $key['admin'],
			                                               'server' => $this->getServer(),
			                                               'avatar'=> $key['avatar']]);
		}
	}

	/**
	 * Affiche les commentaire à valider sur la page admin.
	 */

	public function admin(){

		$key = (new Session)->getKey('user');
		$adminManager = (new AdminManager());
		$admin = $adminManager->checkAdmin((int)$key['id']);


		if ($admin){
			$allComments = $adminManager->commentsPost();
			$this->render('admin.twig', ['logged' => TRUE,
												         'user' => $key['pseudo'],
												         'allComments' => $allComments,
												         'server' => $this->getServer(),]);
		}else{
			$errorMsg = 'Une erreur est survenue';
			$this->render( '404.twig', ['error'=> $errorMsg] );
		}
	}

	/**
	 * Permet de valider un commentaire.
	 */

	public function validate(){

		$commentId = $this->getSlug();
		$validComment = (new CommentsManager)->validComments((int)$commentId);

		if ($validComment){
			$path = $this->getServer()."/Admin/admin";
			$this->redirect($path);
		}else{
			$errorMsg = 'Une erreur est survenue';
			$this->render( '404.twig', ['error'=> $errorMsg] );
		}
	}

	/**
	 * Vérifie si un email existe et met à jour le mail del'utilisateur dans la BDD.
	 *
	 * Page Profil
	 */

	public function checkMailProfile(){

		$params = $this->getPost();
		$key = (new Session)->getKey('user');

        $this->checkForm($params);

		$userManager = (new AuthManager);
		$emailExist = $userManager->checkEmail($params['identifiant']);

		if ($params['identifiant'] != $key['email'] && $emailExist == true)
		{
			$this->render('profile.twig', ['noEmail' => TRUE,
			                                               'server' => $this->getServer(),
			                                               'logged'=> TRUE,
			                                               'user'=> $key['pseudo'],
			                                               'value'=> $key['email'],
			                                               'admin' => $key['admin'],
			                                               'avatar'=> $key['avatar']]);
			exit(0);
		}

		elseif ($params['identifiant'] === $key['email'])
		{
			$this->render('profile.twig', ['logged'=> TRUE,
			                                               'server' => $this->getServer(),
			                                               'user'=> $key['pseudo'],
			                                               'value'=> $key['email'],
			                                               'admin' => $key['admin'],
			                                               'avatar'=> $key['avatar']]);
		}

		else
			{
			$validChange = $userManager->updateUserMail($params['identifiant'], $key);

			if ($validChange){
				(new Session)->setValueKey('user', 'email', $params['identifiant']);
				$key['email'] = $params['identifiant'];

				$this->render('profile.twig', ['confirm' => TRUE,
				                                               'server' => $this->getServer(),
				                                               'logged' => TRUE,
				                                               'user' => $key['pseudo'],
				                                               'value' => $key['email'],
				                                               'admin' => $key['admin'],
				                                               'avatar'=> $key['avatar']]);

			}
		}
	}

	/**
	 * Vérifie que les mots de passe correspondent et le met à jour dans la BDD.
	 *
	 * Page Profil
	 */

	public function checkPassProfile(){
		$params = $this->getPost();
		$key = (new Session)->getKey('user');

		$this->checkForm($params);

		if($params['password'] != $params['confirme']){

			$this->render('profile.twig', ['valid' => TRUE,
			                                               'server' => $this->getServer(),
			                                               'logged'=> TRUE,
			                                               'user'=> $key['pseudo'],
			                                               'value'=> $key['email'],
			                                               'admin' => $key['admin'],
			                                               'avatar'=> $key['avatar']]);
			exit();
		}

		if ($params['password'] === $params['confirme']){

			(new AuthManager)->updateUserPass($params, $key);

			$this->render('profile.twig', ['confirm' => TRUE,
			                                               'server' => $this->getServer(),
			                                               'logged'=> TRUE,
			                                               'user'=> $key['pseudo'],
			                                               'value'=> $key['email'],
			                                               'admin' => $key['admin'],
			                                               'avatar'=> $key['avatar']]);

		}

	}


	/**
	 * Vérifie les entrées d'utilisateur des formulaires
	 *
	 * @param array $params
	 */
	public function checkForm(array $params){
		foreach ($params as $key => $value){

			if ($value != preg_replace('/\s+/', '', $value)){
				$this->render('profile.twig', ['space' => TRUE,
				                                               'server' => $this->getServer(),
				                                               'logged'=> TRUE,
				                                               'user'=> $key['pseudo'],
				                                               'admin' => $key['admin'],
				                                               'value'=> $key['email']]);
				exit(0);
			}

			if (strlen($value) < 4){
				$this->render('profile.twig', ['noValid' => TRUE,
				                                               'server' => $this->getServer(),
				                                               'logged'=> TRUE,
				                                               'user'=> $key['pseudo'],
				                                               'admin' => $key['admin'],
				                                               'value'=> $key['email']]);
				exit(0);
			}
		}
	}

	/**
	 * Vérifie les détails du fichier et enregistre le nom de l'avatar dans la BDD.
	 *
	 * L'avatar est ensuite enregistré dans le dossier public/images/avatar
	 */

	public function avatar(){

		$normalizeFile = $this->getUpFile();

		$maxSize = 500000;
		$fileSize = $normalizeFile['avatar']->getSize();
		$fileName = $normalizeFile['avatar']->getClientFilename();
		$extension = ['jpg', 'jpeg', 'gif', 'png'];

		$key = (new Session)->getKey('user');

		if($normalizeFile['avatar']->getError() > 0){
			$this->render('profile.twig', ['error' => TRUE,
			                                               'server' => $this->getServer(),
			                                               'logged'=> TRUE,
			                                               'user'=> $key['pseudo'],
			                                               'value'=> $key['email'],
			                                               'admin' => $key['admin'],
			                                               'avatar'=> $key['avatar']]);
		}

		if ($fileSize > $maxSize){
			$this->render('profile.twig', ['size' => TRUE,
			                                               'server' => $this->getServer(),
			                                               'logged'=> TRUE,
			                                               'user'=> $key['pseudo'],
			                                               'value'=> $key['email'],
			                                               'admin' => $key['admin'],
			                                               'avatar'=> $key['avatar']]);
		}

		$fileExtension = strtolower(substr($fileName, -3));

		if (!in_array($fileExtension, $extension)){
			$this->render('profile.twig', ['ext' => TRUE,
			                                               'server' => $this->getServer(),
			                                               'logged'=> TRUE,
			                                               'user'=> $key['pseudo'],
			                                               'value'=> $key['email'],
			                                               'admin' => $key['admin'],
			                                               'avatar'=> $key['avatar']]);
		}

		$uniqName = md5(uniqid(rand(), true));
		$newNameFile = $uniqName . "." . $fileExtension;
		$pathFile = "../public/images/avatar/". $newNameFile;
		$normalizeFile['avatar']->moveTo($pathFile);
		$moveFile = $normalizeFile['avatar']->isMoved();
		$oldFile = "../public/images/avatar/" . $key['avatar'];

		if ($moveFile){
			$userManager = new AuthManager();
			$upload = $userManager->updateAvatar($newNameFile, $key);

			if (file_exists($oldFile)){
				unlink($oldFile);
			}

			if ($upload){

				(new Session)->setValueKey('user', 'avatar', $newNameFile);
				$key['avatar'] = $newNameFile;
				$this->render('profile.twig', ['confirm' => TRUE,
				                                               'server' => $this->getServer(),
				                                               'logged'=> TRUE,
				                                               'user'=> $key['pseudo'],
				                                               'value'=> $key['email'],
				                                               'admin' => $key['admin'],
				                                               'avatar'=> $key['avatar']]);
			}
		}
	}
}

