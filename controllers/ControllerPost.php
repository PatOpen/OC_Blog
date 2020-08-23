<?php

namespace OC_Blog\Controllers;

use GuzzleHttp\Psr7\ServerRequest;
use OC_Blog\Tools\ConstantGlobal;
use OC_Blog\Models\CommentsManager;
use OC_Blog\Models\PostsManager;
use OC_Blog\Tools\Session;

class ControllerPost {

	private int $_slug;
	private object $_twig;
	private string $_server;


	public function __construct(object $twig, int $slug) {
		$this->_slug = $slug;
		$this->_twig = $twig;
		$this->_server = ( new ConstantGlobal(ServerRequest::fromGlobals()) )->getServerName()['SERVER_NAME'];

	}

	public function viewPost(){

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
			                                         'server' => $this->_server,
			                                         'admin' => $key['admin'],
													 'modifComment' => $modifComment]);
		}else{
			echo $this->_twig->render( 'post.twig', ['thePost' => $thePost,
			                                        'allComments' => $allComments,
			                                         'server' => $this->_server,
			                                        'logged'=> FALSE]);
		}
	}
}
