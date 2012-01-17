<?php

class ZFExt_Fex_Plugin_Cache extends Zend_Controller_Plugin_Abstract
{
	protected $_auth;
	protected $_acl;
	protected $_navigation;
	protected $_caching = null;
	protected $_cache;
	protected $_isFex = false;
	private $_debug = true;

	protected $_hash;

	public function __construct(Zend_Auth $_auth, Zend_Acl $_acl, Zend_Navigation $_navigation) {
		$this->_auth = $_auth;
		$this->_acl = $_acl;
		$this->_navigation = $_navigation;

		if (!empty($this->_auth)) {
			if (empty($this->_auth->getIdentity()->Role)) {
				$role = 'guest';
			} else {
				$role = $this->_auth->getIdentity()->Role;
			}
			$this->_isFex = $_acl->isAllowed($role, ZFExt_Fex::getInstance()->getDefaultResource());
		}

	}

	public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request) {
		parent::dispatchLoopStartup($request);

		if ($this->_isFex) {
			return;
		}

		$page = $this->_navigation->findOneByActive(true);
		if (!empty($page)) {
			$this->_caching = $page->get('caching', false);
			//var_dump($this->_caching);
		}/*
		if ($this->_debug) {
			$this->_caching = array(
					'cache' => 'myMemcached',
					'perget' => 'true',
					'persession' => 'true',
					'lifetime' => '1800',
			);
		}*/
		if (!empty($this->_caching)) {
			$cacheManager = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('cachemanager');
			$this->_cache = $cacheManager->getCache($this->_caching['cache']);

			if ($this->_cache instanceof Zend_Cache_Frontend_Page) {
				Zend_Controller_Front::getInstance()->setParam('disableOutputBuffering', true);
				$this->_cache->start();
			}
		}
	}

	public function dispatchLoopShutdown() {
		parent::dispatchLoopShutdown();
	}
}