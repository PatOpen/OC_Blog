<?php


namespace OC_Blog\Controllers;

use OC_Blog\Models\AuthManager;
use OC_Blog\Tools\Session;


class ControllerAdmin {

	private array $_params;
	private array $_method;
	private object $_twig;
	private object $_userManager;

	public function __construct($method, $twig, $params){
		$this->_method = $method;
		$this->_twig = $twig;
		$this->_params = $params;
		$this->_userManager = new AuthManager();
		$target = $method[2];
		if (method_exists(ControllerAdmin::class, $target) ) {
			$this->$target();
		}else{
			echo $this->_twig->render('404.twig');
		}

	}

	public function profile(){
		$key = (new Session)->getKey('user');

		echo $this->_twig->render('profile.twig', ['logged' => TRUE,
		                                          'user'   => $key['pseudo'],
		                                          'value'  => $key['email'],
		                                           'server' => $_SERVER['SERVER_NAME'],
		                                          'avatar'=> $key['avatar']]);

	}

	public function checkMailProfile(){
		$params = $this->_params['post'];
		$key = (new Session)->getKey('user');

        $this->checkForm($params);

		$emailExist = $this->_userManager->checkEmail($params['identifiant']);

		if ($params['identifiant'] != $key['email'] && $emailExist == true)
		{
			$noEmail = true;
			echo $this->_twig->render('profile.twig', ['noEmail' => $noEmail,
			                                           'server' => $_SERVER['SERVER_NAME'],
			                                           'logged'=> TRUE,
			                                           'user'=> $key['pseudo'],
			                                           'value'=> $key['email']]);
			exit();
		}

		elseif ($params['identifiant'] === $key['email'])
		{
			echo $this->_twig->render('profile.twig', ['logged'=> TRUE,
			                                           'server' => $_SERVER['SERVER_NAME'],
			                                           'user'=> $key['pseudo'],
			                                           'value'=> $key['email']]);
		}

		else
			{
			$validChange = $this->_userManager->updateUserMail($params['identifiant'], $key);
			if ($validChange){
				$key['email'] = $params['identifiant'];
				$confirm = true;
				echo $this->_twig->render('profile.twig', ['confirm' => $confirm,
				                                           'server' => $_SERVER['SERVER_NAME'],
				                                           'logged'=> TRUE,
				                                           'user'=> $key['pseudo'],
				                                           'value'=> $key['email']]);

			}
		}



	}

	public function checkPassProfile(){
		$params = $this->_params['post'];
		$key = (new Session)->getKey('user');

		$this->checkForm($params);

		if($params['password'] != $params['confirme']){
			$valid = true;
			echo $this->_twig->render('profile.twig', ['valid' => $valid,
			                                           'server' => $_SERVER['SERVER_NAME'],
			                                           'logged'=> TRUE,
			                                           'user'=> $key['pseudo'],
			                                           'value'=> $key['email']]);
			exit();
		}

		if ($params['password'] === $params['confirme']){
			$confirm = true;
			$this->_userManager->updateUserPass($params, $key);
			echo $this->_twig->render('profile.twig', ['confirm' => $confirm,
			                                           'server' => $_SERVER['SERVER_NAME'],
			                                           'logged'=> TRUE,
			                                           'user'=> $key['pseudo'],
			                                           'value'=> $key['email']]);

		}

	}

	public function checkForm($params){
		foreach ($params as $key => $value){

			if ($value != preg_replace('/\s+/', '', $value)){
				$space = true;
				echo $this->_twig->render('profile.twig', ['space' => $space,
				                                           'server' => $_SERVER['SERVER_NAME'],
				                                           'logged'=> TRUE,
				                                           'user'=> $key['pseudo'],
				                                           'value'=> $key['email']]);
				exit();
			}

			if (strlen($value) < 4){
				$noValid = true;
				echo $this->_twig->render('profile.twig', ['noValid' => $noValid,
				                                           'server' => $_SERVER['SERVER_NAME'],
				                                           'logged'=> TRUE,
				                                           'user'=> $key['pseudo'],
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
			echo $this->_twig->render('profile.twig', ['error' => $error,
			                                           'server' => $_SERVER['SERVER_NAME'],
			                                           'logged'=> TRUE,
			                                           'user'=> $key['pseudo'],
			                                           'value'=> $key['email']]);
			exit();
		}

		if ($fileSize > $maxSize){
			$size = true;
			echo $this->_twig->render('profile.twig', ['size' => $size,
			                                           'server' => $_SERVER['SERVER_NAME'],
			                                           'logged'=> TRUE,
			                                           'user'=> $key['pseudo'],
			                                           'value'=> $key['email']]);
			exit();
		}

		$fileExtension = strtolower(substr($fileName, -3));

		if (!in_array($fileExtension, $extension)){
			$ext =true;
			echo $this->_twig->render('profile.twig', ['ext' => $ext,
			                                           'server' => $_SERVER['SERVER_NAME'],
			                                           'logged'=> TRUE,
			                                           'user'=> $key['pseudo'],
			                                           'value'=> $key['email']]);
			exit();
		}

		$uniqName = md5(uniqid(rand(), true));
		$newNameFile = $uniqName . "." . $fileExtension;
		$pathFile = "../public/images/avatar/". $newNameFile;
		$moveFile = move_uploaded_file($tmpName, $pathFile);
		$oldFile = "../public/images/avatar/" . $key['avatar'];

		if ($moveFile){

			$upload = $this->_userManager->updateAvatar($newNameFile, $key);

			if ($upload){

				$confirm = true;
				(new Session)->setValueKey('user', 'avatar', $newNameFile);
				echo $this->_twig->render('profile.twig', ['confirm' => $confirm,
				                                           'server' => $_SERVER['SERVER_NAME'],
				                                           'logged'=> TRUE,
				                                           'user'=> $key['pseudo'],
				                                           'value'=> $key['email'],
				                                           'avatar'=> $key['avatar']]);

				if (file_exists($oldFile)){
					unlink($oldFile);
				}
			}
		}

	}

}