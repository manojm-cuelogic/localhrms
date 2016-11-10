<?php

class Default_ApprovedleavesController extends Zend_Controller_Action
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
			$sort = 'DESC';$by = 'modifieddate';$pageNo = 1;$searchData = '';$searchQuery = '';$searchArray='';
		}
		else 
		{
			$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
			$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'modifieddate';
			if($dashboardcall == 'Yes')
				$perPage = $this->_getParam('per_page',DASHBOARD_PERPAGE);
			else 
			    $perPage = $this->_getParam('per_page',PERPAGE);
			$pageNo = $this->_getParam('page', 1);
			$searchData = $this->_getParam('searchData');
            $searchData = rtrim($searchData,',');			
		}
		$objName = 'approvedleaves';
		$queryflag = 'approved';		
		$dataTmp = $leaverequestmodel->getGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall,$objName,$queryflag);     
						
		array_push($data,$dataTmp);
		$this->view->dataArray = $data;
		$this->view->call = $call ;
		$this->view->messages = $this->_helper->flashMessenger->getMessages();
    }
	
    public function viewAction()
	{
        $auth = Zend_Auth::getInstance();
     		if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
					
			}
		$leaverequestmodel = new Default_Model_Leaverequest();	
		$id = $this->getRequest()->getParam('id');
		
		try
		{
			$useridArr = $leaverequestmodel->getUserID($id);
			
			if(!empty($useridArr))
			{
			  $user_id = $useridArr[0]['user_id'];
				if($user_id == $loginUserId)
				{
			
					$callval = $this->getRequest()->getParam('call');
					if($callval == 'ajaxcall')
						$this->_helper->layout->disableLayout();
					$objName = 'pendingleaves';
					$leaverequestform = new Default_Form_leaverequest();
					$leaverequestform->removeElement("submit");
					$elements = $leaverequestform->getElements();
					if(count($elements)>0)
					{
						foreach($elements as $key=>$element)
						{
							if(($key!="Cancel")&&($key!="Edit")&&($key!="Delete")&&($key!="Attachments")){
							$element->setAttrib("disabled", "disabled");
								}
						}
					}
						$data = $leaverequestmodel->getsinglePendingLeavesData($id);
						
						$data = $data[0];
						$getavailbaleleaves = $leaverequestmodel->getAvailableLeaves($loginUserId,$data['leavetypeid']);

						if(!empty($data) ) //&& $data['leavestatus'] == 'Approved'
							{
								$employeeleavetypemodel = new Default_Model_Employeeleavetypes();
								$usersmodel = new Default_Model_Users();
										
								$employeeleavetypeArr = $employeeleavetypemodel->getsingleEmployeeLeavetypeData($data['leavetypeid']);
								if($employeeleavetypeArr != 'norows')
								{
									$leaverequestform->leavetypeid->addMultiOption($employeeleavetypeArr[0]['id'],utf8_encode($employeeleavetypeArr[0]['leavetype']));		   
								}
								
								if($data['leaveday'] == 1)
								{
								  $leaverequestform->leaveday->addMultiOption($data['leaveday'],'Full Day');		   
								}
								else 
								{
								  $leaverequestform->leaveday->addMultiOption($data['leaveday'],'Half Day');
								}					
							    
								$repmngrnameArr = $usersmodel->getUserDetailsByID($data['rep_mang_id'],'all');	
								$leaverequestform->populate($data);								
								$from_date = sapp_Global::change_date($data["from_date"], 'view');
								$to_date = sapp_Global::change_date($data["to_date"], 'view');
								$appliedon = sapp_Global::change_date($data["createddate"], 'view');
								$leaverequestform->from_date->setValue($from_date);
								$leaverequestform->to_date->setValue($to_date);
								$leaverequestform->createddate->setValue($appliedon);
								$leaverequestform->appliedleavesdaycount->setValue($data['appliedleavescount']);
								$leaverequestform->comments->setValue($data['approver_comments']);
								if(!empty($repmngrnameArr))
								 $leaverequestform->rep_mang_id->setValue($repmngrnameArr[0]['userfullname']);
								else 
								  $leaverequestform->rep_mang_id->setValue('');
							/*	if(!empty($getavailbaleleaves))
								 {
									$leaverequestform->no_of_days->setValue($getavailbaleleaves[0]['remainingleaves']);
								 }  */
								$this->view->controllername = $objName;
								$this->view->id = $id;
								$this->view->form = $leaverequestform;
								$this->view->data = $data;
								$leaverequestform->setDefault('leavetypeid',$data['leavetypeid']);
								$leaverequestform->setDefault('leaveday',$data['leaveday']);
							
								$this->view->reportingmanagerStatus = (!empty($repmngrnameArr))?$repmngrnameArr[0]['isactive']:'';
							}	
						
						else
						{
						   $this->view->rowexist = "rows";
						}
					}else
					{
					   $this->view->rowexist = "rows";
					}
				}else
				{
				   $this->view->rowexist = "norows";
				} 
        } 	
        catch(Exception $e){
			    $this->view->rowexist = "norows";
		    } 		
	}
	
	public function deleteAction()
	{
		
	     $auth = Zend_Auth::getInstance();
     		if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
					$loginUserEmail = $auth->getStorage()->read()->emailaddress;
					$loginUserName = $auth->getStorage()->read()->userfullname;
				}
			$leaverequestmodel = new Default_Model_Leaverequest();
			$usersmodel = new Default_Model_Users();
			$leavetypemodel = new Default_Model_Employeeleavetypes();
		  $id = $this->_request->getParam('objid');
		  $comment = $this->_request->getParam('comment');
		  $data = $leaverequestmodel->getsinglePendingLeavesData($id);
		  $data = $data[0];
		  
		  $appliedleavescount = $data['appliedleavescount'];
		  $to_date = $data['to_date'];			  
		  $from_date = $data['from_date'];
		  $reason = $data['reason'];
		  $leavetypeid = $data['leavetypeid'];
		  $leavetypedata = $leavetypemodel->getLeavetypeDataByID($leavetypeid);
		  $leavetypetext = $leavetypedata[0]['leavetype'];
		  $repmngrnameArr = $usersmodel->getUserDetailsByID($data['rep_mang_id']);
		  $reportingManageremail = $repmngrnameArr[0]['emailaddress'];	
	      $reportingmanagerName	= $repmngrnameArr[0]['userfullname'];		 
		  $messages['message'] = '';
		  $actionflag = 3;

		    if($id)
			{
			  $data = array('leavestatus'=>5,'requester_comments'=>$comment);
			  $where = array('id=?'=>$id);
			  $Id = $leaverequestmodel->SaveorUpdateLeaveRequest($data, $where);
			    /*if($Id == 'update')
				{			
				 
				   $messages['message'] = 'Leave request cancelled.';
				}   
				else
                   $messages['message'] = 'Leave request cannot be cancelled.';		
             	*/
                   /*START=======================*/
                   	/* Mail to Reporting manager */
							if($to_date == '' || $to_date == NULL)
							$to_date = $from_date;
							
							$toemailArr = $reportingManageremail; //$employeeemail
							if(!empty($toemailArr))
							{
								$options['subject'] = 'Leave Request - ' . $loginUserName . ' #'.$id;
								$options['header'] = 'Request for Approved leave cancellation';
								$options['toEmail'] = $toemailArr;
								$$cc_temp = EMAIL_LEAVE_CC;
								$cc = explode(",", $cc_temp);
								$options['cc'] = $cc;
                                $url = BASE_URL . "manageremployeevacations"; 
								$options['toName'] = $reportingmanagerName;
								$options['message'] = '<div>
												<div>Dear '.$reportingmanagerName.',</div>
												<div>The approved leave of the below employee is pending for cancellation:</div>
												<div>
                <table width="100%" cellspacing="0" cellpadding="15" border="0" style="border:3px solid #BBBBBB; font-size:16px; font-family:Arial, Helvetica, sans-serif; margin:30px 0 30px 0;" bgcolor="#ffffff">
                      <tbody><tr>
                        <td width="28%" style="border-right:2px solid #BBBBBB;">Employee Name</td>
                        <td width="72%">'.$loginUserName.'</td>
                      </tr>
                      <tr bgcolor="#e9f6fc">
                        <td style="border-right:2px solid #BBBBBB;">No. of Day(s)</td>
                        <td>'.$appliedleavescount.'</td>
                      </tr>
                     
                      <tr bgcolor="#e9f6fc">
                        <td style="border-right:2px solid #BBBBBB;">From</td>
                        <td>'.$from_date.'</td>
                      </tr>
                      <tr>
                        <td style="border-right:2px solid #BBBBBB;">To</td>
                        <td>'.$to_date.'</td>
                 	 </tr>
                 	  <tr>
    	                 <td style="border-right:2px solid #BBBBBB;">Leave Type</td>
                        <td>'.$leavetypetext.'</td>
                        </tr>
                      <tr bgcolor="#e9f6fc">
                        <td style="border-right:2px solid #BBBBBB;">Reason for Leave </td>
                        <td>'.$reason.'</td>
                  </tr>
                  <tr bgcolor="#e9f6fc">
                        <td style="border-right:2px solid #BBBBBB;">Reason for Leave </td>
                        <td>'.$comment.'</td>
                  </tr>
                  
                  <tr>
                        <td style="border-right:2px solid #BBBBBB;">Reporting Manager</td>
                        <td>'.$reportingmanagerName.'</td>
                  </tr>
                </tbody></table>

            </div>
            <div style="padding:20px 0 10px 0;">Please <a href="'.$url.'" target="_blank" style="color:#b3512f;">click here</a> to login and check the leave details.</div>
            </div>';
                                $result = sapp_Global::_sendEmail($options);	
							}		
							/* END */
							
						
							/* Mail to the applied employee*/
							$options['cc']=array();
								$toemailArr = $loginUserEmail;
                                $options['subject'] = 'Leave Request - ' . $loginUserName . ' #'.$id;

								$options['header'] = 'Request for Approved leave cancellation';
								$options['toEmail'] = $toemailArr;
								$options['toName'] = $loginUserName;
								$options['message'] = '<div>
												<div>Hi,</div>
												<div>A leave request raised by you is sent for your managers approval.</div>
<div>
                <table width="100%" cellspacing="0" cellpadding="15" border="0" style="border:3px solid #BBBBBB; font-size:16px; font-family:Arial, Helvetica, sans-serif; margin:30px 0 30px 0;" bgcolor="#ffffff">
                      <tbody>
                      <tr>
                        <td width="28%" style="border-right:2px solid #BBBBBB;">Employee Name</td>
                        <td width="72%">'.$loginUserName.'</td>
                      </tr>
                      <tr bgcolor="#e9f6fc">
                        <td style="border-right:2px solid #BBBBBB;">No. of Day(s)</td>
                        <td>'.$appliedleavescount.'</td>
                      </tr>
                      <tr>
                        <td style="border-right:2px solid #BBBBBB;">From</td>
                        <td>'.$from_date.'</td>
                      </tr>
                      <tr bgcolor="#e9f6fc">
                        <td style="border-right:2px solid #BBBBBB;">To</td>
                        <td>'.$to_date.'</td>
            	     </tr>
	    	          <tr>
    	                 <td style="border-right:2px solid #BBBBBB;">Leave Type</td>
                        <td>'.$leavetypetext.'</td>
                        </tr>
                      <tr bgcolor="#e9f6fc">
                        <td style="border-right:2px solid #BBBBBB;">Reason for Leave</td>
                        <td>'.$reason.'</td>
                  </tr>
                  <tr bgcolor="#e9f6fc">
                        <td style="border-right:2px solid #BBBBBB;">Reason for Leave </td>
                        <td>'.$comment.'</td>
                  </tr>
                </tbody></table>

            </div>
            <div style="padding:20px 0 10px 0;">Please <a href="'.BASE_URL.'" target="_blank" style="color:#b3512f;">click here</a> to login and check the leave details.</div>												
            </div>';
                                $result = sapp_Global::_sendEmail($options);	
							
					
					//$menuID = LEAVEREQUEST;
					//$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
                    $this->_helper->json(array('result'=>'saved',
												'message'=>'Approved Leave Cancellation request applied successfully.',
												'controller'=>'approvedleaves'
										));	
                   /*end ========================*/
			}
			else
			{ 
			 $messages['message'] = 'Approved Leave Cancellation request cannot be cancelled.';
			}
			$this->_helper->json($messages);
		
	}
}

