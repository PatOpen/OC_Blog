<?php

namespace OC_Blog\Controllers;

use GuzzleHttp\Psr7\ServerRequest;
use OC_Blog\Config\ConstantGlobal;
use OC_Blog\Models\CommentsManager;
use OC_Blog\Tools\Session;


class ControllerComment {

	private array $_params;
	private array $_method;
	private object $_twig;
	private object $_commentManager;
	private string $_server;


	public function __construct($method, $twig, $params){
		$this->_method = $method;
		$this->_twig = $twig;
		$this->_params = $params;
		$this->_commentManager = new CommentsManager();
		$this->_server = ( new ConstantGlobal(ServerRequest::fromGlobals()) )->getServerName()['SERVER_NAME'];
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
			header("Location: http://".$this->_server."/Auth/login");
			exit();
		}else{

			$userId = $key['id'];
			$comment = htmlentities(trim($this->_params['message']));
			$postId = (new Session)->getKey('post');

			$good = $this->_commentManager->addComment($userId, $comment, $postId['id']);

			if ($good){
				header("Location: http://".$this->_server."/Post/".$postId['id']);
			}else{
				echo 'Une erreur c\'est produite veuillez recommencer !' ;
			}
		}
	}

	private function updateComment(){

		$key = (new Session)->getKey('post');
		$commentId = $this->_method[3];
		$post = $this->_params;

		if (isset($post['message'])){
			$this->checkComment($post['message'], $commentId, $key['id']);
		}



		$content = $this->_commentManager->oneComment((int) $commentId);

		echo $this->_twig->render( 'comment.twig', ['logged'=> TRUE,
		                                            'server' => $this->_server,
		                                            'id' => $content['id'],
		                                            'content'=> html_entity_decode($content['content'])]);

	}

	public function checkComment(string $comment, int $commentId, int $postId){

		$content = htmlentities(trim($comment), ENT_QUOTES);
		$update = $this->_commentManager->updateComment($content, $commentId);

		if ($update){
			header('location: http://'.$this->_server.'/Post/'.$postId);
		}else{
			echo $this->_twig->render( 'comment.twig', ['logged'=> TRUE,
			                                            'error' => TRUE,
			                                            'server' => $this->_server,
			                                            'id' => $commentId,
			                                            'content'=> $content]);
		}
	}

	public function deleteComment(){

		$key = (new Session)->getKey('post');
		$this->_commentManager->deleteComment($this->_method[3]);
		header('location: http://'.$this->_server.'/Post/'. $key['id']);
	}

}
