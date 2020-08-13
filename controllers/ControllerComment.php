<?php

namespace OC_Blog\Controllers;

use OC_Blog\Models\CommentsManager;
use OC_Blog\Tools\Session;


class ControllerComment {

	private $params;
	private $method;
	private $twig;
	private $commentManager;


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
		$key = (new Session)->getKey('user');


		if (!isset($key)){
			header("Location: http://".$_SERVER['SERVER_NAME']."/Auth/login");
			exit();
		}else{

			$userId = $key['id'];
			$comment = $this->params['post']['message'];
			$postId = (new Session)->getKey('post');

			$good = $this->commentManager->addComment($userId, $comment, $postId['id']);

			if ($good){
				header("Location: http://".$_SERVER['SERVER_NAME']."/Post/".$postId['id']);
			}else{
				var_dump($good);
			}
		}
	}

/*	public function modifComment(){
		$keyUser = (new Session)->getKey('user');
		$keyPost = (new Session)->getKey('post');

		if (isset($this->method[3]) && isset($keyUser['id']) && isset($keyPost['id'])){
			//$comentId = (int)$this->method[3];
			//$user = $keyUser['id'];
			//$postId = $keyPost['id'];






		}


	}*/



}