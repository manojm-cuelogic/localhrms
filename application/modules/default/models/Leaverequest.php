<?php

class Default_Model_Leaverequest extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_leaverequest';
    
	public function getPendingLeaves($loginUserId,$leavetypeid) {
		$select = $this->select()
    					   ->setIntegrityCheck(false)	
                           ->from(array('l'=>'main_leaverequest'),array('unapproved_leaves_count'=>new Zend_Db_Expr('count(l.appliedleavescount)')))
						   ->where('l.user_id='.$loginUserId.' AND l.leavetypeid='.$leavetypeid.' AND l.isactive = 1 and l.leavestatus = 1');  		   					   				
		return $this->fetchAll($select)->toArray();
	}
	
	public function getAvailableLeaves($loginUserId,$leavetypeid)
	{
	 	 $select = $this->select()
    					   ->setIntegrityCheck(false)	
                           ->from(array('e'=>'main_employeeleaves'),array('leavelimit'=>'e.emp_leave_limit','remainingleaves'=>new Zend_Db_Expr('if((e.emp_leave_limit - e.used_leaves) > 0, (e.emp_leave_limit - e.used_leaves), 0)')))
						   ->where('e.user_id='.$loginUserId.' AND e.leavetypeid='.$leavetypeid.' AND e.alloted_year = now() AND e.isactive = 1');  		   					   				
		
		
		return $this->fetchAll($select)->toArray();   
	
	}

	/*
		To check for Leave balance avialable for that particular Users
	*/

	public function getLeaves($loginUserId)
	{

		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "SELECT sum(emp_leave_limit) as value_sum FROM `main_employeeleaves` where  user_id = '$loginUserId'";
		
        $result = $db->query($query)->fetchAll();
        
        $sum = $result[0]['value_sum'];
     	
	    return $sum;

	}	
	
	
	public function getsinglePendingLeavesData($id)
	{
		$result =  $this->select()
    				->setIntegrityCheck(false) 	
    				->from(array('l'=>'main_leaverequest'),array('l.*'))
 	  				->where("l.isactive = 1 AND l.id = ".$id);
	
    	return $this->fetchAll($result)->toArray();
	}
	
	public function getUserLeavesData($id)
	{
		$result =  $this->select()
    				->setIntegrityCheck(false) 	
    				->from(array('l'=>'main_leaverequest'),array('l.*'))
 	  				->where("l.isactive = 1 AND l.user_id = ".$id);
		
    	return $this->fetchAll($result)->toArray();
	}
	
	public function getPendingLeavesBetweenDate ($fromDate, $toDate) {
		echo $result =  $this->select()
    				->setIntegrityCheck(false) 	
    				->from(array('l'=>'main_leaverequest'),array('l.*'))
 	  				->where("l.isactive = 1 AND l.leavestatus = 1 and from_date between '" . $fromDate . "' and '" . $toDate . "'");
		
    	return $this->fetchAll($result)->toArray();
	}

	public function getUserApprovedOrPendingLeavesData($id)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
       
		
		$query = "SELECT `l`.* FROM `main_leaverequest` AS `l` WHERE (l.isactive = 1 AND l.user_id = '$id' and l.leavestatus IN(1,2))";
		
        $result = $db->query($query)->fetchAll();



	    return $result;
	}
	
	public function getManagerApprovedOrPendingLeavesData($id)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
       
		
		$query = "SELECT `l`.*,u.userfullname FROM `main_leaverequest` AS `l`
				 left join main_users u on u.id=l.user_id 
				 WHERE (l.isactive = 1 AND l.rep_mang_id = '$id' and l.leavestatus IN(1,2))";
		
        $result = $db->query($query)->fetchAll();

	    return $result;
	}
	
	public function getReportingManagerId($id)
	{
	    $result =  $this->select()
    				->setIntegrityCheck(false) 	
    				->from(array('l'=>'main_leaverequest'),array('repmanager'=>'l.rep_mang_id'))
 	  				->where("l.isactive = 1 AND l.id = ".$id);
	
    	return $this->fetchAll($result)->toArray();
	}
	
	public function SaveorUpdateLeaveRequest($data, $where)
	{
		
	    if($where != '')
		{
			$this->update($data, $where);
			return 'update';
		}
		else
		{
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_leaverequest');
			return $id;
		}
	}
	
	public function getLeaveStatusHistory($sort, $by, $pageNo, $perPage,$searchQuery,$queryflag='',$loggedinuser,$managerstring='')
	{	//die($by);
	    $auth = Zend_Auth::getInstance();
			if($auth->hasIdentity()){
				$loginUserId = $auth->getStorage()->read()->id;
		}  
		if($loggedinuser == '') 
		 $loggedinuser = $loginUserId;
		if($by == 'leave_from_date') $by = 'from_date';
		else if($by == 'leave_to_date') $by = 'to_date';
		else if($by == 'leave_applieddate') $by = 'l.createddate';


		/* Removing isactive checking from configuration table */ 
		if($managerstring !='')
		{
		  
		  $where = "l.isactive = 1 ";
		}  
		else 
        {		
	      
		  $where = "l.isactive = 1 AND l.user_id = ".$loggedinuser." ";
		}  
		if($queryflag !='')
		{
		   if($queryflag == 'pending')
		   {
		     $where .=" AND l.leavestatus = 1 ";
		   }
		   else if($queryflag == 'approved')
		   {
		     $where .=" AND (l.leavestatus = 2 or l.leavestatus = 5)";
		   }
		   else if($queryflag == 'cancel')
		   {
		     $where .=" AND l.leavestatus = 4 ";
		   }
		   else if($queryflag == 'rejected')
		   {
		     $where .=" AND l.leavestatus = 3 ";
		   }
		
		}else
		{
		  $where .=" AND l.leavestatus = 2 ";
		}
		
			
		if($searchQuery)
			$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();		
		//die("exit");
		$leaveStatusData = $this->select()
    					   ->setIntegrityCheck(false)	
                           ->from(array('l'=>'main_leaverequest'),
						          array( 'l.*','leave_from_date'=>'DATE_FORMAT(l.from_date,"%d %b %Y")',
								         'leave_to_date'=>'DATE_FORMAT(l.to_date,"%d %b %Y")',
										 'leave_applieddate'=>'DATE_FORMAT(l.createddate,"%d %b %Y")',
                                         'leaveday'=>'if(l.leaveday = 1,"Full Day","Half Day")', 										 
								       ))
						   ->joinLeft(array('et'=>'main_employeeleavetypes'), 'et.id=l.leavetypeid',array('leavetype'=>'et.leavetype'))	
                           ->joinLeft(array('u'=>'main_users'), 'u.id=l.rep_mang_id',array('reportingmanagername'=>'u.userfullname'))
                           ->joinLeft(array('mu'=>'main_users'), 'mu.id=l.user_id',array('employeename'=>'mu.userfullname'))						                 			   						   
						   ->where($where)
    					   ->order("$by $sort") 
    					   ->limitPage($pageNo, $perPage);
		
		
		return $leaveStatusData;
		
	}
	
	
	public function getEmployeeLeaveRequest($sort, $by, $pageNo, $perPage,$searchQuery,$loginUserId)
	{	
		$where = "l.isactive = 1  AND u.isactive=1 AND l.rep_mang_id=".$loginUserId." ";
		if($by == 'leave_from_date') $by = 'from_date';
		else if($by == 'leave_to_date') $by = 'to_date';
		else if($by == 'leave_applieddate') $by = 'l.createddate';
		//Removed the condition from above  AND l.leavestatus=1 
		if($searchQuery)
			$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$employeeleaveData = $this->select()
    					   ->setIntegrityCheck(false)	
                           ->from(array('l'=>'main_leaverequest'),
						          array( 'l.*','leave_from_date'=>'DATE_FORMAT(l.from_date,"'.DATEFORMAT_MYSQL.'")',
								         'leave_to_date'=>'DATE_FORMAT(l.to_date,"'.DATEFORMAT_MYSQL.'")',
										 'leave_applieddate'=>'DATE_FORMAT(l.createddate,"'.DATEFORMAT_MYSQL.'")',
                                         'leaveday'=>'if(l.leaveday = 1,"Full Day","Half Day")', 										 
								       ))
						   ->joinLeft(array('et'=>'main_employeeleavetypes'), 'et.id=l.leavetypeid',array('leavetype'=>'et.leavetype'))	
						   ->joinLeft(array('u'=>'main_users'), 'u.id=l.user_id',array('userfullname'=>'u.userfullname'))						   						 		   						   
						   ->where($where)
    					   ->order("$by $sort") 
    					   ->limitPage($pageNo, $perPage);
		return $employeeleaveData;       		
	}
	
	public function updateemployeeleaves($appliedleavescount,$employeeid,$leavetypeid)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		
        $qry = "update main_employeeleaves  
		set used_leaves = used_leaves+".$appliedleavescount." where user_id = ".$employeeid." AND leavetypeid=".$leavetypeid." 
		AND alloted_year = year(now()) AND isactive = 1 ";


		print_r($qry);exit;
		$db->query($qry);		
	
	}
	/*Richa code start
	function to increase leavecount on approved leave cancellation*/

	public function updateemployeeleavesoncancellation($appliedleavescount,$employeeid,$leavetypeid)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$db->query("update main_employeeleaves  set used_leaves = used_leaves-".$appliedleavescount." where user_id = ".$employeeid." AND leavetypeid=".$leavetypeid." AND alloted_year = year(now()) AND isactive = 1 ");		
	
	}
	/*Richa code end*/
	
	public function getUserID($id)
    {
    	$result =  $this->select()
    				->setIntegrityCheck(false) 	
    				->from(array('l'=>'main_leaverequest'),array('l.user_id'))
 	  				->where("l.isactive = 1 AND l.id = ".$id);
	
    	return $this->fetchAll($result)->toArray();
    }
	
	public function getLeaveRequestDetails($id)
    {
    	$result =  $this->select()
    				->setIntegrityCheck(false) 	
    				->from(array('l'=>'main_leaverequest'),array('l.*'))
 	  				->where("l.isactive = 1 AND l.id = ".$id);
	
    	return $this->fetchAll($result)->toArray();
    }
	
	public function checkdateexists($from_date, $to_date,$loginUserId)
	{
	    $db = Zend_Db_Table::getDefaultAdapter();
        
		
		$query = "select count(l.id) as dateexist from main_leaverequest l where l.user_id=".$loginUserId." and l.leavestatus IN(1,2) and l.isactive = 1
        and (l.from_date between '".$from_date."' and '".$to_date."' OR l.to_date between '".$from_date."' and '".$to_date."' )";
		
        $result = $db->query($query)->fetchAll();
	    return $result;
	
	}
	
	/* This function is common to manager employee leaves, employee leaves , approved,cancel,pending and rejected leaves
       Here differentiation is done based on objname. 
    */	   
	public function getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$objName,$queryflag,$unitId='',$statusidstring='', $loginUserId = '')
	{	
        $auth = Zend_Auth::getInstance();
     	if($loginUserId == "" && $auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
		}	
        $searchQuery = '';
        $searchArray = array();
        $data = array();
		if($objName == 'manageremployeevacations')
		{
		       if($searchData != '' && $searchData!='undefined')
				{
					$searchValues = json_decode($searchData);
					foreach($searchValues as $key => $val)
					{
						if($key == 'applieddate')
						 $searchQuery .= " l.createddate like '%".  sapp_Global::change_date($val,'database')."%' AND ";	
						else if($key == 'from_date' || $key == 'to_date')
						{
							$searchQuery .= " ".$key." like '%".  sapp_Global::change_date($val,'database')."%' AND ";
						} 
						else 
						  $searchQuery .= " ".$key." like '%".$val."%' AND ";
						$searchArray[$key] = $val;
					}
					$searchQuery = rtrim($searchQuery," AND");					
				}
				
				$tableFields = array('action'=>'Action','userfullname' => 'Employee name','leavetype' => 'Leave Type',
                    'leaveday' => 'Leave Duration','leave_from_date' => 'From Date','leave_to_date' => 'To Date','reason' => 'Reason',
                    'approver_comments' => 'Manager Comments','requester_comments' => 'Employee Comments','leavestatus' => 'Status','appliedleavescount' => 'Leave Count','leave_applieddate' => 'Applied On');
		
		        $leave_arr = array('' => 'All',1 =>'Full Day',2 => 'Half Day');

                $tablecontent = $this->getEmployeeLeaveRequest($sort, $by, $pageNo, $perPage,$searchQuery,$loginUserId);     				
				$dataTmp = array(
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
					'add' =>'add',
					'call'=>$call,
					'dashboardcall'=>$dashboardcall,
								'search_filters' => array(
									'from_date' =>array('type'=>'datepicker'),
									'to_date' =>array('type'=>'datepicker'),
									'applieddate'=>array('type'=>'datepicker'),
									'leaveday' => array(
										'type' => 'select',
										'filter_data' => $leave_arr,
									),
								)
				);
		}
		else if($objName == 'empleavesummary')
		{

		        $managerstring= "true";
				 
		         if($searchData != '' && $searchData!='undefined')
					{
						$searchValues = json_decode($searchData);
						foreach($searchValues as $key => $val)
						{
						  if($key !='leavestatus')
						  {
							if($key == 'reportingmanagername')
							 $searchQuery .= " u.userfullname like '%".$val."%' AND ";
							else if($key == 'employeename')
							 $searchQuery .= " mu.userfullname like '%".$val."%' AND "; 
							else if($key == 'applieddate')
							{
							$searchQuery .= " l.createddate  like '%".  sapp_Global::change_date($val,'database')."%' AND ";
							}
							else if($key == 'from_date' || $key == 'to_date')
							{
								$searchQuery .= " ".$key." like '%".  sapp_Global::change_date($val,'database')."%' AND ";
							}
							else 
							 $searchQuery .= " ".$key." like '%".$val."%' AND ";
							
							}
							$searchArray[$key] = $val;
						}
						$searchQuery = rtrim($searchQuery," AND");					
					}
					
				    $statusid = '';				
			        if($queryflag !='')
					{
					   $statusid = $queryflag;
						   if($statusid == 1)
							  $queryflag = 'pending';
						   else if($statusid == 2)
							  $queryflag = 'approved'; 
						   else if($statusid == 3)
							  $queryflag = 'rejected'; 
						   else if($statusid == 4)
							  $queryflag = 'cancel'; 
					}
					else
					{
						$queryflag = 'approved';
					}
					

            $tableFields = array('action'=>'Action','employeename' => 'Leave Applied By','leavetype' => 'Leave Type','leaveday' => 'Leave Duration','leave_from_date' => 'From Date','leave_to_date' => 'To Date','reason' => 'Reason','approver_comments' => 'Manager Comments','requester_comments' => 'Employee Comments','reportingmanagername'=>'Reporting Manager','appliedleavescount' => 'Leave Count','leave_applieddate' => 'Applied On');						 
				 
			$leave_arr = array('' => 'All',1 =>'Full Day',2 => 'Half Day');	 
			
			$search_filters = array(
										'from_date' =>array('type'=>'datepicker'),
										'to_date' =>array('type'=>'datepicker'),
										'applieddate'=>array('type'=>'datepicker'),
										'leaveday' => array(
															'type' => 'select',
															'filter_data' => $leave_arr,
														),
										);
										
			
            /* This is for dashboard call.
               Here one additional column Status is build by passing it to table fields
            */ 			   
			if($dashboardcall == 'Yes')
            {
					$tableFields['leavestatus'] = "Status";
					$search_filters['leavestatus'] = array(
					'type' => 'select',
					'filter_data' => array('pending' => 'Pending for approval','approved'=>'Approved','rejected'=>'Rejected','cancel'=>'Cancelled',),
				);
				if(isset($searchArray['leavestatus']))
				{
					$queryflag = $searchArray['leavestatus'];
					 if($queryflag =='')
					 {
						$queryflag = 'pending';
					 }	
				}
				
			}
			
			$tablecontent = $this->getLeaveStatusHistory($sort, $by, $pageNo, $perPage,$searchQuery,$queryflag,$loginUserId,$managerstring);    
			
			
			if(isset($queryflag) && $queryflag != '') 
		      $formgrid = 'true';
			else 
		      $formgrid = '';  
			  
			$dataTmp = array(
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
				'add' =>'add',
				'formgrid' => $formgrid,
				'unitId'=>sapp_Global::_encrypt($statusid),
				'call'=>$call,
				'dashboardcall'=>$dashboardcall,
				'search_filters' => $search_filters
			);
		
		}
		else
		{

				if($searchData != '' && $searchData!='undefined')
					{
						$searchValues = json_decode($searchData);
						foreach($searchValues as $key => $val)
						{
							if($key == 'reportingmanagername')
							 $searchQuery .= " u.userfullname like '%".$val."%' AND ";					
							else if($key == 'applieddate')
							{
								
								$searchQuery .= " l.createddate  like '%".  sapp_Global::change_date($val,'database')."%' AND ";
							}
							else if($key == 'from_date' || $key == 'to_date')
							{
								$searchQuery .= " ".$key." like '%".  sapp_Global::change_date($val,'database')."%' AND ";
							}
							else 
							 $searchQuery .= " ".$key." like '%".$val."%' AND ";
							$searchArray[$key] = $val;
						}
						$searchQuery = rtrim($searchQuery," AND");					
					}
				
				
				if($queryflag == 'approved'){

				$tableFields = array('action'=>'Action','leavetype' => 'Leave Type','leavestatus' => 'Status','leaveday' => 'Leave Duration',
							'leave_from_date' => 'From Date','leave_to_date' => 'To Date','reason' => 'Reason','approver_comments' => 'Manager Comments','requester_comments' => 'Employee Comments',
							"reportingmanagername"=>"Reporting Manager",'appliedleavescount' => 'Leave Count',
							'leave_applieddate' => 'Applied On');
				}else{
					$tableFields = array('action'=>'Action','leavetype' => 'Leave Type','leaveday' => 'Leave Duration',
							'leave_from_date' => 'From Date','leave_to_date' => 'To Date','reason' => 'Reason','approver_comments' => 'Manager Comments','requester_comments' => 'Employee Comments',
							"reportingmanagername"=>"Reporting Manager",'appliedleavescount' => 'Leave Count',
							'leave_applieddate' => 'Applied On');
				}
				$leave_arr = array('' => 'All',1 =>'Full Day',2 => 'Half Day');	
				$tablecontent = $this->getLeaveStatusHistory($sort, $by, $pageNo, $perPage,$searchQuery,$queryflag,$loginUserId);    
				
				$dataTmp = array(
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
					'add' =>'add',
					'call'=> $call,
					'dashboardcall'=>$dashboardcall,
					'search_filters' => array(
									'from_date' =>array('type'=>'datepicker'),
									'to_date' =>array('type'=>'datepicker'),
									'applieddate'=>array('type'=>'datepicker'),
									'leaveday' => array(
										'type' => 'select',
										'filter_data' => $leave_arr,
									),
								)
				);
        }
		
		return $dataTmp;
	}
	
	public function getUsersAppliedLeaves($userId)
	{
		$result =  $this->select()
    				->setIntegrityCheck(false) 	
    				->from(array('l'=>'main_leaverequest'),array('l.from_date','l.to_date'))
 	  				->where("l.isactive = 1 AND l.user_id = ".$userId." AND l.leavestatus IN(1,2)");
		
    	return $this->fetchAll($result)->toArray();
	}
	
	public function checkLeaveExists($applied_from_date,$applied_to_date,$from_date, $to_date,$loginUserId)
	{
	    $db = Zend_Db_Table::getDefaultAdapter();
        
		
		$query = "select count(l.id) as leaveexist from main_leaverequest l where l.user_id=".$loginUserId." and l.leavestatus IN(1,2) and l.isactive = 1
        and ('".$from_date."' between '".$applied_from_date."' and '".$applied_to_date."'
         OR '".$to_date."' between '".$applied_from_date."' and '".$applied_to_date."' )";
		
        $result = $db->query($query)->fetchAll();
	    return $result;
	
	}

	/*Richa code start
	defining function to get users total used leaves of current year */

	public function employeeUsedLeaves($userId,$leavetypeid)
	{
	    $db = Zend_Db_Table::getDefaultAdapter();
        
		
		 $query = "select count(l.appliedleavescount) as usedleaves from main_leaverequest l where l.user_id=".$userId." and l.leavestatus = 2 and l.isactive = 1 and  l.leavetypeid=".$leavetypeid." and ('from_date between '".date('Y-1-1')."' and '".date('Y-m-d')."' OR 'to_date' between '".date('Y-1-1')."' and '".date('Y-m-d')."' )";
		//die();
        $result = $db->query($query)->fetchAll();
	    return (isset($result[0]['usedleaves'])?$result[0]['usedleaves']:0);
	
	}

	/*Richa code end*/
}