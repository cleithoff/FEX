<?php
class ZFExt_Fex_Form_Navigation_Metadata extends Zend_Form {


	public function init() {

		$this->setMethod('post');

		$this->addElement('text','hash', array(
				'label'		=> 'Hash',
				'filters'	=> array('StripTags', 'StringTrim'),
		));

		$this->addElement('text','attribute', array(
				'label'		=> 'Attribute',
				'filters'	=> array('StripTags', 'StringTrim'),
		));

		$this->addElement('text','metakey', array(
				'label'		=> 'Meta Key',
				'filters'	=> array('StripTags', 'StringTrim'),
		));

		$this->addElement('text','content', array(
				'label'		=> 'Content',
				'filters'	=> array('StripTags', 'StringTrim'),
		));

		$this->addElement('text','lang', array(
				'label'		=> 'Language',
				'filters'	=> array('StripTags', 'StringTrim'),
		));
	}

}