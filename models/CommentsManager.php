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
					   u.pseudo,
					   u.avatar
					   FROM comment AS c
					   INNER JOIN users AS u
					   ON c.user_id = u.id
					   WHERE post_id = :postId AND validation = false ";

		$req = $this->getBdd()->prepare($sql);
		$req->bindValue(':postId', $postId);
		$req->execute();
		$req->setFetchMode( PDO::FETCH_ASSOC);
		$comments = $req->fetchAll();

		$req->closeCursor();

		return $comments;
	}

	public function oneComment($commentId){
		$sql = "SELECT id,
					   post_id,
					   content
					   FROM comment
					   WHERE id = :commentId";

		$req = $this->getBdd()->prepare($sql);
		$req->bindValue(':commentId', $commentId);
		$req->execute();
		$req->setFetchMode( PDO::FETCH_ASSOC);
		$comments = $req->fetch();

		$req->closeCursor();

		return $comments;
	}



	public function addComment($userId, $comment, $postId){

		$sql = "INSERT INTO comment (user_id, post_id, create_at, content, validation, modified_at) VALUES (:user_id, :post_id, now(), :content, false, null )";
		$req = $this->getBdd()->prepare($sql);
		$result = $req->execute([
			':user_id' => $userId,
			':post_id' => $postId,
			':content' => $comment
		]);

		$req->closeCursor();

		if ($result){
			return true;
		}else{
			return  false;
		}

	}

	public function deleteComment($id){

		$this->getBdd()->exec('DELETE FROM comment WHERE id = ' . (int) $id);

	}

	public function updateComment(string $content,int $id){

		$sql = "UPDATE comment SET modified_at = NOW(), content = :content, validation = false WHERE id = :id";
		$req = $this->getBdd()->prepare($sql);
		$comments = $req->execute([':id' => $id,
								  ':content'=> $content]);

		$req->closeCursor();

		if ($comments){
			return true;
		}else{
			return false;
		}

	}

	public function validComments(int $commentId){

		$req = $this->getBdd()->prepare('UPDATE comment SET validation = true WHERE id = :id');
		$valid = $req->execute([':id' => $commentId]);

		$req->closeCursor();

		if ($valid){
			return true;
		}else{
			return false;
		}

	}
}