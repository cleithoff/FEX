<?php

class S {

	protected static $_s = null;

	public static function init() {
		if (empty(self::$_s)) {
			if (!Zend_Registry::isRegistered(I::_()->appnamespace . '_session')) {
				$session = new Zend_Session_Namespace(I::_()->appnamespace);
				$session->setExpirationSeconds(60);
				Zend_Registry::set(I::_()->appnamespace . '_session', $session);
			}
			self::$_s = Zend_Registry::get(I::_()->appnamespace . '_session');
		}
	}

	public static function get($key, $default=null) {
		self::init();
		if (!isset(self::$_s->$key)) {
			return $default;
		}
		return self::$_s->$key;
	}

	public static function set($key, $value) {
		self::init();
		self::$_s->$key = $value;
	}

	public static function uset($key) {
		self::init();
		unset(self::$_s->$key);

	}

	public static function has($key) {
		return !empty(self::$_s->$key);
	}

	public static function remove($key) {
		self::uset($key);
	}

	public static function _() {
		return self::$_s;
	}
}
