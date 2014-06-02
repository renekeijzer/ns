<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {

    }
    
    public function loginAction(){
    	
    }
	
    public function logoutAction(){
    	
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
    	->setCredentialTreatment('(CONCAT(sha1(?), salt))');
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

