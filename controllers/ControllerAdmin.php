<?php


namespace OC_Blog\Controllers;

use GuzzleHttp\Psr7\ServerRequest;
use OC_Blog\Tools\ConstantGlobal;
use OC_Blog\Models\AdminManager;
use OC_Blog\Models\AuthManager;
use OC_Blog\Models\CommentsManager;
use OC_Blog\Models\PostsManager;
use OC_Blog\Tools\Session;


class ControllerAdmin {

	private array $_params;
	private int $_slug;
	private object $_twig;
	private object $_userManager;
	private object $_adminManager;
	private object $_postsManager;
	private object $_commentManager;
	private string $_server;

	public function __construct( $twig, $slug, $params){
		$this->_slug = $slug;
		$this->_twig = $twig;
		$this->_params = $params;
		$this->_userManager = new AuthManager();
		$this->_adminManager = new AdminManager();
		$this->_postsManager = new PostsManager();
		$this->_commentManager = new CommentsManager();
		$this->_server = ( new ConstantGlobal(ServerRequest::fromGlobals()) )->getServerName()['SERVER_NAME'];

	}

	public function profile(){

		$key = (new Session)->getKey('user');

		if (isset($this->_params['identifiant'])){
			$this->checkMailProfile();
		}elseif (isset($this->_params['password'])){
			$this->checkPassProfile();
		}elseif (isset($_FILES['avatar'])){
			$this->avatar();
		}else{
			echo $this->_twig->render('profile.twig', ['logged' => TRUE,
			                                           'user'   => $key['pseudo'],
			                                           'value'  => $key['email'],
			                                           'admin' => $key['admin'],
			                                           'server' => $this->_server,
			                                           'avatar'=> $key['avatar']]);
		}
	}

	public function admin(){

		$key = (new Session)->getKey('user');
		$admin = $this->_adminManager->checkAdmin((int)$key['id']);


		if ($admin){
			$allComments = $this->_adminManager->commentsPost();
			echo $this->_twig->render('admin.twig', ['logged' => TRUE,
												     'user'   => $key['pseudo'],
												     'allComments'  => $allComments,
												     'server' => $this->_server,]);
		}else{
			echo 'une erreur est survenue';
		}
	}

	public function validate(){

		$commentId = $this->_slug;
		$validComment = $this->_commentManager->validComments((int)$commentId);

		if ($validComment){
			header("location: http://".$this->_server."/Admin/admin");
		}else{
			echo 'Une erreur est survenue';
		}


	}



	public function checkMailProfile(){
		$params = $this->_params;
		$key = (new Session)->getKey('user');

        $this->checkForm($params);

		$emailExist = $this->_userManager->checkEmail($params['identifiant']);

		if ($params['identifiant'] != $key['email'] && $emailExist == true)
		{
			$noEmail = true;
			echo $this->_twig->render('profile.twig', ['noEmail' => $noEmail,
			                                           'server' => $this->_server,
			                                           'logged'=> TRUE,
			                                           'user'=> $key['pseudo'],
			                                           'value'=> $key['email'],
			                                           'admin' => $key['admin'],
			                                           'avatar'=> $key['avatar']]);
			exit();
		}

		elseif ($params['identifiant'] === $key['email'])
		{
			echo $this->_twig->render('profile.twig', ['logged'=> TRUE,
			                                           'server' => $this->_server,
			                                           'user'=> $key['pseudo'],
			                                           'value'=> $key['email'],
			                                           'admin' => $key['admin'],
			                                           'avatar'=> $key['avatar']]);
		}

		else
			{
			$validChange = $this->_userManager->updateUserMail($params['identifiant'], $key);
			if ($validChange){
				(new Session)->setValueKey('user', 'email', $params['identifiant']);
				$key['email'] = $params['identifiant'];
				echo $this->_twig->render('profile.twig', ['confirm' => TRUE,
				                                           'server' => $this->_server,
				                                           'logged' => TRUE,
				                                           'user' => $key['pseudo'],
				                                           'value' => $key['email'],
				                                           'admin' => $key['admin'],
				                                           'avatar'=> $key['avatar']]);

			}
		}



	}

	public function checkPassProfile(){
		$params = $this->_params;
		$key = (new Session)->getKey('user');

		$this->checkForm($params);

		if($params['password'] != $params['confirme']){
			$valid = true;
			echo $this->_twig->render('profile.twig', ['valid' => $valid,
			                                           'server' => $this->_server,
			                                           'logged'=> TRUE,
			                                           'user'=> $key['pseudo'],
			                                           'value'=> $key['email'],
			                                           'admin' => $key['admin'],
			                                           'avatar'=> $key['avatar']]);
			exit();
		}

		if ($params['password'] === $params['confirme']){
			$confirm = true;
			$this->_userManager->updateUserPass($params, $key);
			echo $this->_twig->render('profile.twig', ['confirm' => $confirm,
			                                           'server' => $this->_server,
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
				echo $this->_twig->render('profile.twig', ['space' => $space,
				                                           'server' => $this->_server,
				                                           'logged'=> TRUE,
				                                           'user'=> $key['pseudo'],
				                                           'admin' => $key['admin'],
				                                           'value'=> $key['email']]);
				exit();
			}

			if (strlen($value) < 4){
				$noValid = true;
				echo $this->_twig->render('profile.twig', ['noValid' => $noValid,
				                                           'server' => $this->_server,
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
			echo $this->_twig->render('profile.twig', ['error' => $error,
			                                           'server' => $this->_server,
			                                           'logged'=> TRUE,
			                                           'user'=> $key['pseudo'],
			                                           'value'=> $key['email'],
			                                           'admin' => $key['admin'],
			                                           'avatar'=> $key['avatar']]);
			exit();
		}

		if ($fileSize > $maxSize){
			$size = true;
			echo $this->_twig->render('profile.twig', ['size' => $size,
			                                           'server' => $this->_server,
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
			echo $this->_twig->render('profile.twig', ['ext' => $ext,
			                                           'server' => $this->_server,
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
			$upload = $this->_userManager->updateAvatar($newNameFile, $key);

			if ($upload){

				(new Session)->setValueKey('user', 'avatar', $newNameFile);
				$key['avatar'] = $newNameFile;
				echo $this->_twig->render('profile.twig', ['confirm' => TRUE,
				                                           'server' => $this->_server,
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
