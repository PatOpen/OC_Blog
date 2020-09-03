<?php

namespace OC_Blog\Models;

use PDO;

class PostsManager extends Manager {

	/**
	 * Récupère tous les post de la BDD.
	 *
	 * @return array
	 */
	public function listPosts(): array {

		$sql = "SELECT id,
					   title,
					   chapo,
					   description,
					   TO_CHAR(create_at, 'DD/MM/YYYY à HH24hMI') AS post_create_date,
					   TO_CHAR(modified_at, 'DD/MM/YYYY à HH24hMI') AS post_modif_date,
					   image,
					   user_id
					   FROM post
					   ORDER BY id DESC";

		$req = $this->getBdd()->prepare( $sql );
		$req->execute();
		$req->setFetchMode( PDO::FETCH_ASSOC );
		$listPosts = $req->fetchAll();

		$req->closeCursor();

		return $listPosts;
	}

	/**
	 *Récupère les information d'un post et les infos de l'auteur de ce post.
	 *
	 * @param int $id
	 *
	 * @return array
	 */
	public function getPost( int $id ): array {
		$sql = "SELECT p.id,
					   title,
					   chapo,
					   description,
					   TO_CHAR(p.create_at, 'DD/MM/YYYY à HH24hMI') AS post_create_date,
					   TO_CHAR(modified_at, 'DD/MM/YYYY à HH24hMI') AS post_modif_date,
					   p.user_id,
					   p.image,
					   u.pseudo
					   FROM post AS p
					   INNER JOIN users AS u
					   ON p.user_id = u.id
					   WHERE p.id = :id";

		$req = $this->getBdd()->prepare( $sql );
		$req->execute( [ ':id' => $id ] );
		$req->setFetchMode( PDO::FETCH_ASSOC );
		$post = $req->fetchAll();

		$req->closeCursor();

		return $post;
	}

	/**
	 * Enregistre un nouvel article en BDD.
	 *
	 * @param array $form
	 * @param int $userId
	 * @param string $file
	 *
	 * @return bool
	 */
	public function addPost( array $form, int $userId, string $file ): bool {
		$sql = "INSERT INTO post ( title, chapo, description, create_at, modified_at, user_id, image)
				VALUES ( :title, :chapo, :description, now(), null, :user_id, :image)";

		$req    = $this->getBdd()->prepare( $sql );
		$result = $req->execute( [
			':title'      => $form['titre'],
			':chapo'      => $form['chapo'],
			'description' => $form['description'],
			':user_id'    => $userId,
			':image'      => $file
		] );
		$req->closeCursor();

		if ( $result ) {
			return true;
		} else {
			return false;
		}
	}

	public function updatePost( int $postId, array $form ) {
		$sql  = "UPDATE post 
				SET title = :title, chapo = :chapo, description = :description, modified_at = NOW(), image = :image 
				WHERE id = :id";
		$req  = $this->getBdd()->prepare( $sql );
		$post = $req->execute( [
			':id'          => $postId,
			':title'       => $form['titre'],
			':chapo'       => $form['chapo'],
			':description' => $form['description'],
			':image'       => $form['image']
		] );

		$req->closeCursor();

		if ( $post ) {
			return true;
		} else {
			return false;
		}
	}

	/**Supprime un article de la BDD.
	 *
	 * @param int $postId
	 */
	public function deletePost( int $postId ) {
		$this->getBdd()->exec( 'DELETE FROM post WHERE id = ' . $postId );
	}
}
