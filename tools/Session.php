<?php


namespace OC_Blog\Tools;


class Session {

	private static $session;

	public function __construct() {

		if(session_status() == PHP_SESSION_NONE){
			session_start();
		}
	}

	public static function newSession(){

		if (!self::$session){
			self::$session = new Session();
		}
	}

	public function setKey($key, $value){

		$_SESSION[$key] = $value;

	}

	public function getKey($key){

		if (isset($_SESSION[$key])){
			return $_SESSION[$key];
		}
		else{
			return null;
		}

	}

}