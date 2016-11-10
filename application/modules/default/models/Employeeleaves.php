<?php

class Default_Model_Employeeleaves extends Zend_Db_Table_Abstract
{
	protected $_name = 'main_employeeleaves';
    protected $_primary = 'id';
	
	public function getEmpLeavesData($sort, $by, $pageNo, $perPage,$searchQuery,$id)
	{
		$where = " e.user_id = ".$id." AND e.isactive = 1";
		if($searchQuery)
			$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$empskillsData = $this->select()
    					   ->setIntegrityCheck(false)	 
						   ->from(array('e' => 'main_employeeleaves'),
						   	array('id'=>'e.id','emp_leave_limit'=>'e.emp_leave_limit',
						   		'pending_leaves' => '( select count(ml.appliedleavescount) from main_leaverequest ml where ml.leavestatus = 1 and  ml.leavetypeid  = e.leavetypeid and ml.user_id =  e.user_id AND YEAR( STR_TO_DATE( ml.from_date,  "%Y" ) ) = YEAR( NOW() ))',
						   		'used_leaves' => '( select if(sum(ml.appliedleavescount) > 0 , sum(ml.appliedleavescount) , 0 )  from main_leaverequest ml where ml.leavestatus = 2 and  ml.leavetypeid  = e.leavetypeid and ml.user_id =  e.user_id AND YEAR( STR_TO_DATE( ml.from_date,  "%Y" ) ) = YEAR( NOW( ) ))',
						   		'leavetypeid'=>'lt.leavetype', 
						   		'remainingleaves'=> '(e.emp_leave_limit - e.used_leaves)',
                                    //new Zend_Db_Expr('if((e.emp_leave_limit - e.used_leaves) > 0, (e.emp_leave_limit - e.used_leaves), (e.emp_leave_limit - e.used_leaves))'),
						   		'e.alloted_year'))
						   ->join(array('lt' => 'main_employeeleavetypes'),'e.leavetypeid = lt.id', 
						   	array('leavetypeid'=>'lt.leavetype'))
						   ->where($where)
    					   ->order("$by $sort") 
    					   ->limitPage($pageNo, $perPage); //exit;
		return $empskillsData;       		
	}
	
	public function getsingleEmployeeleaveData($id)
	{
		$select = $this->select()
						->setIntegrityCheck(false)
						->from(array('el'=>'main_employeeleaves'),array('el.*'))
						->where('el.user_id='.$id.' AND el.isactive = 1 AND el.alloted_year = year(now())');
		$data = $this->fetchAll($select)->toArray();
		if (empty($data)) {$data = array(array('leave_count' => 0));}
		return $data;

		//return $this->fetchAll($select)->toArray();
	}
	
	public function getPreviousYearEmployeeleaveData($id)
	{
		$select = $this->select()
						->setIntegrityCheck(false)
						->from(array('el'=>'main_employeeleaves'),array('el.*','remainingleaves'=>new Zend_Db_Expr('el.emp_leave_limit - el.used_leaves')))
						->where('el.user_id='.$id.' AND el.isactive = 1 AND el.alloted_year = year(now())-1');
		
		return $this->fetchAll($select)->toArray();
	}
	
	public function getsingleEmpleavesrowWithUsedLeaves($id)
	{
		$where = " e.id = ".$id;
		$select = $this->select()
    					   ->setIntegrityCheck(false)	 
						   ->from(array('e' => 'main_employeeleaves'),
						   	array('id'=>'e.id','emp_leave_limit'=>'e.emp_leave_limit','user_id'=>'e.user_id',
						   		'used_leaves' => '( select if(sum(ml.appliedleavescount) > 0 , sum(ml.appliedleavescount) , 0 )  from main_leaverequest ml where ml.leavestatus = 2 and  ml.leavetypeid  = e.leavetypeid and ml.user_id =  e.user_id AND YEAR( STR_TO_DATE( ml.from_date,  "%Y" ) ) = YEAR( NOW( ) ))',
						   		'leavetypeid'=>'e.leavetypeid', 
						   		'remainingleaves'=>new Zend_Db_Expr('e.emp_leave_limit - e.used_leaves'),
						   		'e.alloted_year'))
						   ->join(array('lt' => 'main_employeeleavetypes'),'e.leavetypeid = lt.id', 
						   	array('leavetypename'=>'lt.leavetype'))
						   ->where($where);
		$row = $this->fetchAll($select)->toArray();
		if (!$row) {
			throw new Exception("Could not find row $id");
		}
		return (isset($row[0]) ? $row[0] : array());
	}

	public function getsingleEmpleavesrow($id)
	{
		$row = $this->fetchRow("id = '".$id."'");
		if (!$row) {
			throw new Exception("Could not find row $id");
		}
		return $row->toArray();
	}
	
	public function SaveorUpdateEmpLeaves($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_employeeleaves');
			return $id;
		}
		
	}
	
	public function SaveorUpdateEmployeeLeaves($user_id,$emp_leave_limit,$leavetypeid,$isleavetrasnfer,$loginUserId)
	{
		$date= gmdate("Y-m-d H:i:s");
	   
	    $db = Zend_Db_Table::getDefaultAdapter();
	    $rows = $db->query("INSERT INTO `main_employeeleaves` (user_id,emp_leave_limit,leavetypeid,used_leaves,alloted_year,createdby,modifiedby,createddate,modifieddate,isactive,isleavetrasnferset) VALUES (".$user_id.",".$emp_leave_limit.",".$leavetypeid.",'0',year(now()),".$loginUserId.",".$loginUserId.",'".$date."','".$date."',1,".$isleavetrasnfer.") ON DUPLICATE KEY UPDATE emp_leave_limit='".$emp_leave_limit."',modifiedby=".$loginUserId.",modifieddate='".$date."',isactive = 1,isleavetrasnferset=".$isleavetrasnfer." ");				
		$id=$this->getAdapter()->lastInsertId('main_employeeleaves');
		return $id;
		
	
	}
	
	public function saveallotedleaves_normalquery($postedArr,$totLeaves,$userid,$loginUserId)
	{
	    $date= gmdate("Y-m-d H:i:s");
		$db = Zend_Db_Table::getDefaultAdapter();		
	 	$rows = $db->query("INSERT INTO main_allottedleaveslog (userid,assignedleaves,totalleaves,year,
				createdby,modifiedby,createddate,modifieddate) VALUES (".$userid.",".$postedArr['leave_limit'].",".$totLeaves.",".$postedArr['alloted_year'].",
				".$loginUserId.",".$loginUserId.",'".$date."','".$date."');");		
		
		$id = $this->getAdapter()->lastInsertId('main_allottedleaveslog');
		return $id;
	}
	
    public function saveallotedleaves($postedArr,$totLeaves,$userid,$loginUserId)
	{
	    $allotedLeavesDetailsmodel = new Default_Model_Allottedleaveslog();
	    $date= gmdate("Y-m-d H:i:s");
	    $data = array(
			'userid' => $userid,
			'assignedleaves' => !empty($postedArr['leave_limit'])?$postedArr['leave_limit']:$totLeaves,
	        'totalleaves' => $totLeaves,
	        'year' => !empty($postedArr['alloted_year'])?$postedArr['alloted_year']:date("Y"),
	        'createdby' => $loginUserId,
	        'modifiedby' => $loginUserId,
	        'createddate' => $date,
			'modifieddate' => $date
		);
		$id = $allotedLeavesDetailsmodel->SaveorUpdateAllotedLeavesDetails($data,'');	
		return $id;
	}
	
	public function getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$exParam1='',$exParam2='',$exParam3='',$exParam4='')
	{		
        $searchQuery = '';$tablecontent = '';$level_opt = array();
        $searchArray = array();$data = array();$id='';
        $dataTmp = array();
		if($searchData != '' && $searchData!='undefined')
		{
			$searchValues = json_decode($searchData);
			foreach($searchValues as $key => $val)
			{
				if($key == 'remainingleaves')
					$searchQuery .= "  e.emp_leave_limit - e.used_leaves   like '%".$val."%' AND ";
				else
					$searchQuery .= " ".$key." like '%".$val."%' AND ";
				$searchArray[$key] = $val;
			}
			$searchQuery = rtrim($searchQuery," AND");					
		}
		/** search from grid - END **/
		$objName = 'empleaves';
		$tableFields = array('action'=>'Action','leavetypeid'=>'Leave Type','used_leaves'=>'Leaves Availed','remainingleaves'=>'Leave Balance','alloted_year'=>'Allotted Year');
		
		$tablecontent = $this->getEmpLeavesData($sort, $by, $pageNo, $perPage,$searchQuery,$exParam1);  

		$dataTmp = array('userid'=>$exParam1, 
						'sort' => $sort,
						'by' => $by,
						'pageNo' => $pageNo,
						'perPage' => $perPage,				
						'tablecontent' => $tablecontent,
						'objectname' => $objName,
						'extra' => array(),
						'tableheader' => $tableFields,
						'jsGridFnName' => 'getEmployeeAjaxgridData',
						'jsFillFnName' => '',
						'searchArray' => $searchArray,
						'add'=>'add',
						'menuName'=>'Leaves',
						'formgrid'=>'true',
						'unitId'=>$exParam1,
						'dashboardcall'=>$dashboardcall,
						'call'=>$call,
						'context'=>$exParam2
				);		
		return $dataTmp;
	}
	/*Richa code start
	Get employee available leave count of selected leave type*/
	public function getsingleEmployeeleavetypeCount($id,$leavetypeid)
	{
		
			$select = $this->select()
						->setIntegrityCheck(false)
						->from(array('el'=>'main_employeeleaves'),array('(el.emp_leave_limit - el.used_leaves) as leave_count'))
						->where('el.user_id='.$id.' AND el.leavetypeid='.$leavetypeid.' AND el.isactive = 1 AND el.alloted_year = year(now())');
 
	
		
			return $this->fetchAll($select)->toArray();
	}
	public function getCompanyHolidays($year = 0) {
		if($year == 0) $year = date('Y');
		$db = Zend_Db_Table::getDefaultAdapter();
		$h_query = "select * from main_holidaydates where holidayyear = " . $year; 
        $h_result = $db->query($h_query);
        $h_rows = $h_result->fetchAll();
        return $h_rows;
	}

	public function getEmployeeLeaveBalance ($leavetypeid){
		$db = Zend_Db_Table::getDefaultAdapter();
        $select = "select mel.user_id, mel.leavetypeid, mu.employeeId, mu.userfullname, (mel.emp_leave_limit - mel.used_leaves) as leave_count from main_employeeleaves mel left join main_users mu on mu.id = mel.user_id where mel.leavetypeid=".$leavetypeid." AND mu.isactive = 1 AND mel.isactive = 1 AND mel.alloted_year = year(now())"; //exit;
		//$select = "select mel.user_id, mel.leavetypeid, mu.employeeId, mu.userfullname, if((mel.emp_leave_limit - mel.used_leaves) >= 0,(mel.emp_leave_limit - mel.used_leaves),0) as leave_count from main_employeeleaves mel left join main_users mu on mu.id = mel.user_id where mel.leavetypeid=".$leavetypeid." AND mu.isactive = 1 AND mel.isactive = 1 AND mel.alloted_year = year(now())"; //exit;
		$result = $db->query($select);
        return $result->fetchAll();

	}

	public function getEmployeeWiseLeaveBalance (){
		$db = Zend_Db_Table::getDefaultAdapter();
        $select = "SELECT mel.user_id, mel.leavetypeid, (mel.emp_leave_limit - mel.used_leaves) as leave_count from main_employeeleaves mel where mel.isactive = 1 AND mel.alloted_year = year(now())";
		//$select = "SELECT mel.user_id, mel.leavetypeid, if((mel.emp_leave_limit - mel.used_leaves) >= 0,(mel.emp_leave_limit - mel.used_leaves),0) as leave_count from main_employeeleaves mel where mel.isactive = 1 AND mel.alloted_year = year(now())";
		$result = $db->query($select);
		$arr = array();
		foreach($result->fetchAll() as $res) {
			if($res['leavetypeid'] == 1)
				$arr[$res['user_id']]['pl'] = $res['leave_count'];
			else
				$arr[$res['user_id']]['sl'] =  $res['leave_count'];
		}
        return $arr;

	}

	public function getEmployeeLeaves($u_ids = array(), $months = array(), $year = 0,  $status = "Approved") {

		if($year == 0) $year = date('Y');
		if(empty($months)) $months = array(date("m"));
		$db = Zend_Db_Table::getDefaultAdapter();
        $furtherCondition = "";
        if(is_array($u_ids) && count($u_ids) > 0) {
            $furtherCondition = " and u.id in (" . implode(",", $u_ids) . ")";
        }
        if(!empty($months)){
        	$furtherCondition .= " and (";
        	foreach($months as $k=>$month) {
        		if($k != 0) $furtherCondition .= " OR ";
        		$stDt = date("Y-m-d", mktime(0,0,0, $month, 1, $year));
        		$endDt = date("Y-m-d", mktime(0,0,0, $month + 1, 1, $year) - 1000);
        		$furtherCondition .= " ((from_date >= '" . $stDt . "' and from_date  <= '" . $endDt . "') or  (to_date >= '" . $stDt . "' and to_date  <= '" . $endDt . "')) ";
        	}
        	$furtherCondition .= ") ";
        }
        if(trim($status) != "") $furtherCondition .= " and  lrs.leavestatus = '".$status."'";
        $h_rows = $this->getCompanyHolidays($year); 
        $holidays = array();
        foreach ($h_rows as $holiday_dt) {
        	$holidays[] = date("Y-m-d", strtotime($holiday_dt['holidaydate']));
        }

        $query = "select u.id, u.employeeId, u.emailaddress, lrs.leavestatus, lrs.leavetypeid, lrs.leavetype_name, lrs.from_date, lrs.to_date from  main_leaverequest_summary lrs left join main_users u on u.id = lrs.user_id where lrs.isactive = 1 " . $furtherCondition . " order by lrs.user_id";
        $result = $db->query($query);
        $rows = $result->fetchAll();
        $resultantArray = array();
        foreach($rows as $data) {
        	if(!isset($resultantArray[$data['id']]))
        	$resultantArray[$data['id']] = array('id' => $data['id'], 'cue_id' => $data['employeeId'], 'email' => $data['emailaddress'], 'leaves' => array());
        	$startDate = $fromDate = date("Y-m-d", strtotime($data['from_date']));
        	$endDate = $toDate =  date("Y-m-d", strtotime($data['to_date']));
        	while (strtotime($startDate) <= strtotime($endDate)) {
        		$month_of_date = date('m', strtotime($startDate));
        		if(in_array($month_of_date, $months))
        		{
        			$dayofweek = date('w', strtotime($startDate));
        			if($dayofweek != 0 && $dayofweek != 6)
	        		{
	        			if(!in_array($startDate, $holidays)) {
		                	$resultantArray[$data['id']]['leaves'][] = array(
			                														'date' 			=> date("d/m/Y", strtotime($startDate)),
			                														'leave_type'	=> $data['leavetype_name'],
			                														'leave_type_id'	=> $data['leavetypeid'],
			                														'leave_status'	=> $data['leavestatus']
		                														);
		                }
	        		}
        		}
        		
                $startDate = date ("Y-m-d", strtotime("+1 day", strtotime($startDate)));
 			}
        }
        return $resultantArray;
	}


	/*function to credit monthly pl/sl to employees*/
	public function creditEmployeeMonthlyLeaves($employeesId){
		$date= gmdate("Y-m-d H:i:s");  
	    $db = Zend_Db_Table::getDefaultAdapter();
	    $kracycle_date = gmdate('Y-04-01 00:00:00');
	    $kracycle_flag = 0;
		if (strtotime('today') == strtotime($kracycle_date) ){
			$kracycle_flag = 1;
		}
		echo "cron start";
	    for($i=0;$i<sizeof($employeesId);$i++){	    
	    //credit PL    	
		    $employeePL_count = EMPLOYEE_PL_COUNT;
		    $employeeSL_count= EMPLOYEE_SL_COUNT;
		    $employeePLArr=$this->getsingleEmployeeleavetypeCount($employeesId[$i]["id"],1);
			
			if(sizeof($employeePLArr)>=1 && $employeePLArr[0]['leave_count']>0){
				if($kracycle_flag ==1 && $employeePLArr[0]['leave_count']>EMPLOYEE_PL_CARRYFORWARD_COUNT){
					$employeePLArr[0]['leave_count'] = EMPLOYEE_PL_CARRYFORWARD_COUNT;
				}
			  	$employeePL_count=$employeePLArr[0]['leave_count']+$employeePL_count;
			}

		    $employeeSLArr=$this->getsingleEmployeeleavetypeCount($employeesId[$i]["id"],2);

		    if(sizeof($employeeSLArr)>=1 && $employeeSLArr[0]['leave_count']>0){
		    	if($kracycle_flag ==1 && $employeeSLArr[0]['leave_count']>EMPLOYEE_SL_CARRYFORWARD_COUNT){
					$employeeSLArr[0]['leave_count']=EMPLOYEE_SL_CARRYFORWARD_COUNT;
				}
		   	    $employeeSL_count=$employeeSLArr[0]['leave_count']+$employeeSL_count;
		    }
		    echo "SL ->".$employeeSL_count."PL ->".$employeePL_count."<br>";
	    //credit SL
		    echo "sl update query INSERT INTO `main_employeeleaves` (user_id,emp_leave_limit,leavetypeid,used_leaves,alloted_year,createdby,modifiedby,createddate,modifieddate,isactive,isleavetrasnferset) VALUES (".$employeesId[$i]["id"].",".$employeeSL_count.",2,0,year(now()),1,1,'".$date."','".$date."',1,0) ON DUPLICATE KEY UPDATE emp_leave_limit='".$employeeSL_count."',modifiedby=1,modifieddate='".$date."',isactive = 1,used_leaves = 0 <br>";
		    echo "pl update query INSERT INTO `main_employeeleaves` (user_id,emp_leave_limit,leavetypeid,used_leaves,alloted_year,createdby,modifiedby,createddate,modifieddate,isactive,isleavetrasnferset) VALUES (".$employeesId[$i]["id"].",".$employeePL_count.",1,0,year(now()),1,1,'".$date."','".$date."',1,0) ON DUPLICATE KEY UPDATE emp_leave_limit='".$employeePL_count."',modifiedby=1,modifieddate='".$date."',isactive = 1,used_leaves = 0 <br>";
	
	    $rowscreditSL = $db->query("INSERT INTO `main_employeeleaves` (user_id,emp_leave_limit,leavetypeid,used_leaves,alloted_year,createdby,modifiedby,createddate,modifieddate,isactive,isleavetrasnferset) VALUES (".$employeesId[$i]["id"].",".$employeeSL_count.",2,0,year(now()),1,1,'".$date."','".$date."',1,0) ON DUPLICATE KEY UPDATE emp_leave_limit='".$employeeSL_count."',modifiedby=1,modifieddate='".$date."',isactive = 1 ,used_leaves = 0 ");	
	    $rowscreditPL = $db->query("INSERT INTO `main_employeeleaves` (user_id,emp_leave_limit,leavetypeid,used_leaves,alloted_year,createdby,modifiedby,createddate,modifieddate,isactive,isleavetrasnferset) VALUES (".$employeesId[$i]["id"].",".$employeePL_count.",1,0,year(now()),1,1,'".$date."','".$date."',1,0) ON DUPLICATE KEY UPDATE emp_leave_limit='".$employeePL_count."',modifiedby=1,modifieddate='".$date."',isactive = 1 ,used_leaves = 0 ");	
	   echo "<br><br>";
	    }
	    $this->logCreditEmployeeMonthlyLeaves();
	    echo "cron end";
	    die();
	}
	/*Richa code end*/

	/**
	* @Author: Aju John
	* Added function to log cron status
	*/
	function logCreditEmployeeMonthlyLeaves() {
		$db = Zend_Db_Table::getDefaultAdapter();
		$date= gmdate("Y-m-d"); 
		$dateTime= gmdate("Y-m-d H:i:s"); 
		$qry = "INSERT INTO `cron_log` (cron_name,cron_date,createddate) VALUES ('creditEmployeeMonthlyLeaves','".$date."', '".$dateTime."')";
		$db->query($qry);
		$this->sendEmailAfterLeaveCredit();
	}

	function isCronExecuted() {
		$currentMonth = date("n");
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select * from cron_log where cron_name = 'creditEmployeeMonthlyLeaves' and Month(cron_date) = ".$currentMonth; 
        $result = $db->query($query);
        $rows = $result->fetchAll();
        if(count($rows) > 0) {
        	return true;
        } else {
        	return false;
        }
	}

	function sendEmailAfterLeaveCredit () {
		$options = array();
		$options['header'] = 'HRMS - Monthly Leave Credited for the month of ' . date("F Y");
		$temp = EMAIL_LEAVE_CC;
		$tmpAr = explode(",", $temp);
		$options['toEmail'] = $tmpAr[0];		
		$options['cc'] = array($tmpAr[1]);
		$options['toName'] =  "The HR";
    	$options['subject'] = 'HRMS - Monthly Leave Credited for the month of ' . date("F Y");
		$options['message'] = '<div>Hi,</div><div>The monthly leave credit cron has been executed successfully at <i>' . date('d-M-Y h:i:s A') .'</i> and credited the leaves for the month of ' . date("F") . '.</div>';
		
		$result = sapp_Global::_sendEmail($options);
	}
}
?>