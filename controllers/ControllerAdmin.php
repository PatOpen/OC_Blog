<?php


namespace OC_Blog\Controllers;

use OC_Blog\Models\AuthManager;


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

		if (method_exists(ControllerAdmin::class, $method) ) {
			$this->$method();
		}else{
			echo $this->twig->render('404.twig');
		}

	}

	public function profile(){
		echo $this->twig->render('profile.twig', ['logged' => self::LOGGED,
		                                          'user'   => $_SESSION['user']['pseudo'],
		                                          'value'  => $_SESSION['user']['email']]);

	}

	public function checkMailProfile(){
		$params = $this->params['post'];

        $this->checkForm($params);

		$emailExist = $this->userManager->checkEmail($params['identifiant']);

		if ($params['identifiant'] != $_SESSION['user']['email'] && $emailExist == true)
		{
			$noEmail = true;
			echo $this->twig->render('profile.twig', ['noEmail' => $noEmail, 'logged'=> self::LOGGED, 'user'=> $_SESSION['user']['pseudo'], 'value'=> $_SESSION['user']['email']]);
			exit();
		}

		elseif ($params['identifiant'] === $_SESSION['user']['email'])
		{
			echo $this->twig->render('profile.twig', ['logged'=> self::LOGGED, 'user'=> $_SESSION['user']['pseudo'], 'value'=> $_SESSION['user']['email']]);
		}

		else
			{
			$validChange = $this->userManager->updateUserMail($params['identifiant']);
			if ($validChange){
				$_SESSION['user']['email'] = $params['identifiant'];
				$confirm = true;
				echo $this->twig->render('profile.twig', ['confirm' => $confirm, 'logged'=> self::LOGGED, 'user'=> $_SESSION['user']['pseudo'], 'value'=> $_SESSION['user']['email']]);

			}
		}



	}

	public function checkPassProfile(){
		$params = $this->params['post'];

		$this->checkForm($params);

		if($params['password'] != $params['confirme']){
			$valid = true;
			echo $this->twig->render('profile.twig', ['valid' => $valid, 'logged'=> self::LOGGED, 'user'=> $_SESSION['user']['pseudo'], 'value'=> $_SESSION['user']['email']]);
			exit();
		}

		if ($params['password'] === $params['confirme']){
			$confirm = true;
			$this->userManager->updateUserPass($params);
			echo $this->twig->render('profile.twig', ['confirm' => $confirm, 'logged'=> self::LOGGED, 'user'=> $_SESSION['user']['pseudo'], 'value'=> $_SESSION['user']['email']]);

		}

	}

	public function checkForm($params){
		foreach ($params as $key => $value){

			if ($value != preg_replace('/\s+/', '', $value)){
				$space = true;
				echo $this->twig->render('profile.twig', ['space' => $space, 'logged'=> self::LOGGED, 'user'=> $_SESSION['user']['pseudo'], 'value'=> $_SESSION['user']['email']]);
				exit();
			}

			if (strlen($value) < 4){
				$noValid = true;
				echo $this->twig->render('profile.twig', ['noValid' => $noValid, 'logged'=> self::LOGGED, 'user'=> $_SESSION['user']['pseudo'], 'value'=> $_SESSION['user']['email']]);
				exit();
			}
		}
	}

}