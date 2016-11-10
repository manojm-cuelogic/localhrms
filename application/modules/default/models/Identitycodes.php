<?php

class Default_Model_Identitycodes extends Zend_Db_Table_Abstract
{	
    protected $_name = 'main_identitycodes';
    protected $_primary = 'id';
	
	public function getIdentitycodesRecord()
	{	$identityCodesArr="";
		$db = Zend_Db_Table::getDefaultAdapter();		
	    $select = $this->select()
                            ->from(array('i'=>'main_identitycodes'),array('i.*'));    					   				
		$identityCodesArr = $this->fetchAll($select)->toArray(); 
		
		return  $identityCodesArr; 
	
	}
        public function getIdentitycodesRecord_i($id)
	{	$identityCodesArr="";
		$db = Zend_Db_Table::getDefaultAdapter();		
	    $select = $this->select()
                            ->from(array('i'=>'main_identitycodes'),array('i.*'))
                            ->where("id = ".$id);
		$identityCodesArr = $this->fetchAll($select)->toArray(); 
		
		return  $identityCodesArr; 
	
	}
	public function SaveorUpdateIdentitycodesData($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_identitycodes');
			return $id;
		}		
	}
	
	public function getallcodes($code)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select * from main_identitycodes";		
		$result = $db->query($query)->fetch();
		if($code == 'bgcheck')
	    return $result['backgroundagency_code'];
		else
		return $result;
	}
}