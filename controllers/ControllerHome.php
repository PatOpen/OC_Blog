<?php

namespace OC_Blog\Controllers;

use OC_Blog\Models\PostsManager;
use OC_Blog\Tools\Session;


class ControllerHome {

	private object $_twig;

	public function __construct( $twig ) {

		$this->_twig = $twig;
		$this->renderHome();

	}

	public function renderHome(){
		$posts = new PostsManager();
		$allPosts = $posts->listPosts();
		$key = (new Session)->getKey('user');

		if(!empty($key)){
			echo $this->_twig->render( 'home.twig', ['allPosts' => $allPosts,
			                                         'logged'=> TRUE,
			                                         'user'=> $key['pseudo'],
													  'server' => $_SERVER['SERVER_NAME']]);
		}else{
			echo $this->_twig->render( 'home.twig', ['allPosts' => $allPosts,
			                                         'logged' => FALSE,
													 'server'=> $_SERVER['SERVER_NAME']]);
		}

	}
}