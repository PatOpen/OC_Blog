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
		echo $this->twig->render( 'post.twig', ['thePost' => $thePost,
												'allComments' => $allComments]);
	}
}