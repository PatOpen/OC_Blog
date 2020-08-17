<?php

namespace OC_Blog\Models;

class Posts {

	private $_id;
	private $_title;
	private $_chapo;
	private $_description;
	private $_create_at;
	private $_modified_at;
	private $_user_id;

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
	public function getTitle() {
		return $this->_title;
	}

	/**
	 * @return mixed
	 */
	public function getChapo() {
		return $this->_chapo;
	}

	/**
	 * @return mixed
	 */
	public function getDescription() {
		return $this->_description;
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
	public function getModifiedAt() {
		return $this->_modified_at;
	}

	/**
	 * @return mixed
	 */
	public function getUserId() {
		return $this->_user_id;
	}

	/**
	 * @param mixed $id
	 */
	public function setId(int $id ) {

		if ($id > 0){
			$this->_id = $id;
		}
	}

	/**
	 * @param mixed $title
	 */
	public function setTitle(string $title ) {
		$this->_title = $title;
	}

	/**
	 * @param mixed $chapo
	 */
	public function setChapo(string $chapo ) {
		$this->_chapo = $chapo;
	}

	/**
	 * @param mixed $description
	 */
	public function setDescription(string $description ) {
		$this->_description = $description;
	}

	/**
	 * @param mixed $create_at
	 */
	public function setCreateAt( $create_at ) {
		$this->_create_at = $create_at;
	}

	/**
	 * @param mixed $modified_at
	 */
	public function setModifiedAt( $modified_at ) {
		$this->_modified_at = $modified_at;
	}

	/**
	 * @param mixed $user_id
	 */
	public function setUserId(int $user_id ) {

		if ($user_id > 0){
			$this->_user_id = $user_id;
		}
	}



}