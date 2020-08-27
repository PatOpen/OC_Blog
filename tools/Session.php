<?php


namespace OC_Blog\Tools;



class Session {

	private static Session $session;

	public function __construct() {

		if(session_status() == PHP_SESSION_NONE){
			session_start();
		}
	}

	/**
	 * Démarre une session si ce n'est pas déja fait.
	 *
	 * @return Session
	 */
	public static function getSession(): Session{

		if (!isset(self::$session)){
			self::$session = new Session();
		}

		return self::$session;
	}

	/**
	 * Ajoute ou modifie une session avec sa valeur.
	 *
	 * @param string $key
	 * @param array $value
	 */
	public function setKey(string $key, array $value): void {
		$_SESSION[$key] = $value;
	}

	/**
	 * Récupère les valeurs d'une session.
	 *
	 * @param string $key
	 *
	 * @return mixed|null
	 */
	public function getKey(string $key) {

		if (isset($_SESSION[$key])){
			return $_SESSION[$key];
		}
		else{
			return null;
		}
	}

	/**
	 * Modifie la valeur d'un tableau d'une session.
	 *
	 * @param string $key
	 * @param string $valueKey
	 * @param string $value
	 */
	public function setValueKey(string $key, string $valueKey, string $value): void {
		$_SESSION[$key][$valueKey] = $value;
	}
}
