<?php
class UserController extends Zend_Controller_Action{
	public function init(){
		if(!$this->getViewer()){
			$action = $this->getRequest()->getActionName();
			if($action!='login'){
				$this->_helper->redirector("index/login");
			}
		}
	}
	public function indexAction(){
		$userTable = new Application_Model_DbTable_User();
		$user = $userTable->find($this->getViewer()->user_id)->current();
		$questionnaireTable = new Application_Model_DbTable_Questionnaire();
		if($user->telnr == "" || $user->telnr == null || $user->username =="" || $user->username == null || $user->email ==""||$user->email == null){
			$this->redirect("user/update");
		}
		$dateNow = date("%m%d/%Y H:i:s");
		$select = $questionnaireTable->select()->where("expire_date >= ?",  $dateNow);
		/*
		 * @todo when login is done, only show the right questionnaires
		 */
		$questionnairs = $questionnaireTable->fetchAll($select);
		
		$this->view->questionnairs = $questionnairs;
	}
	public function updateAction(){
		$userTable = new Application_Model_DbTable_User();
		$user = $userTable->find($this->getViewer()->user_id)->current();
		if($this->getRequest()->getPost() != null){
			$user->telnr = $this->getParam("tel");
			$user->username = $this->getParam("username");
			$user->email = $this->getParam("email");
			
			if(($this->getParam("pw1") == $this->getParam("pw2")) && strlen($this->getParam("pw1")) > 0){
			
			$user->salt = $salt = uniqid(mt_rand(), true);
			$user->password = sha1($this->getParam("pw1").$salt);
			}
			
			$user->save();
			
		}
		
		
		if($user->telnr == "" || $user->telnr == null || $user->username =="" || $user->username == null || $user->email ==""||$user->email == null){
			$this->view->completed = false;
		}
		else {
			$this->view->completed = true;
		}
		$this->view->user = $user;
	}
	public function viewAction(){
		$userTable = new Application_Model_DbTable_User();
		$user = $userTable->find($this->getViewer()->user_id)->current();
		$this->view->user = $user;
	}	
	

	protected function _process($values)
	{
		// Get our authentication adapter and check credentials
		$adapter = $this->_getAuthAdapter();
		$adapter->setIdentity($values['username']);
		$adapter->setCredential($values['password']);
	
		$auth = Zend_Auth::getInstance();
		$result = $auth->authenticate($adapter);
		if ($result->isValid()) {
			$user = $adapter->getResultRowObject();
			$auth->getStorage()->write($user);
			return true;
		}
		return false;
	}
	
	protected function _getAuthAdapter() {
		$table = new Application_Model_DbTable_User();
		$dbAdapter = $table->getAdapter();
		$authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
		 
		$authAdapter
		->setTableName($table->info('name'))
		->setIdentityColumn('username')
		->setCredentialColumn('password')
		->setCredentialTreatment('(sha1(concat(?, salt)))');
		return $authAdapter;
	}
	
	protected function getViewer(){
		$auth = Zend_Auth::getInstance();
		if ($auth->hasIdentity()) {
			$table = new Application_Model_DbTable_User();
			$user = $table->find($auth->getIdentity()->user_id)->current();
			if($user) return $user;
			else return false;
		} else
			return false;
	}
}