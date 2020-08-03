<?php

namespace OC_Blog\Controllers;

use OC_Blog\Models\CommentsManager;

class ControllerComment {

	private $manager;

	public function __construct() {

		$this->manager = new CommentsManager();

	}



}