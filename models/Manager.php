<?php

namespace OC_Blog\Models;

use Exception;
use PDO;
use const OC_Blog\Config\DBConstant\{DBNAME, DBHOST, USERNAME, PASSWORD};

abstract class Manager {

	private static object $_bdd;

	/**
	 * Connexion à la base de donnée.
	 */
	private static function setBdd() {
		try {
			require_once '../config/DBConstant.php';
			self::$_bdd = new PDO( 'pgsql:dbname='.DBNAME.';host='.DBHOST, USERNAME, PASSWORD );
			self::$_bdd->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
			self::$_bdd->setAttribute( PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ );
		}

		catch ( Exception $e ) {
			echo( 'Erreur : ' . $e->getMessage() );
		}
	}

	/**
	 * Retourne une instance de PDO.
	 *
	 * @return object
	 */
	protected function getBdd(): object {
		if ( empty(self::$_bdd) ) {
			self::setBdd();
		}

		return self::$_bdd;
	}
}
