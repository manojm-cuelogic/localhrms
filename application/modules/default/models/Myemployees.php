<?php

class Default_Model_Myemployees extends Zend_Db_Table_Abstract
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
						   ->joinLeft(array('el'=>'main_employeeleaves'),'e.user_id=el.user_id',array('leave_limit'=>'el.emp_leave_limit', 'leave_type'=>'el.leavetypeid'))
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
	public function getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$exParam1='',$exParam2='',$exParam3='',$exParam4='')
	{
		$auth = Zend_Auth::getInstance();
    	$request = Zend_Controller_Front::getInstance();
     	if($auth->hasIdentity()){
				$loginUserGroup = $auth->getStorage()->read()->group_id;
				$loginUserRole = $auth->getStorage()->read()->emprole;
		}
		$controllerName = $request->getRequest()->getControllerName();
		if($controllerName=='employee' && ($loginUserRole == SUPERADMINROLE || $loginUserGroup == HR_GROUP || $loginUserGroup == MANAGEMENT_GROUP))
			$filterArray = array(''=>'All',1 => 'Active',0 => 'Inactive',2 => 'Resigned',3 => 'Left',4 => 'Suspended');
		else
			$filterArray = array(''=>'All',1 => 'Active');
		
        $searchQuery = '';$tablecontent = '';
        $searchArray = array();$data = array();$id='';
        $dataTmp = array();
		if($searchData != '' && $searchData!='undefined')
		{
			$searchValues = json_decode($searchData);
			
			foreach($searchValues as $key => $val)
			{
				
				if($key == "userfullname")
					$searchQuery .= " e.".$key." like '%".$val."%' AND ";
				else if($key == "rm")
					$searchQuery .= " e.userfullname like '%".$val."%' AND ";
				else if($key == "jobtitle_name")
					$searchQuery .= " e.jobtitle_name like '%".$val."%' AND ";					
				else if($key == 'extn')					
					$searchQuery .= " concat(e.office_number,' (ext ',e.extension_number,')') like '%".$val."%' AND ";
				else if($key == 'astatus')
              		$searchQuery .= " e.isactive like '%".$val."%' AND ";
              	else if($key == 'bpleave')
              		$searchQuery .= " el.emp_leave_limit AND el.leavetypeid = 1 AND ";
              	else if($key == 'bsleave')
              		$searchQuery .= " el.emp_leave_limit AND el.leavetypeid = 2 AND ";
				else 
					$searchQuery .= " e.".$key." like '%".$val."%' AND ";
				
				$searchArray[$key] = $val;
			}
			$searchQuery = rtrim($searchQuery," AND");					
		}
		$objName = 'myemployees';$emptyroles=0;
		
		$tableFields = array('action'=>'Action','firstname'=>'First Name','lastname'=>'Last Name','emailaddress'=>'Email','employeeId' =>'Employee ID','bpleave' => 'Balance Privilege Leave', 'bsleave' => 'Balance Sick Leave', 'astatus' =>'User Status','jobtitle_name'=>'Job Title');
		
		$employeeModel = new Default_Model_Employee();
		$tablecontent = $employeeModel->getEmployeesData($sort,$by,$pageNo,$perPage,$searchQuery,$exParam1,$exParam1);  
		
		if($tablecontent == "emptyroles")
		{
			$emptyroles=1;
		}
		else
		{	
			$emptyroles=0;
		}
			$dataTmp = array('dashboardcall'=>$dashboardcall,
					'emptyroles'=>$emptyroles,
					'sort' => $sort,
					'by' => $by,
					'pageNo' => $pageNo,
					'perPage' => $perPage,				
					'tablecontent' => $tablecontent,
					'objectname' => $objName,
					'extra' => array(),
					'tableheader' => $tableFields,
					'jsGridFnName' => 'getAjaxgridData',
					'jsFillFnName' => '',
					'searchArray' => $searchArray,
					'menuName' => 'My Team',
					'add'=>'add',
					'call'=>$call,
					'sortStr'=>$by,
					'context'=>'myteam',
					'search_filters' => array(
                                                'astatus' => array('type'=>'select',
                                                'filter_data'=>$filterArray),
                                                ),
				);			
		return $dataTmp;
	}
	
	/**
	 * 
	 * Get login user ID
	 * @return Integer - Login user ID
	 */
	public function getLoginUserId() {
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
			return $auth->getStorage()->read()->id;
		}		
	}
	
	public function getTeamIds($mgr_id)
	{
		$data = array();
        if($mgr_id != '')
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            $query = "select user_id from main_employees_summary where reporting_manager = $mgr_id;";
            $data = $db->query($query)->fetchAll();
        }
       return $data;
	}
	
}
?>