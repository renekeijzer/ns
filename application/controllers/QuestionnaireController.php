<?php
class QuestionnaireController extends Zend_Controller_Action{
	
	public function init(){
		
	}
	public function indexAction(){
		
	}
	public function viewAction(){
		$questionsTable = new Application_Model_DbTable_Questions();
		if($this->getRequest()->getPost() != null){
			$anwerTable = new Application_Model_DbTable_Answers();
			$commentsTable = new Application_Model_DbTable_Comments();
			$questions = $questionsTable->fetchAll($questionsTable->select()->where("questionnaire_id = ? ", $this->getParam("id")));
			foreach ($questions as $q){
				$rowComments = $anwerTable->createRow();
				$row = $anwerTable->createRow();
				$row->question_id = $q->question_id;
				$row->answer = ($this->getParam($q->question_id) == null? "":$this->getParam($q->question_id));
				$row->user_id = $this->getViewer()->user_id;
				$row->save();
				if($this->getParam("c".$q->question_id) != ""){
// 					die($row->anwer_id);
// 				$rowComments->answer_id = $row->anwer_id;
// 				$rowComments->comments = $this->getParam("c".$q->question_id);
// 				$rowComments->save();
				}
			}
			
			
			
			$this->redirect("/questionnaire/submit");
		}
		if($this->_getParam("id") != null){
			$this->view->id = $this->_getParam("id");
		
		$this->view->questions = $questionsTable->fetchAll($questionsTable->select()->where("questionnaire_id = ? ", $this->getParam("id")));
		}else{
			 $this->redirect("user/index");
		}
		
		
	}
	public function submitAction(){
		
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