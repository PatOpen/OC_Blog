<?php

namespace OC_Blog\Models;

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
	}

	public function checkUser($user):bool
	{
		$pseudo = $user;
		$sql = "SELECT * FROM users WHERE pseudo = ?";
		$req = $this->getBdd()->prepare($sql);
		$req->execute([$pseudo]);
		$test = $req->fetch();

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

		if ($test){
			return true;
		}else{
			return false;
		}


	}

}