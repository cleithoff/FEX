<?php

class P {
	
	protected static $_auth;
	protected static $_acl;
	
	public static function init($auth, $acl) {
		self::$_auth = $auth;
        self::$_acl = $acl;
	}
	
	public static function _($resource, $action) {
		if (self::$_auth->hasIdentity() && is_object(self::$_auth->getIdentity())) {
            $role = self::$_auth->getIdentity()->Role;
        } else {
            $role = 'guest';
        }

        if (!self::$_acl->has($resource)) {
            $resource = null;
        }
        return self::$_acl->isAllowed($role, $resource, $action);
	}
}