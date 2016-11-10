<?php

class Default_Model_Empworkinfo extends Zend_Db_Table_Abstract
{
	protected $_name = 'main_empworkinfo';
    protected $_primary = 'id';
	
	public function getsingleEmpWorkInfoData($id)
	{
		$select = $this->select()
						->setIntegrityCheck(false)
						->from(array('ep'=>'main_empworkinfo'),array('ewi.*'))
						->where('ewi.user_id='.$id.' AND ewi.isactive = 1');
					
		return $this->fetchAll($select)->toArray();
	}
	
	public function SaveorUpdateEmpWorkInfoData($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_empworkinfo');
			return $id;
		}
		
	}	
}
?>