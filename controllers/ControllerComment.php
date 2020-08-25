<?php

namespace OC_Blog\Controllers;

use OC_Blog\Models\CommentsManager;
use OC_Blog\Tools\ControllerFactory;
use OC_Blog\Tools\Session;


class ControllerComment extends ControllerFactory {

	public function addCommentPost(){
		$key = (new Session)->getKey('user');


		if (!isset($key)){
			header("Location: http://".$this->getServer()."/Auth/login");
			exit();
		}else{

			$userId = $key['id'];
			$comment = htmlentities(trim($this->getPost()['message']));
			$postId = (new Session)->getKey('post');

			$good = (new CommentsManager())->addComment($userId, $comment, $postId['id']);

			if ($good){
				header("Location: http://".$this->getServer()."/Post/viewPost/".$postId['id']);
			}else{
				echo 'Une erreur c\'est produite veuillez recommencer !' ;
			}
		}
	}

	public function updateComment(){

		$keyPost = (new Session)->getKey('post');
		$keyUser = (new Session)->getKey('user');
		$post = $this->getPost();

		if (isset($post['message'])){
			$this->checkComment($post['message'], $this->getSlug(), $keyPost['id']);
		}

		$content = (new CommentsManager())->oneComment((int) $this->getSlug());

		echo $this->getTwig()->render( 'comment.twig', ['logged'=> TRUE,
		                                            'server' => $this->getServer(),
		                                            'id' => $content['id'],
		                                            'admin' => $keyUser['admin'],
		                                            'user' => $keyUser['pseudo'],
		                                            'content'=> html_entity_decode($content['content'])]);

	}

	public function checkComment(string $comment, int $commentId, int $postId){

		$content = htmlentities(trim($comment), ENT_QUOTES);
		$update = (new CommentsManager())->updateComment($content, $commentId);

		if ($update){
			header('location: http://'.$this->getServer().'/Post/viewPost/'.$postId);
		}else{
			echo $this->getTwig()->render( 'comment.twig', ['logged'=> TRUE,
			                                            'error' => TRUE,
			                                            'server' => $this->getServer(),
			                                            'id' => $commentId,
			                                            'content'=> $content]);
		}
	}

	public function deleteComment(){

		$key = (new Session)->getKey('post');
		(new CommentsManager())->deleteComment($this->getSlug());
		header('location: http://'.$this->getServer().'/Post/viewPost/'. $key['id']);
	}

}
