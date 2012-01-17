<?php

class UserController extends Zend_Controller_Action
{

	public function init()
	{
		/* Initialize action controller here */
	}

	public function indexAction()
	{
		// action body
	}

	public function forgotpasswordAction()
	{
		// action body
	}

	public function registerAction()
	{
		// action body
		$this->view->success = false;
		$this->view->errors = array();
		$form = new Application_Form_Register();
		if ($this->getRequest()->isPost() && $this->getRequest()->getParam('doregister', false) == 'doregister') {
			if ($form->isValid($this->getRequest()->getPost())) {
				$user = new Application_Model_User(array('data' => $form->getValues()));
				$this->view->userId = $user->save(true);
				switch ($this->view->userId) {
					case -1 :
						$this->view->errors[] = 'RegisterErrorUsernameExists';
						break;
					case 0 :
					case null :
						$this->view->errors[] = 'RegisterErrorGeneralFault';
						break;
					default :
						$this->view->success = true;
						break;
				}
			}
			$form->populate($form->getValues());
		}
		$this->view->form = $form;
	}

	public function loginAction()
	{
		if (Zend_Auth::getInstance()->hasIdentity()) {
			return;
		}
		$this->view->errors = array();
		$form = new Application_Form_Login();
		if ($this->getRequest()->isPost() && $this->getRequest()->getParam('dologin', false) == 'dologin') {
			if ($form->isValid($this->getRequest()->getPost())) {

				$authAdapter = new Zend_Auth_Adapter_DbTable(Zend_Db_Table::getDefaultAdapter());
				$authAdapter->setTableName('user')
				->setIdentityColumn('Username')
				->setCredentialColumn('Password')
				->setCredentialTreatment("SHA1(CONCAT(?,Salt))") // Line of concern
				->setIdentity($form->getValue('Username'))
				->setCredential($form->getValue('Password'));
				$result = $authAdapter->authenticate();
				Zend_Session::regenerateId();

				if ($result->isValid()) {
					$auth = Zend_Auth::getInstance();
					$storage = $auth->getStorage();
					$storage->write($authAdapter->getResultRowObject(null,'Password'));
					$this->_redirect();
					return;
				} else {
					$this->view->errors[] = 'LoginError';
				}
			}
			$form->populate($form->getValues());
		}
		$this->view->form = $form;
	}

	public function logoutAction()
	{
		if (Zend_Auth::getInstance()->hasIdentity()) {
			$auth = Zend_Auth::getInstance()->clearIdentity();
		}
		$this->_redirect('/');
	}


}









