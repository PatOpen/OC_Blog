<?php

namespace OC_Blog\Controllers;

use OC_Blog\Models\CommentsManager;
use OC_Blog\Models\PostsManager;
use OC_Blog\Tools\ControllerFactory;
use OC_Blog\Tools\Session;

class ControllerPost extends ControllerFactory {

	/**
	 * Récupère les commentaires d'un post avant d'affiche la page du post et ses commantaires.
	 */
	public function viewPost() : void {

		$post = new PostsManager();
		$comments = new CommentsManager();
		$thePost = $post->getPost($this->getSlug());
		$allComments = $comments->postComment($this->getSlug());
		$keyPost = 'post';

		(new Session)->setKey($keyPost, ['id' => $this->getSlug()]);
		$key = (new Session)->getKey('user');
		if (!empty($key)){
			$modifComment = $key['id'];
			$this->render( 'post.twig', ['thePost' => $thePost,
			                                             'allComments' => $allComments,
			                                             'logged'=> TRUE,
			                                             'user'=> $key['pseudo'],
			                                             'server' => $this->getServer(),
			                                             'admin' => $key['admin'],
														 'modifComment' => $modifComment]);
		}else{
			$this->render( 'post.twig', ['thePost' => $thePost,
			                                             'allComments' => $allComments,
			                                             'server' => $this->getServer(),
			                                             'logged'=> FALSE]);
		}
	}
}

