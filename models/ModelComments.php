<?php


class ModelComments extends Manager {

	private $_id;
	private $_user_id;
	private $_post_id;
	private $_create_at;
	private $_content;
	private $_validation;

	public function __construct(array $data)
	{
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
	public function getUserId() {
		return $this->_user_id;
	}

	/**
	 * @return mixed
	 */
	public function getPostId() {
		return $this->_post_id;
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
	public function getContent() {
		return $this->_content;
	}

	/**
	 * @return mixed
	 */
	public function getValidation() {
		return $this->_validation;
	}

	/**
	 * @param mixed $id
	 */
	public function setId( int $id ) {

		if ( $id > 0 ) {
			$this->_id = $id;
		}
	}

	/**
	 * @param mixed $user_id
	 */
	public function setUserId( int $user_id ) {

		if ( $user_id > 0 ) {
			$this->_user_id = $user_id;
		}
	}

	/**
	 * @param mixed $post_id
	 */
	public function setPostId( int $post_id ) {

		if ( $post_id > 0 ) {
			$this->_post_id = $post_id;
		}
	}

	/**
	 * @param mixed $create_at
	 */
	public function setCreateAt( $create_at ) {
		$this->_create_at = $create_at;
	}

	/**
	 * @param mixed $content
	 */
	public function setContent( string $content ) {

		$this->_content = $content;
	}

	/**
	 * @param mixed $validation
	 */
	public function setValidation( bool $validation ) {
		$this->_validation = $validation;
	}


}