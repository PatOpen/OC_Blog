<?php

namespace OC_Blog\Models;


use OC_Blog\Config\DBConstant;
use PDO;

abstract class Manager {

	private static object $_bdd;

	/**
	 * Connexion à la base de donnée.
	 */
	private function setBdd() {
			self::$_bdd = new PDO( 'pgsql:dbname='.DBConstant::DBNAME.';host='.DBConstant::DBHOST, DBConstant::USERNAME, DBConstant::PASSWORD );
			self::$_bdd->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
			self::$_bdd->setAttribute( PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ );
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
