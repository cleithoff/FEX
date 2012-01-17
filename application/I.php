<?php

define('DEFAULT_INI_CUSTOMIZE', APPLICATION_PATH.'/configs/customize.ini');

class I {

	protected static $_i = null;

	public static function _($ini = DEFAULT_INI_CUSTOMIZE, $section = APPLICATION_ENV) {
		if (empty(self::$_i)) {
			self::$_i = new Zend_Config_Ini($ini, $section);
		}
		return self::$_i;
	}


}