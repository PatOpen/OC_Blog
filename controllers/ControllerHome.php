<?php

namespace OC_Blog\Controllers;

use OC_Blog\Models\PostsManager;
use OC_Blog\Tools\Session;

class ControllerHome {

	private $twig;


	public function __construct( $twig ) {

		$this->twig = $twig;
		$this->renderHome();

	}

	public function renderHome(){
		$posts = new PostsManager();
		$allPosts = $posts->listPosts();
		$key = (new Session)->getKey('user');

		if(!empty($key)){
			$logged = true;
			echo $this->twig->render( 'home.twig', ['allPosts' => $allPosts, 'logged'=> $logged, 'user'=> $key['pseudo']]);
		}else{
			$logged = false;
			echo $this->twig->render( 'home.twig', ['allPosts' => $allPosts, 'logged' => $logged]);
		}

	}
}