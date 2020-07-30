<?php

namespace OC_Blog\Controllers;

use OC_Blog\Models\PostsManager;

class ControllerHome {


	private $twig;


	public function __construct( $twig) {

		$this->twig = $twig;
		$this->renderHome();

	}

	public function renderHome(){
		$posts = new PostsManager();
		$allPosts = $posts->listPosts();
		echo $this->twig->render( 'home.twig', ['allPosts' => $allPosts]);
	}
}