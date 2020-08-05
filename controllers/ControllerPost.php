<?php

namespace OC_Blog\Controllers;

use OC_Blog\Models\CommentsManager;
use OC_Blog\Models\PostsManager;

class ControllerPost {

	private $slug;
	private $twig;


	public function __construct( $slug, $twig) {
		$this->slug = (int)$slug;
		$this->twig = $twig;
		$this->renderPost();

	}

	public function renderPost(){

		$post = new PostsManager();
		$comments = new CommentsManager();
		$thePost = $post->getPost($this->slug);
		$allComments = $comments->postComment($this->slug);
		if (!empty($_SESSION)){
			$logged = true;
			echo $this->twig->render( 'post.twig', ['thePost' => $thePost,
			                                        'allComments' => $allComments,
			                                        'logged'=> $logged,
			                                        'user'=> $_SESSION['user']['pseudo']]);
		}else{
			$logged = false;
			echo $this->twig->render( 'post.twig', ['thePost' => $thePost,
			                                        'allComments' => $allComments,
			                                        'logged'=> $logged]);
		}

	}
}