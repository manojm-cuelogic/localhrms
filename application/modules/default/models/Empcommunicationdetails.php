<?php

class Default_Model_Empcommunicationdetails extends Zend_Db_Table_Abstract
{
	protected $_name = 'main_empcommunicationdetails';
    protected $_primary = 'id';
	
	public function getsingleEmpCommDetailsData($id)
	{
		$select = $this->select()
						->setIntegrityCheck(false)
						->from(array('ec'=>'main_empcommunicationdetails'),array('ec.*'))
						->where('ec.user_id='.$id.' AND ec.isactive = 1');
					
		return $this->fetchAll($select)->toArray();
	}
	
	public function SaveorUpdateEmpcommData($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_empcommunicationdetails');
			return $id;
		}
		
	}	
}