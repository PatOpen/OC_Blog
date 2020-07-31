<?php

namespace OC_Blog\Models;

use PDO;

class CommentsManager extends Manager {

	public function listComments($postId){

		$req = $this->getBdd()->prepare("SELECT id, user_id, DATE_FORMAT(create_at, '%d/%m/%Y à %Hh%i') AS create_date, content, validation FROM comment WHERE post_id = :post_id ORDER BY id DESC");
		$req->execute([':post_id'=> $postId]);
		$req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Comments');

		return $req->fetchAll();

	}

	public function postComment($postId){
		$sql = "SELECT c.id,
					   c.user_id,
					   c.post_id,
					   TO_CHAR(c.create_at, 'DD/MM/YYYY à HH24hMI') AS comment_create_date,
					   content,
					   validation,
					   TO_CHAR(c.modified_at, 'DD/MM/YYYY à HH24hMI') AS comment_modif_date,
					   u.pseudo
					   FROM comment AS c
					   INNER JOIN users AS u
					   ON c.user_id = u.id
					   WHERE post_id = :postId AND validation = false ";

		$req = $this->getBdd()->prepare($sql);
		$req->bindValue(':postId', $postId);
		$req->execute();
		$req->setFetchMode( PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Comments' );
		$comments = $req->fetchAll();

		$req->closeCursor();

		return $comments;
	}



	public function addComments(Comments $comments){

		$req = $this->getBdd()->prepare('INSERT INTO comment(user_id, post_id, create_at, content, validation) VALUES ( :user_id, :post_id, NOW(), :content, false )');

		$req->blindValue(':user_id', $comments->getUserId(), PDO::PARAM_INT);
		$req->blindValue(':post_id', $comments->getPostId(), PDO::PARAM_INT);
		$req->blindValue(':content', $comments->getContent(), PDO::PARAM_STR);

		$req->execute();

	}

	public function deleteComments($id){

		$this->getBdd()->exec('DELETE FROM comment WHERE id = ' . (int) $id);

	}

	public function updateComments(Comments $comments){

		$req = $this->getBdd()->prepare('UPDATE comment SET user_id = :user_id, post_id = :post_id, modified_at = NOW(), content = :content, validation = false ');
		$req->blindValue(':user_id', $comments->getUserId(), PDO::PARAM_INT);
		$req->blindValue(':post_id', $comments->getPostId(), PDO::PARAM_INT);
		$req->blindValue(':content', $comments->getContent(), PDO::PARAM_STR);

		$req->execute();

	}

	public function validComments(Comments $comments){

		$req = $this->getBdd()->prepare('UPDATE comment SET validation = true WHERE id = :id');
		$req->blindValue(':id', $comments->getId(), PDO::PARAM_INT);

		$req->execute();

	}
}