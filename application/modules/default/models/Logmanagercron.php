<?php

class Default_Model_Logmanagercron extends Zend_Db_Table_Abstract{
	protected $_name = 'main_logmanagercron';
	
 public function InsertLogManagerCron($menuId,$actionflag,$jsonlogarr,$userid,$keyflag,$date)
	{
	    $date= gmdate("Y-m-d H:i:s");
		$db = Zend_Db_Table::getDefaultAdapter();
		
				
		$data =  array('menuId' => $menuId,
		'user_action' => $actionflag, 
		'log_details' => $jsonlogarr,
		'last_modifiedby' => $userid,
		'last_modifieddate' => $date,
		'key_flag' => $keyflag,
        'is_active' => 1
		);
		
		$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_logmanagercron');
			return $id;

	}

}