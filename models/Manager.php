<?php

require_once 'config.php';

abstract class Manager {

	private static $_bdd;

	private static function setBdd() {
		try {
			$db = CONNECT;
			self::$_bdd = new PDO( "$db[dsn]", "$db[username]", "$db[passwd]" );
			self::$_bdd->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
			self::$_bdd->setAttribute( PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ );
		}

		catch ( Exception $e ) {
			die( 'Erreur : ' . $e->getMessage() );
		}
	}

	protected function getBdd() {
		if ( self::$_bdd == null ) {
			self::setBdd();
		}

		return self::$_bdd;
	}
}