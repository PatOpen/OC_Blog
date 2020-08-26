<?php

namespace OC_Blog\Models;

use PDO;


class AuthManager extends Manager {

	/**
	 * Enregistre un utilisateur.
	 *
	 * @param array $params
	 */
	public function registerUser(array $params): void {
		$pass = password_hash($params['password'], PASSWORD_DEFAULT);
		$sql = "INSERT INTO users (pseudo, email, password, create_at) VALUES (:pseudo, :email, :password, now())";
		$req = $this->getBdd()->prepare($sql);
		$req->execute([
			':pseudo'   => $params['pseudo'],
			':email'    => $params['identifiant'],
			':password' => $pass
		]);

		$req->closeCursor();
	}

	/**
	 * Vérifie si un pseudo existe déjà.
	 *
	 * @param string $user
	 *
	 * @return bool
	 */
	public function checkUser(string $user): bool {
		$pseudo = $user;
		$sql = "SELECT * FROM users WHERE pseudo = ?";
		$req = $this->getBdd()->prepare($sql);
		$req->execute([$pseudo]);
		$test = $req->fetch();

		$req->closeCursor();

		if ($test){
			return true;
		}else{
			return false;
		}
	}

	/**
	 * Vérifie si un email existe déjà.
	 *
	 * @param string $mail
	 *
	 * @return bool
	 */
	public function checkEmail(string $mail): bool {
		$email = $mail;
		$sql = "SELECT * FROM users WHERE email = ?";
		$req = $this->getBdd()->prepare($sql);
		$req->execute([$email]);
		$test = $req->fetch();

		$req->closeCursor();

		if ($test){
			return true;
		}else{
			return false;
		}
	}

	/**
	 * Vérifie si les identifiants pour se connecter.
	 *
	 * @param array $params
	 *
	 * @return array|null
	 */
	public function checkLogin(array $params): ?array {
		$email = $params['identifiant'];

		$sql = "SELECT * FROM users WHERE email = ?";
		$req = $this->getBdd()->prepare($sql);
		$req->execute([$email]);
		$user = $req->fetch(PDO::FETCH_ASSOC);

		$req->closeCursor();

		if ($user && password_verify($params['password'], $user['password'])){
				return $user;
			}else{
				return null;
			}
	}

	/**
	 * Met à jour l'email d'un utilisateur.
	 *
	 * @param string $params
	 * @param array $userKey
	 *
	 * @return bool
	 */
	public function updateUserMail(string $params, array $userKey): bool {

		$user = new Auth($userKey);
		$sql ="UPDATE users SET email = :email WHERE id = :id";
		$req = $this->getBdd()->prepare($sql);
		$user = $req->execute([':id'=> $user->getId(),
		                       ':email'=> $params]);

		$req->closeCursor();

		if ($user){
			return true;
		}else{
			return false;
		}
	}

	/**
	 * Modofie le mot de passe.
	 *
	 * @param array $params
	 * @param array $userKey
	 */
	public function updateUserPass(array $params,array $userKey): void {
		$pass = password_hash($params['password'], PASSWORD_DEFAULT);

		$user = new Auth($userKey);
		$sql ="UPDATE users SET password = :password WHERE id = :id";
		$req = $this->getBdd()->prepare($sql);
		$req->execute([':id'=> $user->getId(),
		               ':password'=> $pass]);

		$req->closeCursor();
	}

	/**
	 * Enregistre le nom de l'avatar d'un utilisteur.
	 *
	 * @param string $params
	 * @param array $userId
	 *
	 * @return bool
	 */
	public function updateAvatar(string $params, array $userId): bool {

		$user = new Auth($userId);
		$sql ="UPDATE users SET avatar = :avatar WHERE id = :id";
		$req = $this->getBdd()->prepare($sql);
		$user = $req->execute([':id'=> $user->getId(),
		                       ':avatar'=> $params]);

		$req->closeCursor();

		if ($user){
			return true;
		}else{
			return false;
		}
	}
}

