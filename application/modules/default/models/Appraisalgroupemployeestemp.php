<?php

class Default_Model_Appraisalgroupemployeestemp extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_pa_groups_employees_temp';
    protected $_primary = 'id';
	
	public function checkAppraisalRecordexists($groupid,$appraisalid)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$options = array();
		
		if($groupid !='null' && $appraisalid !='null')
		{
		 	$query = "select ge.id from main_pa_groups_employees_temp ge where ge.pa_initialization_id='.$appraisalid.' AND ge.group_id ='.$groupid.' AND ge.isactive=1 ";
            $result = $db->query($query);
            $options = $result->fetchAll();
		}    
        return $options;
	}
	
		
	public function SaveorUpdateAppraisalGroupsEmployeesTempData($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_pa_groups_employees_temp');
			return $id;
		}
		
	
	} 
		
}