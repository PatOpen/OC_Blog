<?php

namespace OC_Blog\Controllers;

//use GuzzleHttp\Psr7\ServerRequest;
//use OC_Blog\Tools\ConstantGlobal;
use OC_Blog\Models\PostsManager;
use OC_Blog\Tools\ControllerFactory;
use OC_Blog\Tools\Session;


class ControllerHome extends ControllerFactory {

	public function home(){
		$posts = new PostsManager();
		$allPosts = $posts->listPosts();
		$key = (new Session)->getKey('user');


		if(!empty($key)){
			echo $this->getTwig()->render( 'home.twig', ['allPosts' => $allPosts,
			                                         'logged'=> TRUE,
			                                         'user'=> $key['pseudo'],
			                                         'admin' => $key['admin'],
			                                         'server' => $this->getServer()]);
		}else{
			echo $this->getTwig()->render( 'home.twig', ['allPosts' => $allPosts,
			                                         'logged' => FALSE,
													 'server'=> $this->getServer()]);
		}

	}
}
