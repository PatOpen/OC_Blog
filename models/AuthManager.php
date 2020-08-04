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


		if (password_verify($params['password'], $user['password'])){
			return $user;
		}else{
			return false;
		}
	}

}