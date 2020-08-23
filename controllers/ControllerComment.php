<?php

namespace OC_Blog\Controllers;

use GuzzleHttp\Psr7\ServerRequest;
use OC_Blog\Tools\ConstantGlobal;
use OC_Blog\Models\CommentsManager;
use OC_Blog\Tools\Session;


class ControllerComment {

	private array $_params;
	private int $_slug;
	private object $_twig;
	private object $_commentManager;
	private string $_server;


	public function __construct( $twig, $slug, $params){
		$this->_slug = $slug;
		$this->_twig = $twig;
		$this->_params = $params;
		$this->_commentManager = new CommentsManager();
		$this->_server = ( new ConstantGlobal(ServerRequest::fromGlobals()) )->getServerName()['SERVER_NAME'];
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
				header("Location: http://".$this->_server."/Post/viewPost/".$postId['id']);
			}else{
				echo 'Une erreur c\'est produite veuillez recommencer !' ;
			}
		}
	}

	public function updateComment(){

		$keyPost = (new Session)->getKey('post');
		$keyUser = (new Session)->getKey('user');
		$post = $this->_params;

		if (isset($post['message'])){
			$this->checkComment($post['message'], $this->_slug, $keyPost['id']);
		}



		$content = $this->_commentManager->oneComment((int) $this->_slug);

		echo $this->_twig->render( 'comment.twig', ['logged'=> TRUE,
		                                            'server' => $this->_server,
		                                            'id' => $content['id'],
		                                            'admin' => $keyUser['admin'],
		                                            'user' => $keyUser['pseudo'],
		                                            'content'=> html_entity_decode($content['content'])]);

	}

	public function checkComment(string $comment, int $commentId, int $postId){

		$content = htmlentities(trim($comment), ENT_QUOTES);
		$update = $this->_commentManager->updateComment($content, $commentId);

		if ($update){
			header('location: http://'.$this->_server.'/Post/viewPost/'.$postId);
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
		$this->_commentManager->deleteComment($this->_slug);
		header('location: http://'.$this->_server.'/Post/viewPost/'. $key['id']);
	}

}
