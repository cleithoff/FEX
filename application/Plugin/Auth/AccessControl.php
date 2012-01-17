<?php

class Plugin_Auth_AccessControl extends Zend_Controller_Plugin_Abstract
{
	protected $_auth;
	protected $_acl;

	public function __construct(Zend_Auth $auth, Zend_Acl $acl) {
		$this->_auth = $auth;
		$this->_acl = $acl;
	}

	public function preDispatch(Zend_Controller_Request_Abstract $request) {
		if ($this->_auth->hasIdentity() && is_object($this->_auth->getIdentity())) {
			$role = $this->_auth->getIdentity()->Role;
		} else {
			$role = 'guest';
		}

		$controller = $request->getControllerName();
		Zend_Registry::set('controllername', $request->getControllerName());
		Zend_Registry::set('actionname', $request->getActionName());

		$resource = strtolower($controller);
		if (!$this->_acl->has($resource)) {
			$resource = null;
		}
		if (!$this->_acl->isAllowed($role, $resource, strtolower($request->getActionName()))) {
			if ($this->_auth->hasIdentity()) {
				// logged in, but no permission
				$request->setControllerName('error');
				$request->setActionName('noaccess');
			} else {
				// not logged in and no permission for guest
				$request->setControllerName('error');
				$request->setActionName('login');
			}
		}
	}
}
