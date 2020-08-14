<?php

namespace OC_Blog\Controllers;

use OC_Blog\Models\CommentsManager;
use OC_Blog\Models\PostsManager;
use OC_Blog\Tools\Session;

class ControllerPost {

	private int $_slug;
	private object $_twig;


	public function __construct( $slug, $twig) {
		$this->_slug = (int)$slug;
		$this->_twig = $twig;
		$this->renderPost();

	}

	public function renderPost(){

		$post = new PostsManager();
		$comments = new CommentsManager();
		$thePost = $post->getPost($this->_slug);
		$allComments = $comments->postComment($this->_slug);
		$keyPost = 'post';

		(new Session)->setKey($keyPost, ['id' => $this->_slug]);
		$key = (new Session)->getKey('user');

		if (!empty($key)){
			$modifComment = $key['id'];
			echo $this->_twig->render( 'post.twig', ['thePost' => $thePost,
			                                        'allComments' => $allComments,
			                                        'logged'=> TRUE,
			                                        'user'=> $key['pseudo'],
			                                         'server' => $_SERVER['SERVER_NAME'],
													 'modifComment' => $modifComment]);
		}else{
			echo $this->_twig->render( 'post.twig', ['thePost' => $thePost,
			                                        'allComments' => $allComments,
			                                         'server' => $_SERVER['SERVER_NAME'],
			                                        'logged'=> FALSE]);
		}
	}
}