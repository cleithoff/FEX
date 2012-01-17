<?php
class ZFExt_Fex_Form_Navigation_Item extends Zend_Form {


	public function init() {

		$this->setMethod('post');

		$this->addElement('text','label', array(
				'label'		=> 'Label',
				'required'	=> true,
				'filters'	=> array('StripTags', 'StringTrim'),
		));

		$this->addElement('text','id', array(
				'label'		=> 'Id',
				'required'	=> true,
				'filters'	=> array('StripTags', 'StringTrim'),
		));

		$this->addElement('text','title', array(
				'label'		=> 'Title',
				'filters'	=> array('StripTags', 'StringTrim'),
		));

		$this->addElement('text','target', array(
				'label'		=> 'Target',
				'filters'	=> array('StripTags', 'StringTrim'),
		));

		$this->addElement('text','resource', array(
				'label'		=> 'Resource',
				'filters'	=> array('StripTags', 'StringTrim'),
		));

		$this->addElement('text','privilege', array(
				'label'		=> 'privilege',
				'filters'	=> array('StripTags', 'StringTrim'),
		));

		$this->addElement('text','visible', array(
				'label'		=> 'visible',
				'filters'	=> array('StripTags', 'StringTrim'),
		));

		$this->addElement('text','controller', array(
				'label'		=> 'Controller',
				'filters'	=> array('StripTags', 'StringTrim'),
		));

		$this->addElement('text','action', array(
				'label'		=> 'Action',
				'filters'	=> array('StripTags', 'StringTrim'),
		));

		$this->addElement('text','uri', array(
				'label'		=> 'URI',
				'filters'	=> array('StripTags', 'StringTrim'),
		));

		$this->addElement('text','route', array(
				'label'		=> 'Route',
				'filters'	=> array('StripTags', 'StringTrim'),
		));
	}

}