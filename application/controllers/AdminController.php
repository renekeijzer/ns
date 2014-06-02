<?php
class AdminController extends Zend_Controller_Action{
	public function init(){
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
	
	
	
	
}