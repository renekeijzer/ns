<?php
class AdminController extends Zend_Controller_Action{
	public function init(){
		if(!$this->getViewer()){
    	    $action = $this->getRequest()->getActionName();
			if($action!='login'){
				$this->redirect('admin/login');
			}
		}
		$this->_helper->layout()->setLayout('admin-layout');
	}
	
	public function loginAction(){
	    if($this->getViewer())
	        $this->redirect('admin/index');
	    $this->_helper->layout()->setLayout('admin-login');
	    if($this->getRequest()->isPost()){
	        $vals['username'] = $this->getRequest()->getPost('username');
	        $vals['password'] = $this->getRequest()->getPost('password');
	        if($this->_process($vals)){
	           $this->redirect('admin/index');
	        }
	    }
	}
	
	public function indexAction(){
		
	}
	
	public function logoutAction(){
	    Zend_Auth::getInstance()->clearIdentity();
	    $this->_helper->redirector('login');
	}
	

	public function exportAction(){
		
	}
	public function importAction(){
		
	}
	/* User CRUD*/
	public function manageUsersAction(){
	    $usersTable = new Application_Model_DbTable_User();
	    $this->view->viewer = $viewer = $this->getViewer();
	    if($viewer->role=='super admin')
	        $users = $usersTable->fetchAll();
	    else
	        $users = $usersTable->fetchAll($usersTable->select()->where('role="observer"'));
	    $this->view->users = $users;
	}
	public function createUserAction(){
	    $viewer = $this->getViewer();
	    $usersTable = new Application_Model_DbTable_User();
	    $user = $usersTable->createRow();
	    if($this->getRequest()->isPost()){
	        $username = $this->getParam('username');
	        $email = $this->getParam('email');
	        $newPass = $this->getParam('password');
	        $number = $this->getParam('number');
	        $role = $this->getParam('role');
	        $user->username = $username;
	        $user->email = $email;
            $user->salt = $salt = uniqid(mt_rand(), true);
	        if(strlen($newPass)>0){
	            $user->password = sha1($newPass.$salt);
	        } else {
	            $user->password = $this->randomPassword();
	        }
	        $user->telnr = $number;
	        $user->role = $role;
	        $user->save();
	        $mail = new Zend_Mail();
            $mail->setBodyText("Beste Gebruiker, Uw account is aangemaakt. U kunt inloggen met de gebruikersnaam: $username en met het wachtwoord: $newPass");
            $mail->setBodyHtml("Beste Gebruiker, <br/><br/>Uw account is aangemaakt.<br/> U kunt inloggen met de gebruikersnaam: $username <br/>en met het wachtwoord: $newPass");
            $mail->setFrom('noreply@ns-app.com', 'NS Questionnaire');
            $mail->addTo($email, $username);
            $mail->setSubject('Uw account is aangemaakt.');
            $mail->send();
	        $this->redirect('admin/manage-users');
	    }
	}
	
	public  function updateUserAction(){
	    $id = $this->getParam('id');
	    $viewer = $this->getViewer();
	    $usersTable = new Application_Model_DbTable_User();
	    $this->view->user = $user = $usersTable->find($id)->current();
	    if(!$user)
	        $this->redirect('admin/manage-users');
	    if($viewer->role!='super admin' && $user->role=='super admin')
	        $this->redirect('admin/manage-users');
	    if($viewer->role!='super admin' && $user->role=='admin')
	        $this->redirect('admin/manage-users');
	    if($this->getRequest()->isPost()){
	        $username = $this->getParam('username');
	        $email = $this->getParam('email');
	        $newPass = $this->getParam('password');
	        $number = $this->getParam('number');
	        $role = $this->getParam('role');
	        $user->username = $username;
	        $user->email = $email;
	        if(strlen($newPass)>0){
	           $user->salt = $salt = uniqid(mt_rand(), true);
	           $user->password = sha1($newPass.$salt);
	        }
	        $user->telnr = $number;
	        $user->role = $role;
	        $user->save();
	        $this->redirect('admin/manage-users');
	    }
	}
	public function deleteUserAction(){
	    $this->_helper->layout()->disableLayout();
	    $this->_helper->viewRenderer->setRender(false);
	    
	    $viewer = $this->getViewer();
	    if($viewer->role!='super admin')
	        $this->redirect('admin/manage-users');
	    
	    $id = $this->getParam('id');
	    $usersTable = new Application_Model_DbTable_User();
	    $user = $usersTable->find($id)->current();
	    if(!$user)
	        $this->redirect('admin/manage-users');
	    $user->delete();
	    $this->redirect('admin/manage-users');
	}
	
	public function viewUserAction(){
		
	}
	
	public function toggleUserAction(){
	    $this->_helper->layout()->disableLayout();
	    $this->_helper->viewRenderer->setRender(false);
	    $id = $this->getParam('id');
	    $usersTable = new Application_Model_DbTable_User();
	    $user = $usersTable->find($id)->current();
	    if(!$user)
	        $this->redirect('admin/manage-users');
	    $user->enabled = 1- $user->enabled;
	    $user->save();
	    $this->redirect('admin/manage-users');
	}
	
	/* questionnaire CRUD */
	
	public function manageQuestionnairesAction(){
	    $qTable = new Application_Model_DbTable_Questionnaire();
	    $this->view->forms = $forms = $qTable->fetchAll();
	}
	
	public function updateQuestionnaireAction(){
		$id = $this->getParam('id');
	    $qTable = new Application_Model_DbTable_Questionnaire();
		$questionnaire = $qTable->find($id)->current();
		if(!$questionnaire)
		    $this->redirect('admin/manage-questionnaires');
	    $qsTable = new Application_Model_DbTable_Questions();
	    $questions = $qsTable->fetchAll($qsTable->select()->where('questionnaire_id=?',$questionnaire->questionnaire_id));
		$this->view->questionnaire = $questionnaire;
		$this->view->questions = $questions;
	}
	
	public function createQuestionnaireAction(){
	    $viewer = $this->getViewer();
	   if($this->getRequest()->isPost()){
	       $title = $this->getParam('title');
	       $expire_date = $this->getParam('expire_date');
           $once=0;	       
	       if($this->getParam('once')=='on') $once = 1;
	       $created_by = $viewer->user_id;
	       $qTable = new Application_Model_DbTable_Questionnaire();
	       $q = $qTable->createRow();
	       $q->title = $title;
	       $q->expire_date = $expire_date;
	       $q->once = $once;
	       $q->created_by = $created_by;
	       $q->save();
	       $this->redirect('admin/update-questionnaire/id/'.$q->questionnaire_id);
	   }	
	}
	
	public function deleteQuestionnaireAction(){
		
	}
	
	public function viewQuestionnaireAction(){
		$id = $this->getParam('id');
	    $qTable = new Application_Model_DbTable_Questionnaire();
		$questionnaire = $qTable->find($id)->current();
		if(!$questionnaire)
		    $this->redirect('admin/manage-questionnaires');
	    $qsTable = new Application_Model_DbTable_Questions();
	    $questions = $qsTable->fetchAll($qsTable->select()->where('questionnaire_id=?',$questionnaire->questionnaire_id));
		$this->view->questionnaire = $questionnaire;
		$this->view->questions = $questions;
	}
	
	public function resultsQuestionnaireAction(){
		$id = $this->getParam('id');
	    $qTable = new Application_Model_DbTable_Questionnaire();
		$questionnaire = $qTable->find($id)->current();
		if(!$questionnaire)
		    $this->redirect('admin/manage-questionnaires');
	    $qsTable = new Application_Model_DbTable_Questions();
	    $questions = $qsTable->fetchAll($qsTable->select()->where('questionnaire_id=?',$questionnaire->questionnaire_id));
	    $answersTable = new Application_Model_DbTable_Answers();
	    $this->view->questionnaire = $questionnaire;
		$this->view->questions = $questions;
	}
	
	public function toggleQuestionnaireAction(){
	    $this->_helper->layout()->disableLayout();
	    $this->_helper->viewRenderer->setRender(false);
		$id = $this->getParam('id');
	    $qTable = new Application_Model_DbTable_Questionnaire();
		$questionnaire = $qTable->find($id)->current();
		if(!$questionnaire)
		    $this->redirect('admin/manage-questionnaires');
		$questionnaire->started = 1- $questionnaire->started;
		$questionnaire->save();
		$this->redirect('admin/manage-questionnaires');
	}
	
	public function createQuestionAction(){
	   $id = $this->getParam('id');
	    $qTable = new Application_Model_DbTable_Questionnaire();
	    $qsTable = new Application_Model_DbTable_Questions();
		$questionnaire = $qTable->find($id)->current();
		if(!$questionnaire)
		    $this->redirect('admin/manage-questionnaires');
		if($this->getRequest()->isPost()){
		  $question = $qsTable->createRow();
		  $question->questionnaire_id = $questionnaire->questionnaire_id;
		  $question->type=$this->getParam('type');
		  $question->label=$this->getParam('label');
		  if($this->getParam('required')=='on')
		      $question->required = 1;
		  if($this->getParam('comments')=='on')
		      $question->comments = 1;
		  if(count($this->getParam('params'))>0)
		      $question->params = json_encode($this->getParam('params'));
		  if(count($this->getParam('options'))>0)
		      $question->options = json_encode($this->getParam('options'));
		  $question->save();
		    $this->redirect('admin/update-questionnaire/id/'.$questionnaire->questionnaire_id);
		}
	       
	}
	
	public function deleteQuestionAction(){
	    $this->_helper->layout()->disableLayout();
	    $this->_helper->viewRenderer->setRender(false);
		$id = $this->getParam('qid');
		$question_id = $this->getParam('id');
	    $qTable = new Application_Model_DbTable_Questionnaire();
	    $qsTable = new Application_Model_DbTable_Questions();
		$questionnaire = $qTable->find($id)->current();
		$question = $qsTable->find($question_id)->current();
		if(!$questionnaire || !$question)
		    $this->redirect('admin/manage-questionnaires');
		$question->delete();
		$this->redirect('admin/update-questionnaire/id/'.$questionnaire->questionnaire_id);
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
	
	protected function randomPassword() {
	    $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
	    $pass = array(); //remember to declare $pass as an array
	    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
	    for ($i = 0; $i < 8; $i++) {
	        $n = rand(0, $alphaLength);
	        $pass[] = $alphabet[$n];
	    }
	    return implode($pass); //turn the array into a string
	}
	
	
}