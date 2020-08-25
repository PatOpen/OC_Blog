<?php


namespace OC_Blog\Controllers;


use OC_Blog\Models\AdminManager;
use OC_Blog\Models\AuthManager;
use OC_Blog\Models\CommentsManager;
use OC_Blog\Tools\ControllerFactory;
use OC_Blog\Tools\Session;


class ControllerAdmin extends ControllerFactory {


	public function profile(){

		$key = (new Session)->getKey('user');

		if (isset($this->getPost()['identifiant'])){
			$this->checkMailProfile();
		}elseif (isset($this->getPost()['password'])){
			$this->checkPassProfile();
		}elseif (isset($_FILES['avatar'])){
			$this->avatar();
		}else{
			echo $this->getTwig()->render('profile.twig', ['logged' => TRUE,
			                                           'user'   => $key['pseudo'],
			                                           'value'  => $key['email'],
			                                           'admin' => $key['admin'],
			                                           'server' => $this->getServer(),
			                                           'avatar'=> $key['avatar']]);
		}
	}

	public function admin(){

		$key = (new Session)->getKey('user');
		$adminManager = new AdminManager();
		$admin = $adminManager->checkAdmin((int)$key['id']);


		if ($admin){
			$allComments = $adminManager->commentsPost();
			echo $this->getTwig()->render('admin.twig', ['logged' => TRUE,
												     'user'   => $key['pseudo'],
												     'allComments'  => $allComments,
												     'server' => $this->getServer(),]);
		}else{
			echo 'une erreur est survenue';
		}
	}

	public function validate(){

		$commentId = $this->getSlug();
		$ommentManager = new CommentsManager();
		$validComment = $ommentManager->validComments((int)$commentId);

		if ($validComment){
			header("location: http://".$this->getServer()."/Admin/admin");
		}else{
			echo 'Une erreur est survenue';
		}


	}



	public function checkMailProfile(){
		$params = $this->getPost();
		$key = (new Session)->getKey('user');

        $this->checkForm($params);

		$userManager = new AuthManager();
		$emailExist = $userManager->checkEmail($params['identifiant']);

		if ($params['identifiant'] != $key['email'] && $emailExist == true)
		{
			$noEmail = true;
			echo $this->getTwig()->render('profile.twig', ['noEmail' => $noEmail,
			                                           'server' => $this->getServer(),
			                                           'logged'=> TRUE,
			                                           'user'=> $key['pseudo'],
			                                           'value'=> $key['email'],
			                                           'admin' => $key['admin'],
			                                           'avatar'=> $key['avatar']]);
			exit();
		}

		elseif ($params['identifiant'] === $key['email'])
		{
			echo $this->getTwig()->render('profile.twig', ['logged'=> TRUE,
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
				echo $this->getTwig()->render('profile.twig', ['confirm' => TRUE,
				                                           'server' => $this->getServer(),
				                                           'logged' => TRUE,
				                                           'user' => $key['pseudo'],
				                                           'value' => $key['email'],
				                                           'admin' => $key['admin'],
				                                           'avatar'=> $key['avatar']]);

			}
		}



	}

	public function checkPassProfile(){
		$params = $this->getPost();
		$key = (new Session)->getKey('user');

		$this->checkForm($params);

		if($params['password'] != $params['confirme']){
			$valid = true;
			echo $this->getTwig()->render('profile.twig', ['valid' => $valid,
			                                           'server' => $this->getServer(),
			                                           'logged'=> TRUE,
			                                           'user'=> $key['pseudo'],
			                                           'value'=> $key['email'],
			                                           'admin' => $key['admin'],
			                                           'avatar'=> $key['avatar']]);
			exit();
		}

		if ($params['password'] === $params['confirme']){
			$confirm = true;
			$userManager = new AuthManager();
			$userManager->updateUserPass($params, $key);
			echo $this->getTwig()->render('profile.twig', ['confirm' => $confirm,
			                                           'server' => $this->getServer(),
			                                           'logged'=> TRUE,
			                                           'user'=> $key['pseudo'],
			                                           'value'=> $key['email'],
			                                           'admin' => $key['admin'],
			                                           'avatar'=> $key['avatar']]);

		}

	}

	public function checkForm($params){
		foreach ($params as $key => $value){

			if ($value != preg_replace('/\s+/', '', $value)){
				$space = true;
				echo $this->getTwig()->render('profile.twig', ['space' => $space,
				                                           'server' => $this->getServer(),
				                                           'logged'=> TRUE,
				                                           'user'=> $key['pseudo'],
				                                           'admin' => $key['admin'],
				                                           'value'=> $key['email']]);
				exit();
			}

			if (strlen($value) < 4){
				$noValid = true;
				echo $this->getTwig()->render('profile.twig', ['noValid' => $noValid,
				                                           'server' => $this->getServer(),
				                                           'logged'=> TRUE,
				                                           'user'=> $key['pseudo'],
				                                           'admin' => $key['admin'],
				                                           'value'=> $key['email']]);
				exit();
			}
		}
	}

	public function avatar(){

		$maxSize = 500000;
		$fileSize = $_FILES['avatar']['size'];
		$tmpName = $_FILES['avatar']['tmp_name'];
		$fileName = $_FILES['avatar']['name'];
		$extension = ['jpg', 'jpeg', 'gif', 'png'];

		$key = (new Session)->getKey('user');

		if($_FILES['avatar']['error'] > 0){
			$error =true;
			echo $this->getTwig()->render('profile.twig', ['error' => $error,
			                                           'server' => $this->getServer(),
			                                           'logged'=> TRUE,
			                                           'user'=> $key['pseudo'],
			                                           'value'=> $key['email'],
			                                           'admin' => $key['admin'],
			                                           'avatar'=> $key['avatar']]);
			exit();
		}

		if ($fileSize > $maxSize){
			$size = true;
			echo $this->getTwig()->render('profile.twig', ['size' => $size,
			                                           'server' => $this->getServer(),
			                                           'logged'=> TRUE,
			                                           'user'=> $key['pseudo'],
			                                           'value'=> $key['email'],
			                                           'admin' => $key['admin'],
			                                           'avatar'=> $key['avatar']]);
			exit();
		}

		$fileExtension = strtolower(substr($fileName, -3));

		if (!in_array($fileExtension, $extension)){
			$ext =true;
			echo $this->getTwig()->render('profile.twig', ['ext' => $ext,
			                                           'server' => $this->getServer(),
			                                           'logged'=> TRUE,
			                                           'user'=> $key['pseudo'],
			                                           'value'=> $key['email'],
			                                           'admin' => $key['admin'],
			                                           'avatar'=> $key['avatar']]);
			exit();
		}

		$uniqName = md5(uniqid(rand(), true));
		$newNameFile = $uniqName . "." . $fileExtension;
		$pathFile = "../public/images/avatar/". $newNameFile;
		$moveFile = move_uploaded_file($tmpName, $pathFile);
		$oldFile = "../public/images/avatar/" . $key['avatar'];

		if ($moveFile){
			$userManager = new AuthManager();
			$upload = $userManager->updateAvatar($newNameFile, $key);

			if ($upload){

				(new Session)->setValueKey('user', 'avatar', $newNameFile);
				$key['avatar'] = $newNameFile;
				echo $this->getTwig()->render('profile.twig', ['confirm' => TRUE,
				                                           'server' => $this->getServer(),
				                                           'logged'=> TRUE,
				                                           'user'=> $key['pseudo'],
				                                           'value'=> $key['email'],
				                                           'admin' => $key['admin'],
				                                           'avatar'=> $key['avatar']]);


				if (file_exists($oldFile)){
					unlink($oldFile);
				}
			}

			exit();
		}

	}

}
