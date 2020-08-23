<?php

namespace OC_Blog\Controllers;

use GuzzleHttp\Psr7\ServerRequest;
use OC_Blog\Tools\ConstantGlobal;
use OC_Blog\Models\PostsManager;
use OC_Blog\Tools\Session;


class ControllerHome {

	private object $_twig;
	private string $_server;

	public function __construct( $twig ) {

		$this->_twig = $twig;
		$this->_server = ( new ConstantGlobal(ServerRequest::fromGlobals()) )->getServerName()['SERVER_NAME'];

	}

	public function home(){
		$posts = new PostsManager();
		$allPosts = $posts->listPosts();
		$key = (new Session)->getKey('user');


		if(!empty($key)){
			echo $this->_twig->render( 'home.twig', ['allPosts' => $allPosts,
			                                         'logged'=> TRUE,
			                                         'user'=> $key['pseudo'],
			                                         'admin' => $key['admin'],
			                                         'server' => $this->_server]);
		}else{
			echo $this->_twig->render( 'home.twig', ['allPosts' => $allPosts,
			                                         'logged' => FALSE,
													 'server'=> $this->_server]);
		}

	}
}
