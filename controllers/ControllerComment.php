<?php

namespace OC_Blog\Controllers;

use OC_Blog\Models\CommentsManager;


class ControllerComment {

	private $params;
	private $method;
	private $twig;
	private $commentManager;

	const LOGGED = true;

	public function __construct($method, $twig, $params){
		$this->method = $method;
		$this->twig = $twig;
		$this->params = $params;
		$this->commentManager = new CommentsManager();
		$target = $method[2];
		if (method_exists(ControllerComment::class, $target) ) {
			$this->$target();
		}else{
			echo $this->twig->render('404.twig');
		}
	}

	public function addCommentPost(){


		if (!isset($_SESSION['user'])){
			header("Location: http://localhost:8000/Auth/login");
			exit();
		}

		if (isset($_SESSION['user'])){

			$userId = $_SESSION['user']['id'];
			$comment = $this->params['post']['message'];
			$postId = $_SESSION['post'];

			$good = $this->commentManager->addComment($userId, $comment, $postId);

			if ($good){
				header("Location: http://localhost:8000/Post/$postId");
			}else{
				var_dump($good);
			}
		}
	}

	public function modifComment(){

		if (isset($this->method[3]) && isset($_SESSION['user']['id'])){
			$comentId = $this->method[3];
			$user = $_SESSION['user']['id'];
			$postId = $_SESSION['post']['id'];
			var_dump($_SESSION['post']['id']);
		}


	}



}