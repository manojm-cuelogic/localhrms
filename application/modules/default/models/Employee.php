<?php

class Default_Model_Employee extends Zend_Db_Table_Abstract
{
	protected $_name = 'main_employees';
	protected $_primary = 'id';		

	/*
	   I. This query fetches employees data based on roles.
	   II. If roles are not configured then to eliminate users and other vendors we are using jobtitle clause.
		   As for jobtitle id for vendors and users will always be null.
	*/
	public function getEmployeesData($sort,$by,$pageNo,$perPage,$searchQuery,$managerid='',$loginUserId)
	{
		$auth = Zend_Auth::getInstance();
		$request = Zend_Controller_Front::getInstance();
		if($auth->hasIdentity()){
					$loginUserGroup = $auth->getStorage()->read()->group_id;
					$loginUserRole = $auth->getStorage()->read()->emprole;
		}
		$controllerName = $request->getRequest()->getControllerName();
		//the below code is used to get data of employees from summary table.
		$employeesData=""; 
		if($controllerName=='employee' && ($loginUserRole == SUPERADMINROLE || $loginUserGroup == HR_GROUP || $loginUserGroup == MANAGEMENT_GROUP))                            
			$where = "  e.isactive != 5 AND e.user_id != ".$loginUserId." ";
		else	  
			$where = "  e.isactive = 1 AND e.user_id != ".$loginUserId." ";
		
		if($managerid !='')
			$where .= " AND e.reporting_manager = ".$managerid." ";
		if($searchQuery != '')
			$where .= " AND ".$searchQuery;

		$employeesData = $this->select()
								->setIntegrityCheck(false)	                                
								->from(array('e' => 'main_employees_summary'),
										array('*','bpleave'=>new Zend_Db_Expr('(select (mel.emp_leave_limit-mel.used_leaves) as bpleave from main_employeeleaves mel where mel.user_id = e.user_id and mel.leavetypeid = 1)'), 'bsleave'=>new Zend_Db_Expr('(select (mel.emp_leave_limit-mel.used_leaves) as bpleave from main_employeeleaves mel where mel.user_id = e.user_id and mel.leavetypeid = 2)'),'id'=>'e.user_id','extn'=>new Zend_Db_Expr('case when e.extension_number is not null then concat(e.office_number," (ext ",e.extension_number,")") when e.extension_number is null then e.office_number end'),'astatus'=> new Zend_Db_Expr('case when e.isactive = 0 then "Inactive" when e.isactive = 1 then "Active" when e.isactive = 2 then "Resigned"  when e.isactive = 3 then "Left" when e.isactive = 4 then "Suspended" end')
											))
								->where($where)
								->order("$by $sort") 
								->limitPage($pageNo, $perPage);//
								//exit;

		return $employeesData;       		
	}
	
	public function getUserRole()
	{	
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$usersData = $db->query("select GROUP_CONCAT(r.id) as roles from main_roles As r Inner join main_groups As g  on r.group_id=g.id 
		where r.isactive=1 AND g.id IN(".MANAGER_GROUP.",".HR_GROUP.",".EMPLOYEE_GROUP.",".SYSTEMADMIN_GROUP.",".MANAGEMENT_GROUP.")");
	  
		$usersResult = $usersData->fetchAll();
		
		return $usersResult;
	}
	/**
		 * This function gives full employee details based on user id.
		 * @param integer $id  = id of employee
		 * @return array  Array of employee details.
		 */
	public function getsingleEmployeeData($id)
	{
		
			$db = Zend_Db_Table::getDefaultAdapter();
			$empData = $db->query("SELECT e.*,u.*,p.prefix,p.isactive as active_prefix FROM main_employees e 
				INNER JOIN main_users u ON e.user_id = u.id left JOIN main_prefix p ON e.prefix_id = p.id
						   WHERE e.user_id = ".$id."   AND  u.isactive IN (1,2,3,4,0) AND u.userstatus ='old'");
			$res = $empData->fetchAll();
			if (isset($res) && !empty($res)) 
			{	
				return $res;
			}
			else
				return 'norows';
	}
		/**
		 * This function is used to get data in employees report.
		 * @param array $param_arr   = array of parameters.
		 * @param integer $per_page  = no.of records per page
		 * @param integer $page_no   = page number
		 * @param string $sort_name  = name of the column to be sort
		 * @param string $sort_type  = descending or ascending
		 * @return array  Array of all employees.
		 */
		public function getdata_emp_report($param_arr,$per_page,$page_no,$sort_name,$sort_type)
		{
			$search_str = " e.isactive != 5 ";
	  
			foreach($param_arr as $key => $value)
			{
					if($value != '')
					{
							if($key == 'date_of_joining')
				   
				$search_str .= " and e.".$key." = '".sapp_Global::change_date ($value,'database')."'";
								
							if($key == 'isactive')
							$search_str .= " and e.".$key." = '".$value."'";
							if( ($key == 'businessunit_id' || $key === 'department_id'))
							{
								if(is_array($value))
								{                                    
									$search_str .= " and e.".$key." in (".  implode(',', $value).")";
								}
							}
							else if($key == 'emprole'){
								
								$val = explode("_", $value);
								$search_str .= " and e.emprole = ".$val[0]." ";
							}
							else if($key == 'reporting_manager'){
								
								$search_str .= " and e.reporting_manager = ".$value." ";
							}
							else if($key == 'jobtitle_id'){
								
								$search_str .= " and e.jobtitle_id = ".$value." ";
							}
							else if($key == 'emp_status_id'){
								
								$search_str .= " and e.emp_status_id = ".$value." ";
							}
							
							else
							{
								if($key != 'isactive'&& $key != 'date_of_joining')
									$search_str .= " and ".$key." = '".$value."'";
							}
								

					}
			}
			
			//die($search_str);
			$offset = ($per_page*$page_no) - $per_page;
		 
			$db = Zend_Db_Table::getDefaultAdapter();
			$limit_str = "";
			if($per_page != 0)
				$limit_str = " limit ".$per_page." offset ".$offset;
			
			$count_query = "select count(*) cnt from main_employees_summary e where ".$search_str;
	  
			$count_result = $db->query($count_query);
			$count_row = $count_result->fetch();
			$count = $count_row['cnt'];
			$page_cnt = ceil($count/$per_page);
			$sort_name = ($sort_name == 'modifieddate') ? "e.modifieddate" : $sort_name;
			$query = "select  "
			. "e.employeeId, "
			. "e.userfullname, "
			. "mu.middlename, "
			. "e.reporting_manager_name,"
			. "met.name as employee_type, "
			. "e.emailaddress,"
			. "e.department_name,"
			. "e.jobtitle_name,"
			. "e.emp_status_name,"
			. "e.date_of_joining,"
			. "e.contactnumber,"
			. "me.emp_project_status,"
			. "me.functional_area,"
			. "mpd.dob,"
			. "if(mpd.genderid = 2, 'Female', 'Male') as genderid,"
			. "mpd.skype_id,"
			. "mpd.prev_org_hr_email,"
			. "ms.maritalstatusname,"
			. "es.salary,"
			. "es.accountnumber,"
			. "es.pan_number,"
			. "es.driver_license_number,"
			. "es.pf_number,"
			. "mcd.personalemail,"
			. " (select GROUP_CONCAT(m_p.project_name) from main_employee_projects mep left join main_projects m_p on m_p.id = mep.project_id where mep.user_id = e.user_id and mep.isactive = 1 ) as projects, "
			. " (select GROUP_CONCAT(concat(from_date,',',to_date)) from main_empexperiancedetails med where med.user_id = e.user_id and med.isactive = 1 ) as experiance, "
			. " (select GROUP_CONCAT(course) from main_empeducationdetails medud where medud.user_id = e.user_id and medud.isactive = 1 ) as education,"
			. " (select GROUP_CONCAT(skillname) from main_empskills eskill where eskill.user_id = e.user_id and eskill.isactive = 1 ) as technology,"
			. "mcd.current_streetaddress,"
			. "mcd.perm_streetaddress,"
			. "mcd.emergency_name_1,"
			. "mcd.emergency_name_2,"
			. "mcd.emergency_number_1,"
			. "mcd.emergency_number_2,"
			. "mcd.relation_emergency_1,"
			. "mcd.relation_emergency_2,"
			. "mvd.passport_number"
			. " from main_employees_summary e"
			. " left join main_emppersonaldetails mpd on mpd.user_id = e.user_id"
			//. " left join main_empexperiancedetails med on med.user_id = e.user_id"
			. " left join main_empsalarydetails es on es.user_id = e.user_id"
			. " left join main_empcommunicationdetails mcd on mcd.user_id = e.user_id"
			. " left join main_empvisadetails mvd on mvd.user_id = e.user_id "
			. " left join main_currency c on c.id = es.currencyid "
			. " left join main_payfrequency p on p.id = es.salarytype "
			. " left join main_maritalstatus ms on ms.id = mpd.maritalstatusid "
			. " left join main_users mu on mu.id = e.user_id "
			. " left join main_employees me on me.id = e.id "
			. " left join main_employee_type met on met.id = me.employee_type "
			. "where ".$search_str." "
			. ((strlen(trim($sort_name)) > 0) ? "order by ".$sort_name." ".$sort_type." " : "")
			.$limit_str;
			// $query = "select e.*,es.salary,p.freqtype,c.currencyname, case when e.isactive = 0 then 'Inactive' when e.isactive = 1 then 'Active' when e.isactive = 2 then 'Resigned'  when e.isactive = 3 then 'Left' when e.isactive = 4 then 'Suspended' end isactive"
			// 		. " from main_employees_summary e left join main_empsalarydetails es on es.user_id = e.user_id  "
			// 		. " left join main_currency c on c.id = es.currencyid "
			// 		. " left join main_payfrequency p on p.id = es.salarytype "
			// 		. "where ".$search_str." "
			// 		. "order by ".$sort_name." ".$sort_type." ".$limit_str;


			// $query = "select e.*,es.salary,p.freqtype,c.currencyname, case when e.isactive = 0 then 'Inactive' when e.isactive = 1 then 'Active' when e.isactive = 2 then 'Resigned'  when e.isactive = 3 then 'Left' when e.isactive = 4 then 'Suspended' end isactive"
			// 		. " from main_employees_summary e left join main_empsalarydetails es on es.user_id = e.user_id  "
			// 		. " left join main_currency c on c.id = es.currencyid "
			// 		. " left join main_payfrequency p on p.id = es.salarytype "
			// 		. "where ".$search_str." "
			// 		. "order by ".$sort_name." ".$sort_type." ".$limit_str;
			//echo $query; exit;
			$result = $db->query($query);
			$rows = $result->fetchAll();
			return array('rows' => $rows,'page_cnt' => $page_cnt, 'count_emp' => $count);
		}
	/**
	 * This function is used to get data for pop up in groups,roles and employees report
	 * @param Integer $group_id    = id of the group
	 * @param Integer $role_id     = id of the role
	 * @param Integer $page_no     = page number
	 * @param String $sort_name    = field name to be sort
	 * @param String $sort_type    = sort type like asc,desc
	 * @return Array Array of employees of given role and group
	 */
	public function emprolesgrouppopup($group_id,$role_id,$page_no,$sort_name,$sort_type,$per_page)
	{
		$offset = ($per_page*$page_no) - $per_page;
		$db = Zend_Db_Table::getDefaultAdapter();
		$limit_str = " limit ".$per_page." offset ".$offset;
		if($group_id == USERS_GROUP)
		{
			if($role_id != '')
			{
				$role_str = " and emprole in (".$role_id.")";
			}
			else 
			{
				$role_str = " and emprole in (select id from main_roles where group_id = ".$group_id." and isactive = 1)";
			}
			$count_query = "select count(*) cnt from main_users where isactive = 1 ".$role_str;
			$count_result = $db->query($count_query);
			$count_row = $count_result->fetch();
			$count = $count_row['cnt'];
			$page_cnt = ceil($count/$per_page);
			$query = "select r.rolename rolename_p,u.userfullname,u.employeeId,u.emailaddress from main_users u,main_roles r where r.id = u.emprole and u.isactive = 1 ".$role_str." order by ".$sort_name." ".$sort_type." ".$limit_str;
			$result = $db->query($query);
			$rows = $result->fetchAll();
			return array('rows' => $rows,'page_cnt' => $page_cnt);
		}
		else 
		{
			if($role_id != '')
			{
				$role_str = " and emprole in (".$role_id.")";
			}
			else 
			{
				$role_str = " and emprole in (select id from main_roles where group_id = ".$group_id." and isactive = 1)";
			}
			$count_query = "select count(*) cnt from main_employees_summary where isactive = 1 ".$role_str;
			$count_result = $db->query($count_query);
			$count_row = $count_result->fetch();
			$count = $count_row['cnt'];
			$page_cnt = ceil($count/$per_page);
			$query = "select * from main_employees_summary where isactive = 1 ".$role_str." order by ".$sort_name." ".$sort_type." ".$limit_str;
			$result = $db->query($query);
			$rows = $result->fetchAll();
			return array('rows' => $rows,'page_cnt' => $page_cnt);
		}
	}
	public function SaveorUpdateEmployeeData($data, $where)
	{
		if($where != ''){
			// echo "<pre>". $where; print_r($data); exit;
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_employees');
			return $id;
		}
		
	}
	
	public function getActiveEmployeeData($id)
	{
		$result =  $this->select()
					->setIntegrityCheck(false) 	
					->from(array('e'=>'main_employees'),array('e.*'))
					->where("e.isactive = 1 AND e.user_id = ".$id);
	//echo $result;
		return $this->fetchAll($result)->toArray();
	}
	public function getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$exParam1='',$exParam2='',$exParam3='',$exParam4='')
	{
		$searchQuery = '';
		$tablecontent = '';
		$emptyroles=0;
		$empstatus_opt = array();
		$searchArray = array();
		$data = array();
		$id='';
		$dataTmp = array();
		
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
		
		if($searchData != '' && $searchData!='undefined')
		{
			$searchValues = json_decode($searchData);
			
			foreach($searchValues as $key => $val)
			{				
			   
					if($key == 'astatus')
					$searchQuery .= " e.isactive like '%".$val."%' AND ";
				else if($key == 'extn')					
					$searchQuery .= " concat(e.office_number,' (ext ',e.extension_number,')') like '%".$val."%' AND ";
				else 
					$searchQuery .= $key." like '%".$val."%' AND ";				
				$searchArray[$key] = $val;
			}
			$searchQuery = rtrim($searchQuery," AND");					
		}
		$objName = 'employee';
						
			
		$tableFields = array('action'=>'Action','firstname'=>'First Name','lastname'=>'Last Name','emailaddress'=>'Email',
							 'employeeId' =>'Employee ID','businessunit_name' => 'Business Unit','department_name' => 'Department','astatus' =>'User Status',
							 'jobtitle_name'=>'Job Title','reporting_manager_name'=>'Reporting Manager',
							'emprole_name'=>"Role");

		if($controllerName=='employee' && ($loginUserRole == SUPERADMINROLE || $loginUserGroup == HR_GROUP || $loginUserGroup == MANAGEMENT_GROUP)){
			$tableFields['emp_status_name']='Employment Status';
		}

		   
		$tablecontent = $this->getEmployeesData($sort,$by,$pageNo,$perPage,$searchQuery,'',$exParam1);  
			
		if($tablecontent == "emptyroles")
		{
			$emptyroles=1;
		}
		else
		{	
			$employmentstatusModel = new Default_Model_Employmentstatus();
			$employmentStatusData = $employmentstatusModel->getempstatuslist();	
			
			if(count($employmentStatusData) >0)
			{
				foreach($employmentStatusData as $empsdata)
				{
					$empstatus_opt[$empsdata['workcodename']] = $empsdata['statusname'];
				}
			}
		}
		
		$dataTmp = array(
						'userid'=>$id,
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
						'menuName' => 'Employees',
						'dashboardcall'=>$dashboardcall,
						'add'=>'add',
						'call'=>$call,
						'search_filters' => array(
												'astatus' => array('type'=>'select',
												'filter_data'=>$filterArray),
												'emp_status_id'=>array(
																		'type'=>'select',
																		'filter_data' => array(''=>'All')+$empstatus_opt),
												),
						'emptyroles'=>$emptyroles
					);	
				
		return $dataTmp;
	}
	
	public function getAutoReportEmp($search_str)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select * from ((select u.id user_id,u.profileimg,concat(u.userfullname,if(j.jobtitlename is null,'',concat(' , ',j.jobtitlename))) emp_name,
				  case when u.userfullname like '".$search_str."%' then 4  when u.userfullname like '__".$search_str."%' then 2 
				  when u.userfullname like '_".$search_str."%' then 3 when u.userfullname like '%".$search_str."%' then 1 
				  else 0 end emp 
				  from main_users u left join main_jobtitles j on j.id = u.jobtitle_id  
				  and j.jobtitlename like '%".$search_str."%'  where  u.isactive =1 and u.jobtitle_id is not null 
				  and (u.userfullname like '%".$search_str."%' or u.emailaddress like '%".$search_str."%') 
					)
					union (select u.id user_id,u.profileimg,concat(u.userfullname,', Super Admin') emp_name ,
					case when u.userfullname like '".$search_str."%' then 4 when u.userfullname like '__".$search_str."%' then 2 when u.userfullname like '_".$search_str."%' then 3 
					when u.userfullname like '%".$search_str."%' then 1 else 0 end emp from main_users u where u.id = 1 and 
					(u.userfullname like '%".$search_str."%' or 'Super Admin' like '%".$search_str."%' or u.emailaddress like '%".$search_str."%') )
					) a
				  order by emp desc
				  limit 0,10";
		$result = $db->query($query);
		$emp_arr = array();
		$emp_arr = $result->fetchAll();
		return $emp_arr;
	}
	
	/**
	 * 
	 * Show auto suggestions to user in employees reporing to manager report
	 * @param $search_str
	 */
	public function getAutoReportEmployee($params)
	{
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
		}				
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select u.id user_id, u.emailaddress, u.userfullname, u.profileimg,u.employeeId FROM main_users u 
				  inner join main_employees_summary e on u.id=e.user_id	
				  WHERE u.".$params['field']." LIKE '%".$params['term']."%' AND e.reporting_manager = $loginUserId
				  order by u.emailaddress desc limit 0,10";
		$result = $db->query($query);
		$emp_arr = array();
		$emp_arr = $result->fetchAll();
		return $emp_arr;
	}
		
	public function isSuperManager () {

		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
		}

		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select user_id from main_super_managers where user_id = " . $loginUserId . " and isactive = 1";
		$result = $db->query($query);
		$emp_arr = array();
		$emp_arr = $result->fetchAll();
		return (count($emp_arr) > 0 ? 1 : 0);
		//return $emp_arr;
	}


	/* Get count of direct reportee for given empId*/

	public function getNumberOfDirectReporters($empId)
	{
	
		$select = $this->select()
        ->setIntegrityCheck(false)
        ->from(array('es'=>'main_employees_summary'), array('directReporteeCount'=>'count(es.id)', ))
        ->where('es.reporting_manager= "'.$empId.'"')
        ->where('es.isactive= 1');
        
        return $this->fetchAll($select)->toArray();

	}

	/* Get brief details about direct reportee for given empId*/
	public function getDirectReporters($empId)
	{
		
		$select = $this->select()
        ->setIntegrityCheck(false)
        ->from(array('es'=>'main_employees_summary'), array('userId'=>'es.user_id','jobTitle'=>'es.jobtitle_name', 'userFullName'=>'es.userfullname'))
        ->where('es.reporting_manager= "'.$empId.'"')
        ->where('es.isactive= 1');
       
        return $this->fetchAll($select)->toArray();

	}



	public function getEmployeesUnderRM($empid,$bunit='',$deptid='',$eligibility='')
	{
		/***
		*** edited on 12-03-2015 , soujanya
		*** for filtering employees based on business unit id in Initialize appraisal > step -2
		***/
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select * from main_employees_summary where reporting_manager = ".$empid." and isactive = 1 ";
		if(!empty($bunit)) $query.=' and businessunit_id = '.$bunit;
		if(!empty($deptid)) $query.=' and department_id = '.$deptid;
		if(!empty($eligibility)) $query.=' and emp_status_id IN ('.$eligibility.') ';
		
		$result = $db->query($query);
		$emp_arr = array();
		$emp_arr = $result->fetchAll();
		return $emp_arr;
	}
	
	public function getCurrentOrgHead()
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select user_id from main_employees where is_orghead = 1 and isactive = 1";
		$result = $db->query($query);
		$emp_arr = array();
		$emp_arr = $result->fetchAll();
		return $emp_arr;
	}
	
	public function changeRM($oldRM,$newRM,$status,$ishead)
	{		
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$db = Zend_Db_Table::getDefaultAdapter();	
		$db->beginTransaction();
		$oldRMData = $this->getsingleEmployeeData($oldRM);
		try
		{				
			if($status == 'active')
			{
				$data = array(
					'isactive' => 1,
					'emptemplock' => 0,
					'modifieddate' => gmdate("Y-m-d H:i:s"),
					'modifiedby' => $loginUserId
				);
							$Query1 = "UPDATE main_employees SET isactive = 1, modifieddate = '".gmdate("Y-m-d H:i:s")."', modifiedby = ".$loginUserId." WHERE user_id=".$oldRM." ;";				
							$db->query($Query1);
			}
			else if($status == 'inactive')
			{
				$data = array(
					'isactive' => 0,
					'emptemplock' => 1,
					'modifieddate' => gmdate("Y-m-d H:i:s"),
					'modifiedby' => $loginUserId
				);
			}
			$where = "id = ".$oldRM;
			$user_model =new Default_Model_Usermanagement();
			$result = $user_model->SaveorUpdateUserData($data, $where);
			
			if($status == 'inactive')
			{
				$empQuery1 = "UPDATE main_employees SET reporting_manager = ".$newRM.", modifieddate = '".gmdate("Y-m-d H:i:s")."', modifiedby = ".$loginUserId." WHERE reporting_manager=".$oldRM." and isactive = 1 AND user_id <> ".$newRM.";";
				
				$empQuery2 = "UPDATE main_employees SET reporting_manager = ".$oldRMData[0]['reporting_manager'].", modifieddate = '".gmdate("Y-m-d H:i:s")."', modifiedby = ".$loginUserId." WHERE reporting_manager=".$oldRM." and isactive = 1 AND user_id = ".$newRM.";";
				
				
				if($ishead == '1')
				{
					$orgQuery1 = "UPDATE main_employees SET is_orghead = 0,isactive = 0, reporting_manager= ".$newRM.", modifieddate = '".gmdate("Y-m-d H:i:s")."', modifiedby = ".$loginUserId." WHERE user_id=".$oldRM." ;";				
					$db->query($orgQuery1);
					
					$orgQuery2 = "UPDATE main_employees SET is_orghead = 1,reporting_manager= 0, modifieddate = '".gmdate("Y-m-d H:i:s")."', modifiedby = ".$loginUserId." WHERE user_id=".$newRM." ;";				
					$db->query($orgQuery2);
				}
				$db->query($empQuery1);
				$db->query($empQuery2);
			}
			$db->commit();
			return 'success';
		}
		catch(Exception $e)
		{			
			return 'failed';
			$db->rollBack();
		}
	}
	
	public function getEmployeesForOrgHead($userid = '')
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		if($userid == '')
		{
			$qry_str = " SELECT u.id, concat(u.userfullname,if(j.jobtitlename is null,'',concat(' , ',j.jobtitlename))) as name,u.profileimg 
								FROM main_users u 
								INNER JOIN main_roles r ON u.emprole = r.id 
								INNER JOIN main_employees e ON u.id = e.user_id 
								LEFT join main_jobtitles j on j.id = e.jobtitle_id 
								WHERE  r.group_id IN (".MANAGEMENT_GROUP.")  AND u.userstatus='old' AND u.isactive=1 AND r.isactive=1 order by name asc";
		}
		else
		{
			$qry_str = " SELECT u.id, concat(u.userfullname,if(j.jobtitlename is null,'',concat(' , ',j.jobtitlename))) as name,u.profileimg 
								FROM main_users u 
								INNER JOIN main_roles r ON u.emprole = r.id 
								INNER JOIN main_employees e ON u.id = e.user_id 
								LEFT join main_jobtitles j on j.id = e.jobtitle_id 
								WHERE  r.group_id IN (".MANAGEMENT_GROUP.")  AND u.userstatus='old' AND u.isactive=1 AND r.isactive=1 AND u.id <> ".$userid." order by name asc";
		}
		$reportingManagersData = $db->query($qry_str);
		$res = $reportingManagersData->fetchAll();
		return $res;
	}
	
	public function getEmployeesForServiceDesk($bunitid='',$deptid='')
	{
		$where = 'e.isactive=1 AND r.group_id IN (2,3,4,6)';
		if($bunitid != '' && $bunitid !='null')
			$where .= ' AND e.businessunit_id = '.$bunitid.'';
		if($deptid !='' && $deptid !='null')
			$where .= ' AND e.department_id = '.$deptid.'';	
		
		
		$db = Zend_Db_Table::getDefaultAdapter();
		$qry = "select e.userfullname,e.user_id,e.emprole from main_employees_summary e 
				left join main_roles r on r.id=e.emprole and r.isactive=1
				left join main_privileges p  on e.emprole = p.role  and p.isactive=1 and p.object = ".SERVICEDESK." where ".$where." ";
		$res = $db->query($qry)->fetchAll();
		return $res;
		
	}
	
	public function getApproverForServiceDesk($bunitid='',$deptid='',$empstring='')
	{
		$where = 'e.isactive=1 AND r.group_id =1';
			   
			   if($empstring !='' && $empstring !='null')
				$where.=' AND e.user_id NOT IN('.$empstring.')';	
		
		$db = Zend_Db_Table::getDefaultAdapter();
		/*** modified on 18-08-2015 ***
		*** to fix the issue when job title is empty ***
		*** query is returning empty userfullname ***
		***/
		$qry = "select case when e.jobtitle_name !='' then concat(e.userfullname,concat(' , ',e.jobtitle_name)) else e.userfullname end as userfullname,e.user_id,e.emprole from main_employees_summary e 
				inner join main_roles r on r.id=e.emprole and r.isactive=1
				inner join main_privileges p  on e.emprole = p.role  and p.isactive=1 and p.object = ".SERVICEDESK." where ".$where." group by e.id order by e.userfullname asc";
		
		
		$res = $db->query($qry)->fetchAll();
		return $res;
		
	}
	
	public function getEmployeeDetails($useridstring='')
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$emparr = array();
		if($useridstring !='' && $useridstring !='null')
		{
			$qry = "select e.userfullname,e.user_id,e.emprole from main_employees_summary e where e.user_id IN(".$useridstring.")";
			$res = $db->query($qry)->fetchAll();
		}
		if(!empty($res))
		{
			foreach($res as $resArr)
			{
				$emparr[$resArr['user_id']]= $resArr['userfullname'];
			}
		}
		return $emparr;
		
	}
	
	public function getIndividualEmpDetails($userid)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
			$qry = "select e.userfullname,e.user_id,e.emprole from main_employees_summary e where e.user_id = ".$userid." ";
			$res = $db->query($qry)->fetch();
		return $res;
		
	}
		public function getEmp_from_summary($userid)
		{
			$db = Zend_Db_Table::getDefaultAdapter();
			$qry = "select e.* from main_employees_summary e where e.user_id = ".$userid." ";
			$res = $db->query($qry)->fetch();
			return $res;		
	}
		
		public function getPrefix_emp_excel()
		{
			$db = Zend_Db_Table::getDefaultAdapter();
			$parray = array();
			$query = "select prefix,id from main_prefix where isactive = 1";
			$res = $db->query($query)->fetchAll();
			if(!empty($res))
			{
				foreach($res as $row)
				{
					$parray[strtolower($row['prefix'])] = $row['id'];
				}
			}
			return $parray;
		}
		
		public function getRoles_emp_excel()
		{
			$db = Zend_Db_Table::getDefaultAdapter();
			$parray = array();
			$query = "select roletype,id from main_roles where isactive = 1";
			$res = $db->query($query)->fetchAll();
			if(!empty($res))
			{
				foreach($res as $row)
				{
					$parray[strtolower($row['roletype'])] = $row['id'];
				}
			}
			return $parray;
		}
		
		public function getMngRoles_emp_excel()
		{
			$db = Zend_Db_Table::getDefaultAdapter();
			$parray = array();
			$query = "select roletype,id from main_roles where isactive = 1 and group_id = 1";
			$res = $db->query($query)->fetchAll();
			if(!empty($res))
			{
				foreach($res as $row)
				{                    
					$parray[$row['id']] = strtolower($row['roletype']);
				}
			}
			return $parray;
		}
		
		public function getBU_emp_excel()
		{
			$db = Zend_Db_Table::getDefaultAdapter();
			$parray = array();
			$query = "select unitcode,id from main_businessunits where isactive = 1";
			$res = $db->query($query)->fetchAll();
			if(!empty($res))
			{
				foreach($res as $row)
				{
					$parray[strtolower($row['unitcode'])] = $row['id'];
				}
			}
			return $parray;
		}
		
		public function getPayfrequency_excel()
		{
			$db = Zend_Db_Table::getDefaultAdapter();
			$parray = array();
			$query = "select id,freqtype from main_payfrequency where isactive = 1";
			$res = $db->query($query)->fetchAll();
			if(!empty($res))
			{
				foreach($res as $row)
				{
					$parray[strtolower($row['freqtype'])] = $row['id'];
				}
			}
			return $parray;
		}
		public function getCurrency_excel()
		{
			$db = Zend_Db_Table::getDefaultAdapter();
			$parray = array();
			$query = "select id,currencycode from main_currency where isactive = 1";
			$res = $db->query($query)->fetchAll();
			if(!empty($res))
			{
				foreach($res as $row)
				{
					$parray[strtolower($row['currencycode'])] = $row['id'];
				}
			}
			return $parray;
		}
		
		public function getDep_emp_excel()
		{
			$db = Zend_Db_Table::getDefaultAdapter();
			$parray = array();
			$query = "select deptname,id from main_departments where isactive = 1";
			$res = $db->query($query)->fetchAll();
			if(!empty($res))
			{
				foreach($res as $row)
				{
					$parray[strtolower($row['deptname'])] = $row['id'];
				}
			}
			return $parray;
		}
		
		public function getJobs_emp_excel()
		{
			$db = Zend_Db_Table::getDefaultAdapter();
			$parray = array();
			$query = "select jobtitlename,id from main_jobtitles where isactive = 1";
			$res = $db->query($query)->fetchAll();
			if(!empty($res))
			{
				foreach($res as $row)
				{
					$parray[strtolower($row['jobtitlename'])] = $row['id'];
				}
			}
			return $parray;
		}
		
		public function getPositions_emp_excel()
		{
			$db = Zend_Db_Table::getDefaultAdapter();
			$parray = array();
			$query = "select positionname,id from main_positions where isactive = 1";
			$res = $db->query($query)->fetchAll();
			if(!empty($res))
			{
				foreach($res as $row)
				{
					$parray[strtolower($row['positionname'])] = $row['id'];
				}
			}
			return $parray;
		}
		
		public function getUsers_emp_excel()
		{
			$db = Zend_Db_Table::getDefaultAdapter();
			$parray = array();
			$query = "select employeeId,user_id from main_employees_summary where isactive = 1";
			$res = $db->query($query)->fetchAll();
			if(!empty($res))
			{
				foreach($res as $row)
				{
					$parray[strtolower($row['employeeId'])] = $row['user_id'];
				}
			}
			return $parray;
		}
		
		public function getEstat_emp_excel()
		{
			$db = Zend_Db_Table::getDefaultAdapter();
			$parray = array();
			$query = "select workcode,workcodename from main_employmentstatus where isactive = 1";
			$res = $db->query($query)->fetchAll();
			if(!empty($res))
			{
				foreach($res as $row)
				{
					$parray[strtolower($row['workcode'])] = $row['workcodename'];
				}
			}
			return $parray;
		}
		public function getDOLEstat_emp_excel()
		{
			$db = Zend_Db_Table::getDefaultAdapter();
			$parray = array();
			$query = "select workcode,workcodename from main_employmentstatus where isactive = 1 and workcodename in (8,9,10)";
			$res = $db->query($query)->fetchAll();
			if(!empty($res))
			{
				foreach($res as $row)
				{
					$parray[$row['workcodename']] = strtolower($row['workcode']);
				}
			}
			return $parray;
		}
		
		public function getEmps_emp_excel()
		{
			$db = Zend_Db_Table::getDefaultAdapter();
			$earray = array();
			$iarray = array();
			$query = "select user_id,emailaddress,employeeId from main_employees_summary where isactive = 1";
			$res = $db->query($query)->fetchAll();
			if(!empty($res))
			{
				foreach($res as $row)
				{                    
					$iarray[$row['user_id']] = strtolower($row['employeeId']);
				}
			}
			
			$query = "select user_id,emailaddress,employeeId from main_employees_summary";
			$res = $db->query($query)->fetchAll();
			if(!empty($res))
			{
				foreach($res as $row)
				{
					$earray[$row['user_id']] = strtolower($row['emailaddress']);                    
				}
			}
			return array('email' => $earray,'ids' => $iarray);
		}
		
		public function getEmpsDeptWise()
		{
			$db = Zend_Db_Table::getDefaultAdapter();
			$emp_array = array();
			$query = "select ifnull(department_id,0) department_id,employeeId from main_employees_summary where isactive = 1";
			$res = $db->query($query)->fetchAll();
			if(!empty($res))
			{
				foreach($res as $row)
				{                    
					$emp_array[$row['department_id']][] = strtolower($row['employeeId']);
				}
			}
			return $emp_array;
		}
		
		public function getDeptBUWise()
		{
			$db = Zend_Db_Table::getDefaultAdapter();
			$emp_array = array();
			$query = "select unitid,deptname from main_departments where isactive = 1";
			$res = $db->query($query)->fetchAll();
			if(!empty($res))
			{
				foreach($res as $row)
				{                    
					$emp_array[$row['unitid']][] = strtolower($row['deptname']);
				}
			}
			return $emp_array;
		}
		
		public function getPosJTWise()
		{
			$db = Zend_Db_Table::getDefaultAdapter();
			$emp_array = array();
			$query = "select jobtitleid,positionname from main_positions where isactive =1 order by jobtitleid";
			$res = $db->query($query)->fetchAll();
			if(!empty($res))
			{
				foreach($res as $row)
				{                    
					$emp_array[$row['jobtitleid']][] = strtolower($row['positionname']);
				}
			}
			return $emp_array;
		}
		
		/**
		 * 
		 * Get count of employees reporing to manager
		 * @$manager_id interger - ID of reporting manager
		 */
		public function getCountEmpReporting($manager_id = "") {
			$db = Zend_Db_Table::getDefaultAdapter();
			$count_query = "select count(id) cnt from main_employees_summary e where e.isactive != 5 and reporting_manager = '$manager_id'";
			$count_result = $db->query($count_query);
			$count_row = $count_result->fetch();
			return $count_row['cnt'];        	
		}
		public function getMngmntEmployees()
		{
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "	select es.id,es.user_id,es.userfullname,es.emailaddress from main_employees_summary es where es.user_id in (
					select s.user_id from main_employees_summary s 
					inner join main_roles r on s.emprole = r.id where r.group_id = 1  and r.isactive=1 ) and es.isactive =1 ;";
		$data = $db->query($query)->fetchAll();
		return $data;
		
		}

	public function getCity_emp_excel(){
		$db = Zend_Db_Table::getDefaultAdapter();
		$parray = array();
		$query = "select city_org_id, city from main_cities where isactive = 1";
		$res = $db->query($query)->fetchAll();
		if(!empty($res)){
			foreach($res as $row){
				$parray[strtolower($row['city'])] = $row['city_org_id'];
			}
		}
		return $parray;
	}

	public function getState_emp_excel(){
		$db = Zend_Db_Table::getDefaultAdapter();
		$parray = array();
		$query = "select state_id_org, state from main_states where isactive = 1";
		$res = $db->query($query)->fetchAll();
		if(!empty($res)){
			foreach($res as $row){
				$parray[strtolower($row['state'])] = $row['state_id_org'];
			}
		}
		return $parray;
	}

	public function getCountry_emp_excel(){
		$db = Zend_Db_Table::getDefaultAdapter();
		$parray = array();
		$query = "select country_id_org, country from main_countries where isactive = 1";
		$res = $db->query($query)->fetchAll();
		if(!empty($res)){
			foreach($res as $row){
				$parray[strtolower($row['country'])] = $row['country_id_org'];
			}
		}
		return $parray;
	}

	public function getEducationLevel_emp_excel(){
		$db = Zend_Db_Table::getDefaultAdapter();
		$parray = array();
		$query = "select id, educationlevelcode from main_educationlevelcode where isactive = 1";
		$res = $db->query($query)->fetchAll();
		if(!empty($res)){
			foreach($res as $row){
				$parray[strtolower($row['educationlevelcode'])] = $row['id'];
			}
		}
		return $parray;
	}

	public function getMaritalStatus_emp_excel(){
		$db = Zend_Db_Table::getDefaultAdapter();
		$parray = array();
		$query = "select id, maritalcode from main_maritalstatus where isactive = 1";
		$res = $db->query($query)->fetchAll();
		if(!empty($res)){
			foreach($res as $row){
				$parray[strtolower($row['maritalcode'])] = $row['id'];
			}
		}
		return $parray;
	}

	public function getGender_emp_excel(){
		$db = Zend_Db_Table::getDefaultAdapter();
		$parray = array();
		$query = "select id, gendername from main_gender where isactive = 1";
		$res = $db->query($query)->fetchAll();
		if(!empty($res)){
			foreach($res as $row){
				$parray[strtolower($row['gendername'])] = $row['id'];
			}
		}
		return $parray;
	}

	public function getWorkStatus_emp_excel(){
		$db = Zend_Db_Table::getDefaultAdapter();
		$parray = array();
		$query = "select id, name from main_empworkstatus";
		$res = $db->query($query)->fetchAll();
		if(!empty($res)){
			foreach($res as $row){
				$parray[strtolower($row['name'])] = $row['id'];
			}
		}
		return $parray;
	}

	public function addOtherDetails ($project_status, $functional_area) {
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$data = array('emp_project_status'=>$project_status,'functional_area'=>$functional_area);
		$where = " user_id = $loginUserId";
		$this->update($data, $where);
	}

	public function addProjects($projects) {
		$existingProjects = array();
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$userid = $auth->getStorage()->read()->id;
		}
		foreach($this->getMyProjects() as $myProj) {
			if(!in_array($myProj['project_id'], $projects)){
				//make inactive from db aju
				$db = Zend_Db_Table::getDefaultAdapter();
                $query = "update main_employee_projects  set isactive=0 where project_id = " . $myProj['project_id'] . " and user_id = " . $userid; 
                $db->query($query);
			} else {
				$existingProjects[] = $myProj['project_id'];
			}
		} 
		$previousProjects = array();
		foreach($this->getMyPreviousProjects() as $arr) {
			$previousProjects[] = $arr['project_id'];
		} 
		foreach($projects as $proj) {
			if(!in_array($proj, $existingProjects)) {
				if(!(in_array($proj, $previousProjects))) {
					//insert
					$db = Zend_Db_Table::getDefaultAdapter();
	                $query = "insert into main_employee_projects (project_id, user_id) values ($proj, $userid) ";//exit;
	                $db->query($query);
				} else {
					//update//
					$db = Zend_Db_Table::getDefaultAdapter();
	                $query = "update main_employee_projects  set isactive=1 where project_id = " . $proj . " and user_id = " . $userid; 
	                $db->query($query);
				}
			}
		}
	}
	public function getMyProjects() {
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$id = $auth->getStorage()->read()->id;
		}
		$select = $this->select()
						->setIntegrityCheck(false)
						->from(array('mep' => 'main_employee_projects'))
						->where("mep.user_id = {$id} and mep.isactive = 1");
					
		return $this->fetchAll($select)->toArray();
	}
	public function getMyPreviousProjects() {
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$id = $auth->getStorage()->read()->id;
		}
		$select = $this->select()
						->setIntegrityCheck(false)
						->from(array('mep' => 'main_employee_projects'))
						->where("mep.user_id = {$id} and mep.isactive = 0");
					
		return $this->fetchAll($select)->toArray();
	}

	public function getEmployeeDept($userId)
		{
			$db = Zend_Db_Table::getDefaultAdapter();
			$parray = array();
			$query = "select id, department_id from main_employees where user_id =" . $userId;
			$res = $db->query($query)->fetchAll();
			if(!empty($res))
			{
				foreach($res as $row){
					$parray[strtolower($row['id'])] = $row['department_id'];
				}
			}
			return $parray;
		}
	public function changeReportingManager ($employee_id, $new_manager_id) {
		$db = Zend_Db_Table::getDefaultAdapter();
        $query = "update main_employees  set  reporting_manager = $new_manager_id where user_id = $employee_id and isactive = 1";
        $db->query($query);
        return true;
	}

	public function changeReporteeLeaves ($employee_id, $new_manager_id, $old_manager_id) {
		$db = Zend_Db_Table::getDefaultAdapter();
        $query = "update main_leaverequest  set  rep_mang_id = $new_manager_id where rep_mang_id = $old_manager_id and user_id = $employee_id and leavestatus = 1 and isactive = 1";
        $db->query($query);
        $query = "SELECT ROW_COUNT() as updated_rows";
        $data = $db->query($query)->fetchAll();
        return $data[0]['updated_rows'];
	}

	public function getAllEmployeeType () {
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "SELECT * from main_employee_type where isactive = 1"; 
        $data = $db->query($query)->fetchAll();
        $result = array();
        foreach ($data as $key => $value) {
        	$result[$value['id']] = $value['name'];
        }
        return $result;
	}
}
?>