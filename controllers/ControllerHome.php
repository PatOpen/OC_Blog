<?php

namespace OC_Blog\Controllers;

use OC_Blog\Models\PostsManager;

class ControllerHome {

	private $twig;


	public function __construct( $twig ) {

		$this->twig = $twig;
		$this->renderHome();

	}

	public function renderHome(){
		$posts = new PostsManager();
		$allPosts = $posts->listPosts();
		if(!empty($_SESSION)){
			$logged = true;
			echo $this->twig->render( 'home.twig', ['allPosts' => $allPosts, 'logged'=> $logged, 'user'=> $_SESSION['user']['pseudo']]);
		}else{
			$logged = false;
			echo $this->twig->render( 'home.twig', ['allPosts' => $allPosts, 'logged' => $logged]);
		}

	}
}