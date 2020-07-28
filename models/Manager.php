<?php

namespace OC_Blog\Models;

use Exception;
use PDO;

abstract class Manager {

	private static $_bdd;

	private static function setBdd() {
		try {
			require_once "../config/DBConfig.php";
			self::$_bdd = new PDO( CONNECT['dsn'], CONNECT['username'], CONNECT['passwd'] );
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