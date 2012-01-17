<?php

class ZFExt_Fex_Controller_Action_Helper_Navigation extends Zend_Controller_Action_Helper_Abstract {

	protected function getId($_id) {
		$_id = explode('-', $this->getRequest()->getParam($_id, null));
		array_shift($_id);
		return implode('-', $_id);
	}

	protected function cleanupNavigation(&$items) {
		if (is_array($items)) {
			foreach ($items as $key => $item) {
				if (empty($item)) {
					switch($key) {
						case 'visible' :
						case 'lifetime' :
						case 'perget' :
						case 'persession' :
							break;
						default :
							unset($items[$key]);
					}

				} else {
					$items[$key] = $this->cleanupNavigation($item);
					if (intval($key) === $key && isset($items[$key]['id'])) {
						$items[strtolower(str_replace(array('-','_'),array('',''), $items[$key]['id']))] = unserialize(serialize($items[$key]));
						unset($items[$key]);
						continue;
					}
					switch($key) {
						case 'type' :
						case 'reset_params' :
							unset($items[$key]);
							break;
					}
				}
			}
		}
		return $items;
	}

	protected function makeAttributes($attrs) {
		$ret = array();
		foreach ($attrs as $key => $value) {
			$ret[] = $key . '="' . (is_bool($value) ? ($value === true ? 'true' : 'false') : $value) . '"';
		}
		return implode(' ', $ret);
	}

	protected function getMessage(array $array = null, array $attrs = null) {
		$xml = new SimpleXMLElement('<message ' . $this->makeAttributes($attrs) . '></message>');
		return $this->array2xml($array, $xml);
	}

	protected function array2xml($array, $xml = false){
		foreach($array as $key => $value){
			if(is_array($value)){
				$this->array2xml($value, $xml->addChild($key));
			}else{
				$xml->addChild($key, $value);
			}
		}
		return $xml->asXML();
	}

	public function load($itemId = null, $translate = false) {
		$navigation = Zend_Registry::get('Zend_Navigation');
		if (empty($itemId)) {
			$itemId = $this->getId('itemId');
		}

		if (empty($itemId)) {
			return;
		}

		$item = $navigation->findById($itemId);

		$item = array(
				'navigation' => array(
				'label' => $item->getLabel(),
				'id' => $item->getId(),
				'title' => $item->getTitle(),
				'target' => $item->getTarget(),
				'resource' => $item->getResource(),
				'privilege' => $item->getPrivilege(),
				'visible' => $item->getVisible(),
				'controller' => $item instanceof Zend_Navigation_Page_Mvc ? $item->getController() : null,
				'action' => $item instanceof Zend_Navigation_Page_Mvc ? $item->getAction() : null,
				'uri' => $item instanceof Zend_Navigation_Page_Uri ? $item->getUri() : null,
				'route' => $item->getRoute(),
		));

		if ($translate) {
			$translate = Zend_Registry::get('Zend_Translate');
			$item['navigation']['label'] = $translate->_($item['navigation']['label']);
		}

		return $this->getMessage($item, array('success' => true));
	}

	public function save() {
		$navigation = Zend_Registry::get('Zend_Navigation');
		$saveId = $this->getId('saveId');
		$parentId = $this->getId('parentId');

		/*if (empty($saveId)) {
			return;
		}*/

		$form = new ZFExt_Fex_Form_Navigation_Item();
		if (!($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()))) {
			return;
		}

		$data = $form->getValues();

		foreach ($data as $key => $date) {
			switch($key) {
				case 'visible' :
					if (empty($data[$key])) {
						$data[$key] = '0';
					}
					break;
				default :
					if (empty($data[$key])) {
						unset($data[$key]);
					}
					break;
			}
		}

		if (empty($data['action']) && empty($data['controller'])) {
			$page = new Zend_Navigation_Page_Uri($data);
		} else {
			$page = new Zend_Navigation_Page_Mvc($data);
		}

		$parent = $navigation->findById($parentId);
		$parent->rewind();
		$save = $navigation->findById($saveId);
		if (!empty($save)) {
			$page->setOrder($save->getOrder());
			$parent->removePage($save);
		}
		$parent->addPage($page);
		$parent->rewind();

		Zend_Registry::set('Zend_Navigation', $navigation);

		$array = array('production' => $navigation->toArray());
		$this->cleanupNavigation($array);
		$config = new Zend_Config($array);

		$writer = new Zend_Config_Writer_Xml();
		$writer->write(APPLICATION_PATH . '/configs/navigation.xml', $config);
		return $this->load($page->getId(), true);
	}

	public function move() {
		$navigation = Zend_Registry::get('Zend_Navigation');
		$prevId = $this->getId('prevId');
		$nextId = $this->getId('nextId');
		$parentId = $this->getId('parentId');
		$selfId = $this->getId('selfId');

		if (empty($selfId)) {
			return;
		}

		$self = $navigation->findById($selfId);
		$parent = $navigation->findById($parentId);
		$parent->removePage($self);
		$parent->rewind();
		$pages = $parent->getPages();
		$i = 0;
		foreach ($pages as $page) {
			$i++;
			$page->setOrder($i * 2);
		}
		$parent->addPage($self);

		if (!empty($prevId)) {
			$prev = $navigation->findById($prevId);
			$self->setOrder($prev->getOrder() + 1);
		} elseif (!empty($nextId)) {
			$next = $navigation->findById($nextId);
			$self->setOrder($next->getOrder() - 1);
		}

		$array = array('production' => $navigation->toArray());
		$this->cleanupNavigation($array);
		$config = new Zend_Config($array);

		$writer = new Zend_Config_Writer_Xml();
		$writer->write(APPLICATION_PATH . '/configs/navigation.xml', $config);
	}

	public function delete() {
		$navigation = Zend_Registry::get('Zend_Navigation');
		$itemId = $this->getId('itemId');
		$parentId = $this->getId('parentId');

		if (empty($itemId)) {
			return;
		}

		$item = $navigation->findById($itemId);
		$parent = $navigation->findById($parentId);
		$parent->removePage($item);
		$parent->rewind();

		$array = array('production' => $navigation->toArray());
		$this->cleanupNavigation($array);
		$config = new Zend_Config($array);

		$writer = new Zend_Config_Writer_Xml();
		$writer->write(APPLICATION_PATH . '/configs/navigation.xml', $config);
	}

	public function loadcaching($itemId = null) {
		$navigation = Zend_Registry::get('Zend_Navigation');
		if (empty($itemId)) {
			$itemId = $this->getId('itemId');
		}

		if (empty($itemId)) {
			return;
		}

		$item = $navigation->findById($itemId);

		$caching = $item->get('caching');

		if (is_array($caching)) {
			$item = array(
					'caching' => array(
							'cache' => empty($caching['cache']) ? '' : $caching['cache'],
					));
		} else {
			$item = array(
					'caching' => array(
							'cache' => '',
					));
		}

		return $this->getMessage($item, array('success' => true));
	}

	public function savecaching() {
		$navigation = Zend_Registry::get('Zend_Navigation');
		$saveId = $this->getId('saveId');
		$parentId = $this->getId('parentId');

		if (empty($saveId)) {
			return;
		}

		$form = new ZFExt_Fex_Form_Navigation_Caching();
		if (!($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()))) {
			return;
		}

		$data = $form->getValues();

		foreach ($data as $key => $date) {
			switch($key) {
				case 'cache':
					break;
				default :
					if (empty($data[$key])) {
					unset($data[$key]);
				}
				break;
			}
		}

		$save = $navigation->findById($saveId);
		$save->set('caching', $data);

		Zend_Registry::set('Zend_Navigation', $navigation);

		$array = array('production' => $navigation->toArray());
		$this->cleanupNavigation($array);
		$config = new Zend_Config($array);

		$writer = new Zend_Config_Writer_Xml();
		$writer->write(APPLICATION_PATH . '/configs/navigation.xml', $config);
		return $this->loadcaching($save->getId());
	}

	public function getcachemanager() {
		$bootstrap = Zend_Controller_Front::getInstance()->getParam('bootstrap');
		$options = $bootstrap->getOptions();
		return $options['resources']['cachemanager'];
	}

	public function loadmetadata() {
		$navigation = Zend_Registry::get('Zend_Navigation');
		$itemId = $this->getId('itemId');

		if (empty($itemId)) {
			return;
		}

		$item = $navigation->findById($itemId);

		$results = $item->get('metadata');

		if (empty($results)) {
			$results = array();
		}

		return $results;
	}

	public function savemetadata() {
		$navigation = Zend_Registry::get('Zend_Navigation');
		$saveId = $this->getId('saveId');
		$parentId = $this->getId('parentId');

		if (empty($saveId)) {
			return;
		}

		$form = new ZFExt_Fex_Form_Navigation_Metadata();
		if (!($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()))) {
			return;
		}
		$data = $form->getValues();

		foreach ($data as $key => $date) {
			switch($key) {
				case 'hash':
				case 'attribute':
				case 'metakey':
				case 'content':
				case 'lang':
					break;
				default :
					if (empty($data[$key])) {
					unset($data[$key]);
				}
				break;
			}
		}
		$save = $navigation->findById($saveId);
		$metadata = $save->get('metadata');
		if (!empty($metadata[$data['hash']]) && $data['hash'] != 'meta' . md5($data['metakey'] . $data['lang'])) {
			unset($metadata[$data['hash']]);
			$data['hash'] = 'meta' . md5($data['metakey'] . $data['lang']);
		}
		$metadata[$data['hash']] = $data;


		$save->set('metadata', $metadata);

		Zend_Registry::set('Zend_Navigation', $navigation);

		$array = array('production' => $navigation->toArray());
		$this->cleanupNavigation($array);
		$config = new Zend_Config($array);

		$writer = new Zend_Config_Writer_Xml();
		$writer->write(APPLICATION_PATH . '/configs/navigation.xml', $config);
		die();
		return $this->loadmetadata();
	}

}