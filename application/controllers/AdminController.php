<?php
class AdminController extends Zend_Controller_Action{
	public function init(){
// 		if(!$this->getViewer()){
		if(1){
	    $action = $this->getRequest()->getActionName();
			if($action!='login'){
				$this->redirect('admin/login');
			}
		}
	        
		$this->_helper->layout()->setLayout('admin-layout');
	}
	
	public function loginAction(){
	    $this->_helper->layout()->setLayout('admin-login');
	}
	
	public function indexAction(){
		
	}
	

	public function exportAction(){
		
	}
	public function importAction(){
		
	}
	/* User CRUD*/
	
	public function createUserAction(){
		
	}
	public  function updateUserAction(){
		
	}
	public function deleteUserAction(){
		
	}
	public function viewUserAction(){
		
	}
	
	/* questionnaire CRUD */
	
	public function updateQuestionnaireAction(){
		
	}
	
	public function createQuestionnaireAction(){
		
	}
	
	public function deleteQuestionnaireAction(){
		
	}
	
	public function viewQuestionnaireAction(){
		
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