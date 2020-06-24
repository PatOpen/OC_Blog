<?php


abstract class ModelData {

	private static $bdd;

	private static function setBdd() {
		try {
			self::$bdd = new PDO( 'pgsql:dbname = OC_Blog;host= localhost;charset=utf8', 'lefumier', 'admin!' );
			self::$bdd->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
			self::$bdd->setAttribute( PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ );
		}

		catch ( Exception $e ) {
			die( 'Erreur : ' . $e->getMessage() );
		}
	}

	protected function getBdd() {
		if ( self::$bdd == null ) {
			self::setBdd();
		}

		return self::$bdd;
	}
}