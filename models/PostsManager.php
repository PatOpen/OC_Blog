<?php

namespace OC_Blog\Models;

use PDO;

class PostsManager extends Manager {


	public function listPosts() {

		$sql = "SELECT id,
					   title,
					   chapo,
					   description,
					   TO_CHAR(create_at, 'DD/MM/YYYY à HH24hMI') AS post_create_date,
					   TO_CHAR(modified_at, 'DD/MM/YYYY à HH24hMI') AS post_modif_date,					   
					   user_id,
					   image
					   FROM post
					   ORDER BY id DESC";

		$req = $this->getBdd()->prepare($sql);
		$req->execute();
		$req->setFetchMode( PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Posts' );
		$listPosts = $req->fetchAll();

		$req->closeCursor();

		return $listPosts;

	}

	public function getPost($id){
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

		$req = $this->getBdd()->prepare($sql);
		$req->bindValue(':id', $id);
		$req->execute();
		$req->setFetchMode( PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Posts' );
		$post = $req->fetchAll();

		$req->closeCursor();

		return $post;
	}

}