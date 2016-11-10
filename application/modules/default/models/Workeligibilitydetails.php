<?php

class Default_Model_Workeligibilitydetails extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_empworkeligibility';
    protected $_primary = 'id';
	
	
	public function getWorkEligibilityRecord($id=0)
	{
		$WorkEligibilityDetailsArr="";$where = "";
		$db = Zend_Db_Table::getDefaultAdapter();		
		if($id != 0)
		{
			$where = "user_id =".$id;
			$WorkEligibilityData = $this->select()
									->from(array('w'=>'main_empworkeligibility'))
									->where($where);
		
			
			$WorkEligibilityDetailsArr = $this->fetchAll($WorkEligibilityData)->toArray(); 
        }
		return $WorkEligibilityDetailsArr; 
	}
	
	public function SaveorUpdateWorkEligibilityDetails($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_empworkeligibility');
			return $id;
		}
		
	
	}
}?>