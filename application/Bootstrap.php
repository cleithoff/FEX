<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

	protected function _initAutoload() {
		ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . APPLICATION_PATH);
		require_once 'P.php';
		require_once 'T.php';
		require_once 'I.php';
		require_once 'D.php';
		require_once 'U.php';

		$loader = Zend_Loader_Autoloader::getInstance();
		$loader->registerNamespace('Plugin_');
		$loader->registerNamespace('ZFExt_');
	}

	protected function _initDoctype()
	{
		$doctypeHelper = new Zend_View_Helper_Doctype();
		$doctypeHelper->doctype('XHTML1_STRICT');
	}

	protected function _initAuth() {
		$auth = Zend_Auth::getInstance();
		$acl = new Plugin_Auth_Acl();
		P::init($auth, $acl);
		Zend_Registry::set('Zend_Acl', $acl);
		$this->bootstrap('frontController'); // have to be called
		$this->getResource('frontController')->registerPlugin(new Plugin_Auth_AccessControl($auth, $acl))->setParam('auth', $auth);
	}

	protected function _initSession()
	{
		require_once 'S.php';
	}
/*
	protected function _initConfig()
	{
		$config = new Zend_Config($this->getOptions());
		Zend_Registry::set('Zend_Config', $config);
		return $config;
	}
*/
	protected function _initNavigation()
	{
		$this->bootstrap('layout');
		$this->bootstrap('auth');
		$config = new Zend_Config_Xml(APPLICATION_PATH.'/configs/navigation.xml','production');
		$config->setExtend('production');
		$navigation = new Zend_Navigation($config);
		Zend_Registry::set('Zend_Navigation', $navigation);

	}

	protected function _initLocale() {
		// overide Locale with userdefined
		if (!empty($_REQUEST['locale_id'])) {
			switch ($_REQUEST['locale_id']) {
				case 'en' :
				case 'de' :
					S::set('locale',$_REQUEST['locale_id']);
					break;
				default:
					$_REQUEST['locale_id'] = 'en';
			}
		}

		// Locale init
		$locale_id = S::get('locale');
		if (empty($locale_id)) {
			$locale = new Zend_Locale('auto'); // Defaults to 'Browser' as preferred detection method
			$locale_id = $locale->getLanguage();
			switch ($locale_id) {
				case 'de':
					break;
				default :
					$locale_id = 'en';
			}
			S::set('locale', $locale_id);
		}
		Zend_Locale::setDefault($locale_id);
		$locale = new Zend_Locale($locale_id);
		Zend_Registry::set('Zend_Locale', $locale);

		//Making translation available through the entire site.
		$translate = new Zend_Translate (array(
				'adapter'	=>	'array',
				'content'	=>	APPLICATION_PATH.'/language',
				'scan'		=> 	Zend_Translate::LOCALE_DIRECTORY,
				'locale'	=> 	key(Zend_Locale::getDefault()), //setting up default locale
		));
		Zend_Registry::set('Zend_Translate', $translate);
		Zend_Validate_Abstract::setDefaultTranslator($translate);
		Zend_Form::setDefaultTranslator($translate);

		/*
		$jsDir=APPLICATION_PATH.'/../public/js/';
		$filename='jquery.ui.datepicker-{LOCALE}.js';
		$fullLocaleFilename=str_replace('{LOCALE}',$locale,$filename);
		if (!file_exists($jsDir.$fullLocaleFilename)) {
			$l = new Zend_Locale($l->getLanguage());
			$fullLocaleFilename=str_replace('{LOCALE}',$locale->getLanguage(),$filename);
			if (!file_exists($jsDir.$fullLocaleFilename)) {
				$l = new Zend_Locale($locale_id);
				$fullLocaleFilename = str_replace('{LOCALE}',$locale_id,$filename);
			}
		}
		*/
	}

	public function _initFex() {
		$this->bootstrap('layout');
		$view = Zend_Layout::getMvcInstance()->getView();
		$view->addHelperPath("ZFExt/Fex/View/Helper", "ZFExt_Fex_View_Helper");

	}

	public function run()
	{
		$view = Zend_Layout::getMvcInstance()->getView();
		$view->headLink()->appendStylesheet('/css/reset.css');
		$view->headLink()->appendStylesheet('/css/style.css');
		$view->headMeta()->appendHttpEquiv('Content-Type', 'text/html; charset=utf-8');

		$view->addHelperPath("ZendX/JQuery/View/Helper", "ZendX_JQuery_View_Helper");

		$viewRenderer = new Zend_Controller_Action_Helper_ViewRenderer();
		$viewRenderer->setView($view);
		Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);

		$jquery = $view->jQuery();
		$jquery->enable();
		$jquery->uiEnable();
		$jquery->setLocalPath('/js/jquery-1.7.1.min.js');
		$jquery->setUiLocalPath('/js/jquery-ui-1.8.16.custom.min.js');
		$jquery->addStylesheet('/css/themes/smoothness/jquery-ui-1.8.16.custom.css');

		$view = Zend_Layout::getMvcInstance()->getView();
		$view->navigation()
		->setAcl(Zend_Registry::get('Zend_Acl'))
		->setRole(Zend_Auth::getInstance()->hasIdentity() ? Zend_Auth::getInstance()->getIdentity()->Role : 'guest');
		ZFExt_Fex::getInstance()->initNavigation();
		$this->getResource('frontController')->registerPlugin(new ZFExt_Fex_Plugin_Cache(Zend_Auth::getInstance(), Zend_Registry::get('Zend_Acl'), Zend_Registry::get('Zend_Navigation')));
		parent::run();
	}

}