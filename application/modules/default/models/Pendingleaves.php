<?php

class Default_Model_Leavemanagement extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_leavemanagement';
    protected $_primary = 'id';
	
	public function getLeaveManagementData($sort, $by, $pageNo, $perPage,$searchQuery)
	{
		$where = "l.isactive = 1 AND d.isactive=1 AND w.isactive=1 AND wk.isactive=1 AND m.isactive=1";
		
		if($searchQuery)
			$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$leaveManagementData = $this->select()
    					   ->setIntegrityCheck(false)	
                           ->from(array('l'=>'main_leavemanagement'),
						          array( 'l.*',
										 
										 'halfday'=>'if(l.is_halfday = 1,"yes","No")',
										 'leavetransfer'=>'if(l.is_leavetransfer = 1,"yes","No")',
										 'skipholidays'=>'if(l.is_skipholidays = 1,"yes","No")',
								 ))
						   ->joinLeft(array('w'=>'main_weekdays'), 'w.id=l.weekend_startday',array('daystartname'=>'w.day_name'))	
                           ->joinLeft(array('wk'=>'main_weekdays'), 'wk.id=l.weekend_endday',array('dayendname'=>'wk.day_name'))						   
                           ->joinLeft(array('m'=>'main_monthslist'), 'm.id=l.cal_startmonth',array('m.month_name')) 
                           ->joinLeft(array('d'=>'main_departments'), 'd.id=l.department_id',array('d.deptname')) 		     						   
						   ->where($where)
    					   ->order("$by $sort") 
    					   ->limitPage($pageNo, $perPage);
		
		return $leaveManagementData;       		
	}
	public function getsingleLeaveManagementData($id)
	{
		$row = $this->fetchRow("id = '".$id."'");
		if (!$row) {
			throw new Exception("Could not find row $id");
		}
		return $row->toArray();
	}
	
	public function SaveorUpdateLeaveManagementData($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_leavemanagement');
			return $id;
		}
	
	}
	
	public function getActiveRecord()
	{
	 	$select = $this->select()
    					   ->setIntegrityCheck(false)	
                           ->from(array('l'=>'main_leavemanagement'),
						          array( 'l.*',
										 'satholiday'=>'if(l.is_satholiday = 1,"yes","No")',
										 'halfday'=>'if(l.is_halfday = 1,"yes","No")',
										 'leavetransfer'=>'if(l.is_leavetransfer = 1,"yes","No")',
										 'skipholidays'=>'if(l.is_skipholidays = 1,"yes","No")',
								 ))
						   ->joinLeft(array('w'=>'main_weekdays'), 'w.id=l.week_startday',array('w.day_name'))						   						   
                           ->joinLeft(array('m'=>'main_monthslist'), 'm.id=l.cal_startmonth',array('m.month_name')) 		   
						   ->where('l.isactive = 1');  		   					   				
		return $this->fetchAll($select)->toArray();   
	
	}
	
	public function getsatholidaycount()
	{
	   $select = $this->select()
    					   ->setIntegrityCheck(false)	
                           ->from(array('l'=>'main_leavemanagement'),array('satholiday'=>'if(l.is_satholiday = 1,"yes","no")'))
						   ->where('l.isactive = 1');  		   					   				
		return $this->fetchAll($select)->toArray(); 
	
	}
	
	public function getActiveDepartmentIds()
	{
	  $select = $this->select()
    					   ->setIntegrityCheck(false)	
                           ->from(array('l'=>'main_leavemanagement'),array('deptid'=>'l.department_id'))
						   ->where('l.isactive = 1');  		   					   				
		return $this->fetchAll($select)->toArray(); 
	}
	
	public function getWeekendDetails($deptid)
	{
	   $select = $this->select()
    					   ->setIntegrityCheck(false)	
                           ->from(array('l'=>'main_leavemanagement'),array('weekendstartday'=>'l.weekend_startday','weekendday'=>'l.weekend_endday'))
						   ->where('l.department_id = '.$deptid.' AND l.isactive = 1');  		   					   				
		return $this->fetchAll($select)->toArray(); 
		
	}
}