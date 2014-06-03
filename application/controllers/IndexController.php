<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
       if(!$this->getViewer()){
           $action = $this->getRequest()->getActionName();
           if($action!='login'){
                $this->redirect("index/login");
          }
       }
    }

    public function indexAction()
    {
    	$this->redirect("user/index");
    }
    
    public function loginAction(){
    	$user = $this->getViewer();
    	if ($user) $this->_helper->redirector('index'); //User has no business here.
    	if($this->getRequest()->isPost()){
    			if ($this->_process(array("username" => $this->getRequest()->getPost('username'),
    										"password"=>$this->getRequest()->getPost('password'),
    			))) {
    				$this->_helper->redirector('index');
    			} else {
    				echo "Het password/username is niet valide, probeer het nogmaals";
    			}
    	}
    }
	
    public function logoutAction(){
        Zend_Auth::getInstance()->clearIdentity();
        $this->_helper->redirector('login');
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

