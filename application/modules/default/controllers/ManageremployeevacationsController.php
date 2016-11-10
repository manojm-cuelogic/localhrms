<?php

class Default_ManageremployeevacationsController extends Zend_Controller_Action
{

    private $options;
	public function preDispatch()
	{
		 
		
	}
	
    public function init()
    {
        $this->_options= $this->getInvokeArg('bootstrap')->getOptions();
		
    }

    public function indexAction()
    {
    	
	    $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
		}
		$leaverequestmodel = new Default_Model_Leaverequest();	
        $call = $this->_getParam('call');
		if($call == 'ajaxcall')
				$this->_helper->layout->disableLayout();
		
		$view = Zend_Layout::getMvcInstance()->getView();		
		$objname = $this->_getParam('objname');
		$refresh = $this->_getParam('refresh');
		$dashboardcall = $this->_getParam('dashboardcall');
		
		$data = array();
		$searchQuery = '';
		$searchArray = array();
		$tablecontent='';
		
		if($refresh == 'refresh')
		{
		    if($dashboardcall == 'Yes')
				$perPage = DASHBOARD_PERPAGE;
			else	
				$perPage = PERPAGE;
			$sort = 'DESC';$by = 'createddate';$pageNo = 1;$searchData = '';
		}
		else 
		{
			$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
			$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'createddate';
			if($dashboardcall == 'Yes')
				$perPage = $this->_getParam('per_page',DASHBOARD_PERPAGE);
			else 
			    $perPage = $this->_getParam('per_page',PERPAGE);
			$pageNo = $this->_getParam('page', 1);
			/** search from grid - START **/
			$searchData = $this->_getParam('searchData');	
			$searchData = rtrim($searchData,',');
			/** search from grid - END **/
		}
		
		/** Get next level Managers **/
		$nextLevelManagers = array();
		$empModel = new Default_Model_Employee();
		if($empModel->isSuperManager()) {
			$nextLevelManagers = $empModel->getEmployeesUnderRM($loginUserId);
		}
		/** Get next level Managers **/
		$managerId = ($this->_getParam('manager_Id') !='')? $this->_getParam('manager_Id'):'';
		$objName = 'manageremployeevacations';
		$queryflag = '';
		$dataTmp = $leaverequestmodel->getGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall,$objName,$queryflag, '', '', $managerId);     
		
		array_push($data,$dataTmp);
		$this->view->dataArray = $data;

		$this->view->nextLevelManagers = $nextLevelManagers ;
		$this->view->call = $call ;
		$this->view->messages = $this->_helper->flashMessenger->getMessages();
    }
	
     public function viewAction()
	{	
	    $auth = Zend_Auth::getInstance();
     		if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
			}
		$id = $this->getRequest()->getParam('id');
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
			$this->_helper->layout->disableLayout();
		$objName = 'manageremployeevacations';
		$managerleaverequestform = new Default_Form_managerleaverequest();
		$managerleaverequestform->removeElement("submit");
		$elements = $managerleaverequestform->getElements();
		if(count($elements)>0)
		{
			foreach($elements as $key=>$element)
			{
				if(($key!="Cancel")&&($key!="Edit")&&($key!="Delete")&&($key!="Attachments")){
				$element->setAttrib("disabled", "disabled");
					}
        	}
        }
		
		try
		{
			    if($id && is_numeric($id) && $id>0)
                {				
					$leaverequestmodel = new Default_Model_Leaverequest();
					$usersmodel= new Default_Model_Users();
					$emp_model= new Default_Model_Employee();

					$flag = 'true'; 
					
					$userid = $leaverequestmodel->getUserID($id);
					$getreportingManagerArr = $leaverequestmodel->getReportingManagerId($id);
					$reportingManager = $getreportingManagerArr[0]['repmanager'];
					if($reportingManager != $loginUserId && !$emp_model->isSuperManager())
						$flag = 'false';
					if(!empty($userid))
						$isactiveuser = $usersmodel->getUserDetailsByID($userid[0]['user_id']);
					else
						$this->view->rowexist = "rows"; 
					 
					if(!empty($userid) && !empty($isactiveuser) && $flag == 'true')
					{ 
						$data = $leaverequestmodel->getLeaveRequestDetails($id);
						if(!empty($data)) // && $data[0]['leavestatus'] == 'Pending for approval'
							{
								//die(__LINE__);
								$data = $data[0];
								$employeeleavetypemodel = new Default_Model_Employeeleavetypes();
								$usersmodel = new Default_Model_Users();
										
								$employeeleavetypeArr = $employeeleavetypemodel->getsingleEmployeeLeavetypeData($data['leavetypeid']);
								if($employeeleavetypeArr !='norows')
								{
									$managerleaverequestform->leavetypeid->addMultiOption($employeeleavetypeArr[0]['id'],utf8_encode($employeeleavetypeArr[0]['leavetype']));		   
								}
								
								if($data['leaveday'] == 1)
								{
								  $managerleaverequestform->leaveday->addMultiOption($data['leaveday'],'Full Day');		   
								}
								else 
								{
								  $managerleaverequestform->leaveday->addMultiOption($data['leaveday'],'Half Day');
								}					
							   
								$employeenameArr = $usersmodel->getUserDetailsByID($data['user_id']);	
								$managerleaverequestform->populate($data);							
															
															$from_date = sapp_Global::change_date($data['from_date'], 'view');
															$to_date = sapp_Global::change_date($data['to_date'], 'view');
															$appliedon = sapp_Global::change_date($data['createddate'], 'view');
															
								$managerleaverequestform->from_date->setValue($from_date);
								$managerleaverequestform->to_date->setValue($to_date);
								$managerleaverequestform->createddate->setValue($appliedon);
								$managerleaverequestform->appliedleavesdaycount->setValue($data['appliedleavescount']);
								$managerleaverequestform->employeename->setValue($employeenameArr[0]['userfullname']);
								$managerleaverequestform->setDefault('leavetypeid',$data['leavetypeid']);
								$managerleaverequestform->setDefault('leaveday',$data['leaveday']);
								$this->view->controllername = $objName;
								$this->view->id = $id;
								$this->view->form = $managerleaverequestform;
								$this->view->data = $data;

							}
						else
							{
								   $this->view->rowexist = "rows";
							}				
					}
					else
					{
						   $this->view->rowexist = "rows";
					}
				}
				else
				{
					   $this->view->rowexist = "rows";
				}
			
        }
        catch(Exception $e)
		{
			 $this->view->rowexist = 'norows';
		}  		
			
	}
	
	
	public function editAction()
	{	

	    $auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
				$loginUserId = $auth->getStorage()->read()->id;
		}
		$id = $this->getRequest()->getParam('id');
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
			$this->_helper->layout->disableLayout();
		
		$managerleaverequestform = new Default_Form_managerleaverequest();
		try
		{

		        if($id && is_numeric($id) && $id>0)
                {

					$leaverequestmodel = new Default_Model_Leaverequest();
					$usersmodel= new Default_Model_Users();
					$emp_model= new Default_Model_Employee();

					$flag = 'true';
					$userid = $leaverequestmodel->getUserID($id);
					$getreportingManagerArr = $leaverequestmodel->getReportingManagerId($id);
					$reportingManager = $getreportingManagerArr[0]['repmanager'];
					if($reportingManager != $loginUserId && !$emp_model->isSuperManager())
						$flag = 'false';
					if(!empty($userid))
					 $isactiveuser = $usersmodel->getUserDetailsByID($userid[0]['user_id']);
					else
					 $this->view->rowexist = "rows"; 				
				
					if(!empty($userid) && !empty($isactiveuser) && $flag=='true')
					{	
						$data = $leaverequestmodel->getLeaveRequestDetails($id);				
						if(!empty($data) && ($data[0]['leavestatus'] == 'Pending for approval' ||  $data[0]['leavestatus'] == 'Pending for cancellation'))
							{ 
								$data = $data[0]; 
								$reason = $data['reason'];
								$appliedleavescount = $data['appliedleavescount'];
								$employeeid = $data['user_id']; 
								$leavetypeid = $data['leavetypeid'];
								$employeeleavetypemodel = new Default_Model_Employeeleavetypes();
								$usersmodel = new Default_Model_Users();
								$employeesmodel = new Default_Model_Employees();
								$businessunitid = '';
								$loggedInEmployeeDetails = $employeesmodel->getLoggedInEmployeeDetails($employeeid);
								 if($loggedInEmployeeDetails[0]['businessunit_id'] != '')
									$businessunitid = $loggedInEmployeeDetails[0]['businessunit_id'];
										
								$employeeleavetypeArr = $employeeleavetypemodel->getsingleEmployeeLeavetypeData($data['leavetypeid']);
								if($employeeleavetypeArr !='norows')
								{
									$managerleaverequestform->leavetypeid->addMultiOption($employeeleavetypeArr[0]['id'],utf8_encode($employeeleavetypeArr[0]['leavetype']));		   
								}

								
								if($data['leaveday'] == 1)
								{
								  $managerleaverequestform->leaveday->addMultiOption($data['leaveday'],'Full Day');		   
								}
								else 
								{
								  $managerleaverequestform->leaveday->addMultiOption($data['leaveday'],'Half Day');					  
								}					
							   	
								$getunaprovedleavescount = 0;
								$getunaprovedleaves = $leaverequestmodel->getPendingLeaves($employeeid,$leavetypeid);
								if(!empty($getunaprovedleaves))
								{
									$getunaprovedleavescount = ($getunaprovedleaves[0]['unapproved_leaves_count'] <= 0) ? 0 : ($getunaprovedleaves[0]['unapproved_leaves_count'] - 1); 
									
									$managerleaverequestform->unapprovedleavesdaycount->setValue($getunaprovedleavescount);
									
								}
								else {
									$managerleaverequestform->unapprovedleavesdaycount->setValue($getunaprovedleavescount);
								}

					      	    $getavailbaleleaves = $leaverequestmodel->getAvailableLeaves($employeeid,$leavetypeid);
								if(!empty($getavailbaleleaves))
								{
									$leavesLeft = $getavailbaleleaves[0]['remainingleaves']-$getunaprovedleavescount; 
									$leavesLeft = ($leavesLeft > 0 ) ? $leavesLeft : 0;
									$managerleaverequestform->available_leaves->setValue($leavesLeft);
								}
								

								$employeenameArr = $usersmodel->getUserDetailsByID($data['user_id']);
								$employeeemail = $employeenameArr[0]['emailaddress'];					
								$employeename = $employeenameArr[0]['userfullname'];
								$managerleaverequestform->populate($data);
																						
															$from_date = sapp_Global::change_date($data['from_date'], 'view');
															$to_date = sapp_Global::change_date($data['to_date'], 'view');
															$appliedon = sapp_Global::change_date($data['createddate'], 'view');
															
								$managerleaverequestform->from_date->setValue($from_date);
								$managerleaverequestform->to_date->setValue($to_date);
								$managerleaverequestform->createddate->setValue($appliedon);
								$managerleaverequestform->appliedleavesdaycount->setValue($data['appliedleavescount']);
								$managerleaverequestform->employeename->setValue($employeenameArr[0]['userfullname']);
								$managerleaverequestform->setDefault('leavetypeid',$data['leavetypeid']);
								$managerleaverequestform->setDefault('leaveday',$data['leaveday']);
								
								/*Richa code 
								Changing drop down option as per approval/cancel request
								start*/							
								if($data['leavestatus'] == 'Pending for cancellation'){
								 $managerleaverequestform->managerstatus->setMultiOptions(array(							
															'3'=>'Cancel'
															));
								}else{
								 $managerleaverequestform->managerstatus->setMultiOptions(array(							
															'1'=>'Approve' ,
															'2'=>'Reject'															));
								}
								/*Richa code end*/
								
								$this->view->id = $id;
								$this->view->form = $managerleaverequestform;
								$this->view->data = $data;

								$managerleaverequestform->setAttrib('action',BASE_URL.'manageremployeevacations/edit/id/'.$id);
						
							}
							else
							{
								   $this->view->rowexist = "rows";
							}
					} 
					else
					{
						   $this->view->rowexist = "rows";
					}
                }
				else
				{
					   $this->view->rowexist = "rows";
				} 					
			
		}
		catch(Exception $e)
		{
			 $this->view->rowexist = 'norows';
		}

		if($this->getRequest()->getPost()){
      		$result = $this->save($managerleaverequestform,$appliedleavescount,$employeeemail,$employeeid,$employeename,$from_date,$to_date,$reason,$businessunitid,$leavetypeid);	
		    $this->view->msgarray = $result; 
		}
	}
	
	public function save($managerleaverequestform,$appliedleavescount,$employeeemail,$employeeid,$userfullname,$from_date,$to_date,$reason,$businessunitid,$leavetypeid)
	{

	  $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
		} 		
     		if($managerleaverequestform->isValid($this->_request->getPost())){
			    $id = $this->_request->getParam('id'); 
			    $managerstatus = $this->_request->getParam('managerstatus');
			    $comments = $this->_request->getParam('comments');
				$date = new Zend_Date();
				$leaverequestmodel = new Default_Model_Leaverequest(); 
				$employeeleavetypesmodel = new Default_Model_Employeeleavetypes();
				$usersmodel = new Default_Model_Users();
				$approvedByDetails = $usersmodel->getUserDetailsByID($loginUserId); //echo "<pre>"; print_r($approvedByDetails); exit;
				$actionflag = '';
				$tableid  = ''; 
				$status = '';
				$messagestr = '';
				$leavetypetext = '';
				$leavetypeArr = $employeeleavetypesmodel->getLeavetypeDataByID($leavetypeid);
				$extraEmailContent = "";
				
				$leavetypetext = $leavetypeArr[0]['leavetype'];
				if($managerstatus == 1 && !empty($leavetypeArr))
				{
					if($leavetypeArr[0]['leavepredeductable'] == 1) {		
						$updateemployeeleave = $leaverequestmodel->updateemployeeleaves($appliedleavescount,$employeeid,$leavetypeid);
					}
					$approvedByDetails = $usersmodel->getUserDetailsByID($loginUserId);
					$appliedByUserId = $leaverequestmodel->getsinglePendingLeavesData($id);
					$appliedByUserId = $appliedByUserId[0]['applied_by']; 
					$appliedByDetails = array();
					if(isset($appliedByUserId) && strlen(trim($appliedByUserId)) > 0) {
						$appliedByDetails = $usersmodel->getUserDetailsByID($appliedByUserId);
					}

					$status = 2; 
					$messagestr = "Leave request approved.";
					if(count($appliedByDetails) > 0) {
						$extraEmailContent = '<tr bgcolor="#e9f6fc">
					                        <td style="border-right:2px solid #BBBBBB;">Applied By</td>
					                        <td>'.$appliedByDetails[0]['userfullname'].'</td>
					                  	</tr>';
					} else $extraEmailContent = '';

					$extraEmailContent .= '<tr bgcolor="#e9f6fc">
					                        <td style="border-right:2px solid #BBBBBB;">Approved By</td>
					                        <td>'.$approvedByDetails[0]['userfullname'].'</td>
					                  	</tr>';
				  
				}else if($managerstatus == 2)
				{
					if(trim($comments) == "")
				  	{
				  		$msgarray = array("comments"=>"Comments can not be blank");
				  		return $msgarray;
				  	}
				  $status = 3;  
				  $messagestr = "Leave request rejected.";
				  $extraEmailContent = '<tr bgcolor="#e9f6fc">
			                        <td style="border-right:2px solid #BBBBBB;">Reason for Leave Rejection</td>
			                        <td>'.$comments.'</td>
			                  	</tr>';
				}
				else if($managerstatus == 3)
				{
					if(trim($comments) == "")
				  	{
				  		$msgarray = array("comments"=>"Comments can not be blank");
				  		return $msgarray;
				  	}
				  $updateemployeeleave = $leaverequestmodel->updateemployeeleavesoncancellation($appliedleavescount,$employeeid,$leavetypeid);
				  $status = 4;  
				  $messagestr = "Leave request cancelled.";
				  $extraEmailContent = '<tr bgcolor="#e9f6fc">
			                        <td style="border-right:2px solid #BBBBBB;">Reason for Leave Cancellation</td>
			                        <td>'.$comments.'</td>
			                  	</tr>';
				}

				  
				  if($managerstatus == 1 || $managerstatus == 2 || $managerstatus == 3)
				  {
				   $data = array( 'leavestatus'=>$status,
				   					'approved_by'=>$loginUserId,
				   				  'approver_comments'=> $comments,	
				                  'modifiedby'=>$loginUserId,
								  'modifieddate'=>gmdate("Y-m-d H:i:s")
						);
						if($id!=''){
							$where = array('id=?'=>$id);  
							$actionflag = 2;
						}
						else
						{
							$data['createdby'] = $loginUserId;
							$data['createddate'] = gmdate("Y-m-d H:i:s");
							$data['isactive'] = 1;
							$where = '';
							$actionflag = 1;
						}
						$Id = $leaverequestmodel->SaveorUpdateLeaveRequest($data, $where);
						    if($Id == 'update')
							{
							   $tableid = $id;
							   $this->_helper->getHelper("FlashMessenger")->addMessage($messagestr);
							}   
							else
							{
							   $tableid = $Id; 	
								$this->_helper->getHelper("FlashMessenger")->addMessage($messagestr);					   
							}
                            /** MAILING CODE **/
							
							if($to_date == '' || $to_date == NULL)
								$to_date = $from_date;
								
							
							/* Mail to Employee */
								$options['header'] = 'Leave Request';
								$options['toEmail'] = $employeeemail;
								$options['toName'] = $userfullname;
								$cc_temp = EMAIL_LEAVE_CC;
								$cc = explode(",", $cc_temp);
								$options['cc'] = $cc;
								if($messagestr == 'Leave request approved.'){
                                	$options['subject'] = 'Leave Request - ' . $userfullname . ' #'.$id;
                                	//$options['subject'] = 'Leave request approved - '.$userfullname;
									$options['message'] = '<div>Hi,</div><div>The below leave(s) has been approved.</div>';
								}else if($messagestr == 'Leave request cancelled.'){
                                	$options['subject'] = 'Leave Request - ' . $userfullname . ' #'.$id;
									//$options['subject'] = 'Leave request cancelled - '.$userfullname;
									$options['message'] = '<div>Hi,</div><div>The below leave(s) has been cancelled.</div>';
								}else{ 
                                	$options['subject'] = 'Leave Request - ' . $userfullname . ' #'.$id;
									//$options['subject'] = 'Leave request rejected - '.$userfullname;
									$options['message'] = '<div>Hi,</div><div>The below leave(s) has been rejected. </div>';
								}	
								$options['message'] .= '<div>
                <table width="100%" cellspacing="0" cellpadding="15" border="0" style="border:3px solid #BBBBBB; font-size:16px; font-family:Arial, Helvetica, sans-serif; margin:30px 0 30px 0;" bgcolor="#ffffff">
                      <tbody><tr>
                        <td width="28%" style="border-right:2px solid #BBBBBB;">Employee Name</td>
                        <td width="72%">'.$userfullname.'</td>
                      </tr>
                      <tr bgcolor="#e9f6fc">
                        <td style="border-right:2px solid #BBBBBB;">No. of Day(s)</td>
                        <td>'.$appliedleavescount.'</td>
                      </tr>
                      <tr>
                        <td style="border-right:2px solid #BBBBBB;">From</td>
                        <td>'.date("d F Y", strtotime($from_date)).'</td>
                      </tr>
                      <tr bgcolor="#e9f6fc">
                        <td style="border-right:2px solid #BBBBBB;">To</td>
                        <td>'.date("d F Y", strtotime($to_date)).'</td>
            	     </tr>
            	     <tr>
    	                 <td style="border-right:2px solid #BBBBBB;">Leave Type</td>
                        <td>'.$leavetypetext.'</td>
                        </tr>
                      <tr bgcolor="#e9f6fc">
                        <td style="border-right:2px solid #BBBBBB;">Reason for Leave</td>
                        <td>'.$reason.'</td>
                  	</tr>
                  	'.$extraEmailContent.'
                </tbody></table>

            </div>
            <div style="padding:20px 0 10px 0;">Please <a href="'.BASE_URL.'" target="_blank" style="color:#b3512f;">click here</a> to login and check the leave details.</div>';	
                                $result = sapp_Global::_sendEmail($options);
							/* END */	
							
							
					}	
					$menuID = MANAGEREMPLOYEEVACATIONS;
					$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
    			    $this->_redirect('manageremployeevacations');		
			}else
			{
     			$messages = $managerleaverequestform->getMessages();
				foreach ($messages as $key => $val)
					{
						foreach($val as $key2 => $val2)
						 {
							$msgarray[$key] = $val2;
							break;
						 }
					}
				return $msgarray;			
			}
	}
	
	public function deleteAction()
	{
	     $auth = Zend_Auth::getInstance();
     		if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
				}
		 $id = $this->_request->getParam('objid');
		 $messages['message'] = '';
		 $actionflag = 3;
		    if($id)
			{
			$holidaygroupsmodel = new Default_Model_Holidaygroups(); 
			  $data = array('isactive'=>0);
			  $where = array('id=?'=>$id);
			  $Id = $holidaygroupsmodel->SaveorUpdateGroupData($data, $where);
			    if($Id == 'update')
				{
				   $menuID = HOLIDAYGROUPS;
				   $result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id); 
				   $messages['message'] = 'Holiday group deleted successfully.';
				}   
				else
                   $messages['message'] = 'Holiday group cannot be deleted.';				
			}
			else
			{ 
			 $messages['message'] = 'Holiday group cannot be deleted.';
			}
			$this->_helper->json($messages);
		
	}
	
	

}

