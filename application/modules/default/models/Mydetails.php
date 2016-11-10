<?php

class Default_Model_Mydetails extends Zend_Db_Table_Abstract
{
	protected $_name = 'main_employees';
    protected $_primary = 'id';
	
	public function getEmployeesData($sort, $by, $pageNo, $perPage,$searchQuery)
	{
		$where = " e.isactive = 1 ";
		if($searchQuery)
			$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$employeesData = $this->select()
    					   ->setIntegrityCheck(false)	 
						   ->from(array('e' => 'main_employees'),array('id'=>'e.user_id'))
						   ->joinInner(array('u'=>'main_users'),'e.reporting_manager=u.id',array('reportingmanager'=>'u.userfullname'))
						   ->joinInner(array('mu'=>'main_users'),'e.user_id=mu.id',array('empId'=>'mu.employeeId','empname'=>'mu.userfullname','empemail'=>'mu.emailaddress'))
						   ->joinInner(array('b'=>'main_businessunits'),'e.businessunit_id=b.id',array('businessunit'=>'b.unitname'))
						   ->joinInner(array('d'=>'main_departments'),'e.department_id=d.id',array('department'=>'d.deptname'))
						   ->joinLeft(array('j'=>'main_jobtitles'),'e.jobtitle_id=j.id',array('jobtitle'=>'j.jobtitlename'))
						   ->joinLeft(array('p'=>'main_positions'),'e.position_id=p.id',array('position'=>'p.positionname'))
						   ->where($where)
    					   ->order("$by $sort") 
    					   ->limitPage($pageNo, $perPage);
		
		return $employeesData;       		
	}
	
	public function getsingleEmployeeData($id)
	{
		$row = $this->fetchRow("user_id = '".$id."'");
		if (!$row) {
			throw new Exception("Could not find row $id");
		}
		return $row->toArray();
	}
	
	public function SaveorUpdateEmployeeData($data, $where)
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
	
	
}
?>