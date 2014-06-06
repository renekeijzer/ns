<?php
class QuestionnaireController extends Zend_Controller_Action{
	
	public function init(){
		
	}
	public function indexAction(){
		
	}
	public function viewAction(){
		if($this->_getParam("id") != null){
		$questionsTable = new Application_Model_DbTable_Questions();
		$this->view->questions = $questionsTable->fetchAll($questionsTable->select()->where("questionnaire_id = ? ", $this->getParam("id")));
		}else{
			 $this->redirect("user/index");
		}
		
		
	}
	public function submitAction(){
		var_dump($this->getRequest()->getPost());
	}
}