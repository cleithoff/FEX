<?php

class Application_Model_DbTable_User extends Zend_Db_Table_Abstract
{

	protected $_name = 'user';

	public function getUserByUsername($username) {

		$user = new Application_Model_User();
		$row = $this->fetchRow("Username = " . $this->getAdapter()->quote($username));
		if (!$row) {
			return null;
		}

		$user->setUsername($row['Username'])
		->setPassword($row['Password'])
		->setSalt($row['Salt'])
		->setActive($row['Active'])
		->setAuthToken($row['AuthToken'])
		->setRole($row['Role'])
		;
		return $user;
	}

	public function load(Application_Model_User &$user) {
		if ($user->getId()) {
			$row = $this->fetchRow("id = " . intval($user->getId()));
		} elseif ($user->getUsername()) {
			$row = $this->fetchRow("Username = " . $this->getAdapter()->quote($user->getUsername()));
		} elseif ($user->getAuthToken()) {
			$row = $this->fetchRow("AuthToken = " . $this->getAdapter()->quote($user->getAuthToken()));
		}
		if (!$row) {
			return null;
		}

		$row = $row->toArray();

		$user->setUsername($row['Username'])
		->setPassword($row['Password'])
		->setSalt($row['Salt'])
		->setActive($row['Active'])
		->setAuthToken($row['AuthToken'])
		;
		return true;
	}

	public function save(Application_Model_User &$user, $password = false) {
		$data = array (
				'Username'  => $user->getUsername(),
				'Active'  => $user->getActive(),
				'AuthToken'  => U::buildToken(),
				'Role' => $user->getRole(),
		);

		if (null===($id = $user->getId())) {
			//Registration -- Validate.
			if (null===($registred=$this->getUserByUsername($user->getUsername()))) {
				//New user
				$data['Password'] = $user->getPassword();
				$data['Salt'] = $user->getSalt();
				$user->setActive(1);
				$data['Active'] = $user->getActive();
				$insert_id = $this->insert($data);
				$user->setId($insert_id);
			} else {
				return -1;
			}
		} else {
			if ($password) {
				$data['Password'] = $user->getPassword();
				$data['Salt'] = $user->getSalt();
			}
			$this->update($data, array('id=?' => intval($id)));
		}
		return $user->getId();
	}
}

