<?php
class Plugin_Auth_Acl extends Zend_Acl
{
	protected $_config;

	public function __construct()
	{
		$this->_config = (string) APPLICATION_PATH."/configs/acl.ini";

		if(file_exists($this->_config)) {
			// config to array
			$config = new Zend_config_Ini($this->_config);
			$config = $config->toArray();

			foreach ($config as $res => $value) {
				if ($res == '_role') {
					foreach ($value as $parent => $child) {
						if (!$this->_getRoleRegistry()->has($parent)) {
							// add roles
							$this->addRole(new Zend_Acl_Role($parent));
						}
						if (!$this->_getRoleRegistry()->has($child) && $this->_getRoleRegistry()->has($parent)) {
							// add roles
							$this->addRole(new Zend_Acl_Role($child), $parent);
						} else {
							$this->addRole($this->_getRoleRegistry()->get($child), $parent);
						}
					}
				} else {
					// add ressources eg. controllers
					$this->addResource(new Zend_Acl_Resource($res));
					foreach ($value as $role => $actions) {
						if (!$this->_getRoleRegistry()->has($role)) {
							// add roles
							$this->addRole(new Zend_Acl_Role($role));
						}
						foreach ($actions as $action => $rules) {
							//set permissions
							// if action == all then set permission for every ressource
							if($action == 'all') {
								if ($rules == 'allow') {
									$this->allow($role, $res, null);
								} else {
									$this->deny($role, $res, null);
								}
							} else {
								if ($rules == 'allow') {
									$this->allow($role, $res, $action);
								} else {
									$this->deny($role, $res, $action);
								}
							}
						}
					}
				}
			}
		} else {
			throw new Zend_Acl_Exception('acl config file not found');
		}
	}
}