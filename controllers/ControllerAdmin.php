<?php


namespace OC_Blog\Controllers;

use OC_Blog\Models\AuthManager;
use OC_Blog\Tools\Session;


class ControllerAdmin {

	private $params;
	private $method;
	private $twig;
	private $userManager;

	const LOGGED = true;

	public function __construct($method, $twig, $params){
		$this->method = $method;
		$this->twig = $twig;
		$this->params = $params;
		$this->userManager = new AuthManager();
		$target = $method[2];
		if (method_exists(ControllerAdmin::class, $target) ) {
			$this->$target();
		}else{
			echo $this->twig->render('404.twig');
		}

	}

	public function profile(){
		$key = (new Session)->getKey('user');

		echo $this->twig->render('profile.twig', ['logged' => self::LOGGED,
		                                          'user'   => $key['pseudo'],
		                                          'value'  => $key['email'],
		                                          'avatar'=> $key['avatar']]);

	}

	public function checkMailProfile(){
		$params = $this->params['post'];
		$key = (new Session)->getKey('user');

        $this->checkForm($params);

		$emailExist = $this->userManager->checkEmail($params['identifiant']);

		if ($params['identifiant'] != $key['email'] && $emailExist == true)
		{
			$noEmail = true;
			echo $this->twig->render('profile.twig', ['noEmail' => $noEmail, 'logged'=> self::LOGGED, 'user'=> $key['pseudo'], 'value'=> $key['email']]);
			exit();
		}

		elseif ($params['identifiant'] === $key['email'])
		{
			echo $this->twig->render('profile.twig', ['logged'=> self::LOGGED, 'user'=> $key['pseudo'], 'value'=> $key['email']]);
		}

		else
			{
			$validChange = $this->userManager->updateUserMail($params['identifiant'], $key);
			if ($validChange){
				$key['email'] = $params['identifiant'];
				$confirm = true;
				echo $this->twig->render('profile.twig', ['confirm' => $confirm, 'logged'=> self::LOGGED, 'user'=> $key['pseudo'], 'value'=> $key['email']]);

			}
		}



	}

	public function checkPassProfile(){
		$params = $this->params['post'];
		$key = (new Session)->getKey('user');

		$this->checkForm($params);

		if($params['password'] != $params['confirme']){
			$valid = true;
			echo $this->twig->render('profile.twig', ['valid' => $valid, 'logged'=> self::LOGGED, 'user'=> $key['pseudo'], 'value'=> $key['email']]);
			exit();
		}

		if ($params['password'] === $params['confirme']){
			$confirm = true;
			$this->userManager->updateUserPass($params, $key);
			echo $this->twig->render('profile.twig', ['confirm' => $confirm, 'logged'=> self::LOGGED, 'user'=> $key['pseudo'], 'value'=> $key['email']]);

		}

	}

	public function checkForm($params){
		foreach ($params as $key => $value){

			if ($value != preg_replace('/\s+/', '', $value)){
				$space = true;
				echo $this->twig->render('profile.twig', ['space' => $space, 'logged'=> self::LOGGED, 'user'=> $key['pseudo'], 'value'=> $key['email']]);
				exit();
			}

			if (strlen($value) < 4){
				$noValid = true;
				echo $this->twig->render('profile.twig', ['noValid' => $noValid, 'logged'=> self::LOGGED, 'user'=> $key['pseudo'], 'value'=> $key['email']]);
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
			echo $this->twig->render('profile.twig', ['error' => $error, 'logged'=> self::LOGGED, 'user'=> $key['pseudo'], 'value'=> $key['email']]);
			exit();
		}

		if ($fileSize > $maxSize){
			$size = true;
			echo $this->twig->render('profile.twig', ['size' => $size, 'logged'=> self::LOGGED, 'user'=> $key['pseudo'], 'value'=> $key['email']]);
			exit();
		}

		$fileExtension = strtolower(substr($fileName, -3));

		if (!in_array($fileExtension, $extension)){
			$ext =true;
			echo $this->twig->render('profile.twig', ['ext' => $ext, 'logged'=> self::LOGGED, 'user'=> $key['pseudo'], 'value'=> $key['email']]);
			exit();
		}

		$uniqName = md5(uniqid(rand(), true));
		$newNameFile = $uniqName . "." . $fileExtension;
		$pathFile = "../public/images/avatar/". $newNameFile;
		$moveFile = move_uploaded_file($tmpName, $pathFile);
		$oldFile = "../public/images/avatar/" . $key['avatar'];

		if ($moveFile){

			$upload = $this->userManager->updateAvatar($newNameFile, $key);

			if ($upload){

				$confirm = true;
				(new Session)->setValueKey('user', 'avatar', $newNameFile);
				echo $this->twig->render('profile.twig', ['confirm' => $confirm, 'logged'=> self::LOGGED, 'user'=> $key['pseudo'], 'value'=> $key['email'], 'avatar'=> $key['avatar']]);

				if (file_exists($oldFile)){
					unlink($oldFile);
				}
			}
		}

	}

}