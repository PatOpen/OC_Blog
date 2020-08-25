<?php

namespace OC_Blog\Models;

use PDO;

class CommentsManager extends Manager {

	/**
	 * Récupère les commentaire validé du post donné.
	 *
	 * @param int $postId
	 *
	 * @return array
	 */
	public function postComment(int $postId): array{
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
					   WHERE post_id = :postId AND validation = true ";

		$req = $this->getBdd()->prepare($sql);
		$req->execute([':postId'=> $postId]);
		$req->setFetchMode( PDO::FETCH_ASSOC);
		$comments = $req->fetchAll();

		$req->closeCursor();

		return $comments;
	}

	/**
	 * Récupère un commentaire pour le modifier.
	 *
	 * @param int $commentId
	 *
	 * @return array
	 */
	public function oneComment(int $commentId): array{
		$sql = "SELECT id,
					   post_id,
					   content
					   FROM comment
					   WHERE id = :commentId";

		$req = $this->getBdd()->prepare($sql);
		$req->execute([':commentId'=> $commentId]);
		$req->setFetchMode( PDO::FETCH_ASSOC);
		$comments = $req->fetch();

		$req->closeCursor();

		return $comments;
	}

	/**
	 * Ajoute un commentaire pour un post en BDD.
	 *
	 * @param int $userId
	 * @param string $comment
	 * @param int $postId
	 *
	 * @return bool
	 */
	public function addComment(int $userId, string $comment, int $postId): bool {

		$sql = "INSERT INTO comment (user_id, post_id, create_at, content, validation, modified_at) 
				VALUES (:user_id, :post_id, now(), :content, false, null )";
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

	/**
	 * Supprime un commentaire.
	 *
	 * @param int $id
	 */
	public function deleteComment(int $id):void {

		$this->getBdd()->exec('DELETE FROM comment WHERE id = ' . (int) $id);

	}

	/**
	 * Enregistre la modification d'un commentaire.
	 *
	 * @param string $content
	 * @param int $id
	 *
	 * @return bool
	 */
	public function updateComment(string $content,int $id): bool {

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

	/**
	 * Enregistre la validation d'un commentaire.
	 *
	 * @param int $commentId
	 *
	 * @return bool
	 */
	public function validComments(int $commentId): bool {

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
