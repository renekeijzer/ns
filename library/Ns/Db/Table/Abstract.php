<?php
class Ns_Db_Table_Abstract extends Zend_Db_Table_Abstract{
	protected function _setupTableName(){
		parent::_setupTableName();
		$prefix = "ns";
		$this->_name = $prefix."_".$this->_name;
	}
	
}