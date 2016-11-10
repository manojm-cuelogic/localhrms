<?php

class Default_Model_Allottedleaveslog extends Zend_Db_Table_Abstract{
	protected $_name = 'main_allottedleaveslog';
	protected $_primary = 'id';
	
    public function SaveorUpdateAllotedLeavesDetails($data,$where)
	{	   		
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_bgpocdetails');
			return $id;
		}
	}

}