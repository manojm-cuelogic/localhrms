<?php

class Default_Model_Employees extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_employees';
    protected $_primary = 'id';
	
	public function getRepMangerID($empid)
	{
		$select = $this->select()
								->setIntegrityCheck(false)
								->from(array('e'=>'main_employees'), array('repmangerid'=>'e.reporting_manager'))
								->where('e.user_id="'.$empid.'" AND e.isactive = 1 ');
			 
		return $this->fetchAll($select)->toArray();

	}
	
	public function getLoggedInEmployeeDetails($userid)
	{
	    $result =  $this->select()
    				->setIntegrityCheck(false) 	
    				->from(array('e'=>'main_employees'),array('e.*'))
 	  				->where("e.isactive = 1 AND e.user_id =".$userid." ");
		
    	return $this->fetchAll($result)->toArray();
	}
    
	public function SaveorUpdateEmployees($data, $where)
	{
		
		if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_employees');
			return $id;
		}
	}
	
	public function getHolidayGroupForEmployee($userid)
	{
		
	    $result =  $this->select()
    				->setIntegrityCheck(false) 	
    				->from(array('e'=>'main_employees'),array('e.holiday_group'))
 	  				->where("e.isactive = 1 AND e.user_id =".$userid." ");
    	return $this->fetchAll($result)->toArray();
	
	}
	
	public function CheckIfReportingManager($loginUserId)
	{
		$select = $this->select()
    					   ->setIntegrityCheck(false)	
                           ->from(array('main_employees_summary'),array('count'=>'count(*)'))
                           ->where("isactive = 1 AND reporting_manager = $loginUserId");
		                           
		return $this->fetchAll($select)->toArray();       		
	}

    public function getEmployees($ids = array()) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $furtherCondition = "";
        if(is_array($ids) && count($ids) > 0) {
            $furtherCondition = " and e.user_id in (" . implode(",", $ids) . ")";
        }
        $query = "select e.user_id, e.employeeId, e.emailaddress, e.firstname, e.lastname, e.isactive, e.emp_status_name, e.emp_status_id, e.jobtitle_id, e.jobtitle_name, e.emprole, e.emprole_name, e.date_of_joining, e.date_of_leaving, e.reporting_manager, u.employeeId as m_employeeid, u.emailaddress as m_emailaddress  from main_employees_summary e left join main_users u on u.id = e.reporting_manager where e.isactive = 1 ".$furtherCondition; 
        $result = $db->query($query);
        $rows = $result->fetchAll();
        return $rows;
    }
}