<?php

class Default_Model_Empsalarydetails extends Zend_Db_Table_Abstract
{	
    protected $_name = 'main_empsalarydetails';
    protected $_primary = 'id';
	
	public function getsingleEmpSalaryDetailsData($id)
	{
		$select = $this->select()
						->setIntegrityCheck(false)
						->from(array('s'=>'main_empsalarydetails'),array('s.*'))
						->where('s.user_id='.$id.' AND s.isactive = 1');
					
		return $this->fetchAll($select)->toArray();
	}
	
	public function SaveorUpdateEmpSalaryData($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_empsalarydetails');
			return $id;
		}
		
	}
	
}