<?php
class Core_db_Table_Abstract extends Zend_Db_Table_Abstract{
	protected function _setupTableName(){
		parrent::_setupTableName();
		$prefix = "ns";
		$this->_name = $prefix."_".$this->_name;
	}
	
}