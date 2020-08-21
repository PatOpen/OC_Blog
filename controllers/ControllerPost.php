<?php

namespace OC_Blog\Controllers;

use GuzzleHttp\Psr7\ServerRequest;
use OC_Blog\Config\ConstantGlobal;
use OC_Blog\Models\CommentsManager;
use OC_Blog\Models\PostsManager;
use OC_Blog\Tools\Session;

class ControllerPost {

	private int $_slug;
	private object $_twig;
	private string $_server;


	public function __construct( $slug, $twig) {
		$this->_slug = $slug[2];
		$this->_twig = $twig;
		$this->_server = ( new ConstantGlobal(ServerRequest::fromGlobals()) )->getServerName()['SERVER_NAME'];
		$this->renderPost();

	}

	public function renderPost(){

		$post = new PostsManager();
		$comments = new CommentsManager();
		$thePost = $post->getPost($this->_slug);
		$allComments = $comments->postComment($this->_slug);
/*		foreach ($allComments as  $value){
			if (is_array($value)){
				foreach ($value as $key => $values){
					if ($values === 'content'){
						$values = html_entity_decode($values);
					}

					echo $key." : ".$values.'<br>';
				}
			}
		}
		var_dump($allComments);*/
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
													 'modifComment' => $modifComment]);
		}else{
			echo $this->_twig->render( 'post.twig', ['thePost' => $thePost,
			                                        'allComments' => $allComments,
			                                         'server' => $this->_server,
			                                        'logged'=> FALSE]);
		}
	}
}