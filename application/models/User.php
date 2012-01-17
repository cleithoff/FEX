<?php

class Application_Model_User
{
	protected $_id = null;
	protected $_Username;
	protected $_Password;
	protected $_Salt;
	protected $_Active;
	protected $_AuthToken;
	protected $_Role = 'user';

	public function __construct(array $options = null) {
		if (!empty($options['data'])) {
			$this->build($options['data']);
		}
	}

	/**
	 * @return the $_id
	 */
	public function getId() {
		return $this->_id;
	}

	/**
	 * @param field_type $_id
	 */
	public function setId($_id) {
		$this->_id = $_id;
		return $this;
	}

	/**
	 * @return the $_Username
	 */
	public function getUsername() {
		return $this->_Username;
	}

	/**
	 * @param field_type $_Username
	 */
	public function setUsername($_Username) {
		$this->_Username = $_Username;
		return $this;
	}

	/**
	 * @return the $_Password
	 */
	public function getPassword() {
		return $this->_Password;
	}

	/**
	 * @param field_type $_Password
	 */
	public function setPassword($_Password) {
		//Calculating dynamic salt
		$dynamicSalt = '';
		for ($i = 0; $i < 20; $i++) {
			$dynamicSalt .= chr(rand(33, 126));
		}

		$this->_Salt=$dynamicSalt;
		$this->_Password = (string)sha1($_Password.$dynamicSalt);
		return $this;
	}

	/**
	 * @return the $_Salt
	 */
	public function getSalt() {
		return $this->_Salt;
	}

	/**
	 * @param field_type $_Salt
	 */
	public function setSalt($_Salt) {
		$this->_Salt = $_Salt;
		return $this;
	}
	/**
	 * @return the $_Active
	 */
	public function getActive() {
		return $this->_Active;
	}

	/**
	 * @param field_type $_Active
	 */
	public function setActive($_Active) {
		$this->_Active = $_Active;
		return $this;
	}

	/**
	 * @return the $_AuthToken
	 */
	public function getAuthToken() {
		return $this->_AuthToken;
	}

	/**
	 * @param field_type $_AuthToken
	 */
	public function setAuthToken($_AuthToken) {
		$this->_AuthToken = $_AuthToken;
		return $this;
	}
	/**
	 * @return the $_Role
	 */
	public function getRole() {
		return $this->_Role;
	}

	/**
	 * @param field_type $_Role
	 */
	public function setRole($_Role) {
		$this->_Role = $_Role;
		return $this;
	}

	public function build($data) {
		foreach ($data as $key => $value) {
			$method = 'set' . $key;
			if(method_exists($this, $method)) {
				$this->$method($value);
			}
		}
	}

	public function load() {
		$user = new Application_Model_DbTable_User();
		$user->load($this);
	}

	public function save($password = false)
	{
		$user = new Application_Model_DbTable_User();
		return $user->save($this, $password);
	}

}

