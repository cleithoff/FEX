<?php

class FexController extends Zend_Controller_Action
{

	public function init()
	{
		/* Initialize action controller here */
		$this->_helper->layout()->disableLayout();
		$contextSwitch = $this->_helper->getHelper('AjaxContext');
		$contextSwitch->addActionContext('navigation', 'xml')
		->addActionContext('navigation', 'json')
		->addActionContext('translate', 'json')
		->initContext();
	}

	public function indexAction()
	{
		// action body

	}

	public function imageAction()
	{
		// action body
		if ($this->getRequest()->isPost()) {
			$image = $this->getRequest()->getParam('image', false);
			if (!empty($image)) {
				$width = intval($this->getRequest()->getParam('width', 0));
				$height = intval($this->getRequest()->getParam('height', 0));
				$crop['width'] = intval($this->getRequest()->getParam('cropWidth', $width));
				$crop['height'] = intval($this->getRequest()->getParam('cropHeight', $height));
				$crop['left'] = intval($this->getRequest()->getParam('cropLeft', 0));
				$crop['top'] = intval($this->getRequest()->getParam('cropTop', 0));
				ZFExt_Fex::thumbnail($image, $width, $height, !empty($crop), $crop);
			}
		}

	}

	public function translateAction()
	{
		Zend_Controller_Action_HelperBroker::addPath("ZFExt/Fex/Controller/Action/Helper", "ZFExt_Fex_Controller_Action_Helper");
		$this->_helper->layout()->disableLayout();
		$cmd = $this->getRequest()->getParam('cmd', false);
		switch (strtolower($cmd)) {
			case 'getlocales' :
				$this->view->locales = $this->_helper->getHelper('Translate')->getlocales();
				break;
			case 'save' :
				$locale = $this->getRequest()->getParam('locale', false);
				if (empty($locale)) {
					$locale = key(Zend_Locale::getDefault());
				}
				$filename = APPLICATION_PATH . '/language/' . $locale . '/' . $locale . '.php';
				$locale = require($filename);

				$content = $this->getRequest()->getParam('content', false);
				if (is_array($content) || is_object($content)) {
					foreach ($content as $value) {
						$keys = array_keys($locale, $value['originalContent']);
						if (!empty($keys) && is_array($keys)) {
							foreach ($keys as $key) {
								$locale[$key] = $value['content'];
							}
						}
					}
				} else {
					$value['content'] = $this->getRequest()->getParam('content', false);
					$value['key'] = $this->getRequest()->getParam('key', false);
					$keys = array($value['key']);
					if (!empty($keys) && is_array($keys)) {
						foreach ($keys as $key) {
							$locale[$key] = $value['content'];
						}
					}
				}
				if (!empty($locale)) {
					$content = array();
					foreach ($locale as $key => $value) {
						$content[] = "\t\"" . $key . "\" => \"" . str_replace('"', '\\"', $value) . "\",\n";
					}
					$content = "<?php\n\nreturn array(\n" . implode('', $content) . "\n);\n";
					$fp = fopen($filename, 'w+') or die("I could not open $filename.");
					fwrite($fp, $content);
					fclose($fp);
				}
				break;
			case 'listing' :
				$locale = $this->getRequest()->getParam('locale', false);
				$filename = APPLICATION_PATH . '/language/' . key(Zend_Locale::getDefault()) . '/' . key(Zend_Locale::getDefault()) . '.php';
				$locale = require($filename);
				$recs = array();
				foreach ($locale as $key => $value) {
					$rec = array('key' => $key, 'value' => $value);
					$recs[] = $rec;
				}
				echo '({"total":"'.count($recs).'","results":'. json_encode($recs) .'})' ;
				break;
		}
	}

	public function navigationAction()
	{
		Zend_Controller_Action_HelperBroker::addPath("ZFExt/Fex/Controller/Action/Helper", "ZFExt_Fex_Controller_Action_Helper");
		$cmd = $this->getRequest()->getParam('cmd', false);
		switch (strtolower($cmd)) {
			case 'move' :
				$this->_helper->viewRenderer->setNoRender(true);
				$this->_helper->getHelper('Navigation')->move();
				break;
			case 'load' :
				$this->view->response = $this->_helper->getHelper('Navigation')->load();
				break;
			case 'save' :
				$this->view->response = $this->_helper->getHelper('Navigation')->save();
				break;
			case 'delete' :
				$this->_helper->viewRenderer->setNoRender(true);
				$this->_helper->getHelper('Navigation')->delete();
				break;
			case 'loadcaching' :
				$this->view->response = $this->_helper->getHelper('Navigation')->loadcaching();
				break;
			case 'savecaching' :
				$this->view->response = $this->_helper->getHelper('Navigation')->savecaching();
				break;
			case 'getcachemanager' :
				$this->view->cachemanager = $this->_helper->getHelper('Navigation')->getcachemanager();
				break;
			case 'loadmetadata' :
				$results = $this->_helper->getHelper('Navigation')->loadmetadata();
				$recs = array();
				foreach ($results as $key => $value) {
					$recs[] = $value;
				}
				echo '({"total":"'.count($recs).'","results":'. json_encode($recs) .'})' ;
				die();
				break;
			case 'savemetadata' :
				$this->_helper->getHelper('Navigation')->savemetadata();
				die();
				break;
		}

	}


}







