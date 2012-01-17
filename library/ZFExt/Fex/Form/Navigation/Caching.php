<?php
class ZFExt_Fex_Form_Navigation_Caching extends Zend_Form {


	public function init() {

		$this->setMethod('post');

		$this->addElement('text','cache', array(
				'label'		=> 'Cache',
				'filters'	=> array('StripTags', 'StringTrim'),
		));

		$this->addElement('checkbox','perget', array(
				'label'		=> 'use GET params',
				'filters'	=> array('StripTags', 'StringTrim'),
		));

		$this->addElement('checkbox','persession', array(
				'label'		=> 'use session id',
				'filters'	=> array('StripTags', 'StringTrim'),
		));

		$this->addElement('text','lifetime', array(
				'label'		=> 'Lifetime',
				'filters'	=> array('StripTags', 'StringTrim'),
		));

	}

}