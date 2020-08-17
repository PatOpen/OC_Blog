<?php

namespace OC_Blog\Models;

use PDO;


class AuthManager extends Manager {


	public function registerUser($params){
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

	public function checkUser($user):bool
	{
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

	public function checkEmail($mail):bool
	{
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

	public function checkLogin($params)
	{
		$email = $params['identifiant'];

		$sql = "SELECT * FROM users WHERE email = ?";
		$req = $this->getBdd()->prepare($sql);
		$req->execute([$email]);
		$user = $req->fetch(PDO::FETCH_ASSOC);

		$req->closeCursor();

		if ($user && password_verify($params['password'], $user['password'])){
				return $user;
			}else{
				return false;
			}

	}

	public function updateUserMail($params, $userKey){

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

	public function updateUserPass($params, $userKey){
		$pass = password_hash($params['password'], PASSWORD_DEFAULT);

		$user = new Auth($userKey);
		$sql ="UPDATE users SET password = :password WHERE id = :id";
		$req = $this->getBdd()->prepare($sql);
		$req->execute([':id'=> $user->getId(),
		               ':password'=> $pass]);

		$req->closeCursor();

	}

	public function updateAvatar($params, $userId){

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
