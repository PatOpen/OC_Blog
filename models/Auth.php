<?php

namespace OC_Blog\Models;

class Auth {

	private $_id;
	private $_pseudo;
	private $_email;
	private $_password;
	private $_create_at;
	private $_avatar;

	public function __construct(array $data)
	{
		if(!empty($data))
		{
			$this -> hydrate($data);
		}

	}

	public function hydrate($data){

		foreach ($data as $key => $value)
		{
			$method = 'set'.ucfirst($key);

			if (method_exists($this, $method))
			{
				$this->$method($value);
			}
		}
	}

	/**
	 * @return mixed
	 */
	public function getId() {
		return $this->_id;
	}

	/**
	 * @return mixed
	 */
	public function getPseudo() {
		return $this->_pseudo;
	}

	/**
	 * @return mixed
	 */
	public function getEmail() {
		return $this->_email;
	}

	/**
	 * @return mixed
	 */
	public function getPassword() {
		return $this->_password;
	}

	/**
	 * @return mixed
	 */
	public function getCreateAt() {
		return $this->_create_at;
	}

	/**
	 * @return mixed
	 */
	public function getAvatar() {
		return $this->_avatar;
	}

	/**
	 * @param mixed $id
	 */
	public function setId(int $id ) {

			if($id > 0){
				$this->_id = $id;
			}
	}

	/**
	 * @param mixed $pseudo
	 */
	public function setPseudo(string $pseudo ) {

			$this->_pseudo = $pseudo;
	}

	/**
	 * @param mixed $email
	 */
	public function setEmail(string $email ) {
		$this->_email = $email;
	}

	/**
	 * @param mixed $password
	 */
	public function setPassword(string $password ) {
		$this->_password = $password;
	}

	/**
	 * @param mixed $create_at
	 */
	public function setCreateAt( $create_at ) {
		$this->_create_at = $create_at;
	}

	/**
	 * @param mixed $avatar
	 */
	public function setAvatar( $avatar ) {
		$this->_avatar = $avatar;
	}



}