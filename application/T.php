<?php

class T {
	
	protected static $_t = null;
	
	public static function _($string) {
		if (empty(self::$_t)) {
			self::$_t = Zend_Registry::get('Zend_Translate');
		}
		return self::$_t->_($string);
	}
	
}
