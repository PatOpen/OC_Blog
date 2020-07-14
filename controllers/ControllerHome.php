<?php

class ControllerHome {

	private $url;
	private $twig;

	public function __construct($url, $twig) {
		$this->url = $url;
		$this->twig = $twig;
		$this->renderHome();

	}

	public function renderHome(){
		echo $this->twig->render( 'home.twig');
	}
}