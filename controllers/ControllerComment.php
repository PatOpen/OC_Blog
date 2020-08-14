<?php

namespace OC_Blog\Controllers;

use OC_Blog\Models\CommentsManager;
use OC_Blog\Tools\Session;


class ControllerComment {

	private array $_params;
	private array $_method;
	private object $_twig;
	private object $_commentManager;


	public function __construct($method, $twig, $params){
		$this->_method = $method;
		$this->_twig = $twig;
		$this->_params = $params;
		$this->_commentManager = new CommentsManager();
		$target = $method[2];
		if (method_exists(ControllerComment::class, $target) ) {
			$this->$target();
		}else{
			echo $this->_twig->render('404.twig');
		}
	}

	public function addCommentPost(){
		$key = (new Session)->getKey('user');


		if (!isset($key)){
			header("Location: http://".$_SERVER['SERVER_NAME']."/Auth/login");
			exit();
		}else{

			$userId = $key['id'];
			$comment = $this->_params['post']['message'];
			$postId = (new Session)->getKey('post');

			$good = $this->_commentManager->addComment($userId, $comment, $postId['id']);

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

		if (isset($this->_method[3]) && isset($keyUser['id']) && isset($keyPost['id'])){
			//$comentId = (int)$this->_method[3];
			//$user = $keyUser['id'];
			//$postId = $keyPost['id'];






		}


	}*/



}