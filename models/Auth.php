<?php

namespace OC_Blog\Models;


class Auth {

	private int $_id;
	private string $_pseudo;
	private string $_email;
	private string $_password;
	private string $_create_at;
	private $_avatar;
	private bool $_admin;

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
	 * @return int
	 */
	public function getId(): int {
		return $this->_id;
	}

	/**
	 * @return string
	 */
	public function getPseudo(): string {
		return $this->_pseudo;
	}

	/**
	 * @return string
	 */
	public function getEmail(): string {
		return $this->_email;
	}

	/**
	 * @return string
	 */
	public function getPassword(): string {
		return $this->_password;
	}

	/**
	 * @return string
	 */
	public function getCreateAt(): string {
		return $this->_create_at;
	}

	/**
	 * @return string
	 */
	public function getAvatar(): string {
		return $this->_avatar;
	}

	/**
	 * @param int $id
	 */
	public function setId(int $id ): void {

			if($id > 0){
				$this->_id = $id;
			}
	}

	/**
	 * @param string $pseudo
	 */
	public function setPseudo(string $pseudo ) {

			$this->_pseudo = $pseudo;
	}

	/**
	 * @param string $email
	 */
	public function setEmail(string $email ) {
		$this->_email = $email;
	}

	/**
	 * @param string $password
	 */
	public function setPassword(string $password ) {
		$this->_password = $password;
	}

	/**
	 * @param $create_at
	 */
	public function setCreateAt( $create_at ) {
		$this->_create_at = $create_at;
	}


	public function setAvatar( $avatar ) {
		$this->_avatar = $avatar;
	}

	/**
	 * @return bool
	 */
	public function getAdmin(): bool {
		return $this->_admin;
	}

	/**
	 * @param bool $admin
	 */
	public function setAdmin(bool $admin ): void {
		$this->_admin = $admin;
	}
}
