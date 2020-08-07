<?php

namespace OC_Blog\Controllers;

use OC_Blog\Models\CommentsManager;
use OC_Blog\Models\PostsManager;
use OC_Blog\Tools\Session;

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
		$keyPost = 'post';

		(new Session)->setKey($keyPost, $thePost[0]['id']);
		$key = (new Session)->getKey('user');

		if (!empty($key)){
			$modifComment = $key['id'];
			$logged = true;
			echo $this->twig->render( 'post.twig', ['thePost' => $thePost,
			                                        'allComments' => $allComments,
			                                        'logged'=> $logged,
			                                        'user'=> $key['pseudo'],
													 'modifComment' => $modifComment]);
		}else{
			$logged = false;
			echo $this->twig->render( 'post.twig', ['thePost' => $thePost,
			                                        'allComments' => $allComments,
			                                        'logged'=> $logged]);
		}
	}
}