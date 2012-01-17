<?php

class ZFExt_Fex_Controller_Action_Helper_Translate extends Zend_Controller_Action_Helper_Abstract {

	public function getlocales() {
		$translate = Zend_Registry::get('Zend_Translate');
		return $translate->getList();
	}
}