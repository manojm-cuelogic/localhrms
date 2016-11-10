<?php

class Default_Model_Empworkdetails extends Zend_Db_Table_Abstract
{
	protected $_name = 'main_empworkdetails';
    protected $_primary = 'id';
	
	public function getsingleEmpWorkDetailsData($id)
	{
		$select = $this->select()
						->setIntegrityCheck(false)
						->from(array('ec'=>'main_empworkdetails'),array('ew.*'))
						->where('ew.user_id='.$id.' AND ew.isactive = 1');
					
		return $this->fetchAll($select)->toArray();
	}
	
	public function SaveorUpdateEmpWorkData($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_empworkdetails');
			return $id;
		}
		
	}	
}