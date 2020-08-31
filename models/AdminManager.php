<?php


namespace OC_Blog\Models;


use PDO;

class AdminManager extends Manager {

	/**
	 * Vérifie si l'utilisateur est un administrateur.
	 *
	 * @param int $userId
	 *
	 * @return bool
	 */
	public function checkAdmin( int $userId ): bool {

		$sql = "SELECT * FROM admin WHERE user_id = ?";
		$req = $this->getBdd()->prepare( $sql );
		$req->execute( [ $userId ] );
		$test = $req->fetch();

		$req->closeCursor();

		if ( $test ) {
			return true;
		} else {
			return false;
		}

	}

	/**
	 *Récupération de tout les commentaires qui n'ont pas été validé.
	 *
	 * @return array
	 */
	public function commentsPost(): array {

		$sql = "SELECT c.id AS commentId,
					   c.user_id,
					   c.post_id,
					   c.content,
					   c.validation,
					   TO_CHAR(c.create_at, 'DD/MM/YYYY à HH24hMI') AS comment_create_date,
					   TO_CHAR(c.modified_at, 'DD/MM/YYYY à HH24hMI') AS comment_modif_date,
					   p.user_id,
					   p.title,
					   u.avatar,
					   u.pseudo
					   FROM comment AS c
					   INNER JOIN post AS p
					   ON c.post_id = p.id
					   INNER JOIN users AS u 
					   ON c.user_id = u.id					   
					   WHERE c.validation = false";

		$req = $this->getBdd()->prepare( $sql );
		$req->execute();
		$req->setFetchMode( PDO::FETCH_ASSOC );
		$result = $req->fetchAll();

		$req->closeCursor();

		return $result;

	}
}
