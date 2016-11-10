<?php

class Default_PendingleavesController extends Zend_Controller_Action
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
				
			$sort = 'DESC';$by = 'modifieddate';$pageNo = 1;$searchData = '';
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
			// search from grid - START 
			$searchData = $this->_getParam('searchData');	
			$searchData = rtrim($searchData,',');
			// search from grid - END 
		}
				
		$objName = 'pendingleaves';
		$queryflag = 'pending';
		
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
						if(!empty($data) && $data['leavestatus'] == 'Pending for approval')
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
								if(!empty($repmngrnameArr))
								 $leaverequestform->rep_mang_id->setValue($repmngrnameArr[0]['userfullname']);
								else 
								  $leaverequestform->rep_mang_id->setValue('');
								$leaverequestform->setDefault('leavetypeid',$data['leavetypeid']);
								$leaverequestform->setDefault('leaveday',$data['leaveday']);
								$this->view->controllername = $objName;
								$this->view->id = $id;
								$this->view->form = $leaverequestform;
								$this->view->data = $data;
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
		 $comment = $this->_request->getParam('comment');
		 $id = $this->_request->getParam('objid');
		 $messages['message'] = '';
		 $actionflag = 5;
		 $businessunitid = '';
		 $leavetypetext = '';

		    if($id)
			{
			$leaverequestmodel = new Default_Model_Leaverequest();
			$usersmodel = new Default_Model_Users();
			$employeesmodel = new Default_Model_Employees();
			$employeeleavetypesmodel = new Default_Model_Employeeleavetypes();
			
			$loggedInEmployeeDetails = $employeesmodel->getLoggedInEmployeeDetails($loginUserId);
				 if($loggedInEmployeeDetails[0]['businessunit_id'] != '')
					$businessunitid = $loggedInEmployeeDetails[0]['businessunit_id'];
								
			  $dataarr = array('leavestatus'=>4,'requester_comments'=>$comment,'modifieddate'=>gmdate("Y-m-d H:i:s"),'modifiedby'=>$loginUserId);
			  $where = array('id=?'=>$id);
			  
			  $Id = $leaverequestmodel->SaveorUpdateLeaveRequest($dataarr, $where);
			  $data = $leaverequestmodel->getsinglePendingLeavesData($id);
			  $data = $data[0];
			  $appliedleavesdaycount = $data['appliedleavescount'];
			  $to_date = $data['to_date'];			  
			  $from_date = $data['from_date'];
			  $reason = $data['reason'];
			  $leavetypeid = $data['leavetypeid'];
			   $leavetypedata = $employeeleavetypesmodel->getLeavetypeDataByID($leavetypeid);
		      $leavetypetext = $leavetypedata[0]['leavetype'];
			  $repmngrnameArr = $usersmodel->getUserDetailsByID($data['rep_mang_id']);
			  $reportingmanageremail = $repmngrnameArr[0]['emailaddress'];	
              $reportingmanagername	= $repmngrnameArr[0]['userfullname'];		  
			    if($Id == 'update')
				{
				   $menuID = PENDINGLEAVES;
				   $result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id); 
				    /** MAILING CODE **/
					
					if($to_date == '' || $to_date == NULL)
				      $to_date = $from_date;
							/* Mail to Employee */
                                $options['subject'] = 'Leave Request - ' . $loginUserName . ' #'.$id;
								$options['header'] = 'Leave Request';
								$options['toEmail'] = $loginUserEmail;	
								$options['toName'] = $loginUserName;
								$options['message'] = '<div>Hi,</div>
								<div>The below leave(s) has been cancelled.</div>
								<div>
                <table width="100%" cellspacing="0" cellpadding="15" border="0" style="border:3px solid #BBBBBB; font-size:16px; font-family:Arial, Helvetica, sans-serif; margin:30px 0 30px 0;" bgcolor="#ffffff">
                      <tbody><tr>
                        <td width="28%" style="border-right:2px solid #BBBBBB;">Employee Name</td>
                        <td width="72%">'.$loginUserName.'</td>
                      </tr>
                      <tr bgcolor="#e9f6fc">
                        <td style="border-right:2px solid #BBBBBB;">No. of Day(s)</td>
                        <td>'.$appliedleavesdaycount.'</td>
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
                  <tr bgcolor="#e9f6fc">
                        <td style="border-right:2px solid #BBBBBB;">Cancelled By</td>
                        <td>'.$loginUserName.'</td>
                  </tr>
                  <tr bgcolor="#e9f6fc">
                            <td style="border-right:2px solid #BBBBBB;">Reason for Leave Cancellation</td>
                            <td>'.$comment.'</td>
                        </tr>
                </tbody></table>

            </div>
            <div style="padding:20px 0 10px 0;">Please <a href="'.BASE_URL.'" target="_blank" style="color:#b3512f;">click here</a> to login and check the leave details.</div>';	
								$result = sapp_Global::_sendEmail($options);
								/* End */
								
								/* Mail to Reporting Manager */
                                $options['subject'] = 'Leave Request - ' . $loginUserName . ' #'.$id;
								$options['header'] = 'Leave Request';
								$options['toEmail'] = $reportingmanageremail;
								$options['toName'] = $reportingmanagername;
								$cc_temp = EMAIL_LEAVE_CC;
								$cc = explode(",", $cc_temp);
								$options['cc'] = $cc;
                                $url = BASE_URL. "manageremployeevacations";
								$options['message'] = '<div>Hi,</div>
								<div>The below leave(s) has been cancelled.</div>
								<div>
                <table width="100%" cellspacing="0" cellpadding="15" border="0" style="border:3px solid #BBBBBB; font-size:16px; font-family:Arial, Helvetica, sans-serif; margin:30px 0 30px 0;" bgcolor="#ffffff">
                      <tbody><tr>
                        <td width="28%" style="border-right:2px solid #BBBBBB;">Employee Name</td>
                        <td width="72%">'.$loginUserName.'</td>
                      </tr>
                      <tr bgcolor="#e9f6fc">
                        <td style="border-right:2px solid #BBBBBB;">No. of Day(s)</td>
                        <td>'.$appliedleavesdaycount.'</td>
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
                        <tr bgcolor="#e9f6fc">
                        <td style="border-right:2px solid #BBBBBB;">Cancelled By</td>
                        <td>'.$loginUserName.'</td>
                  </tr>
                        <tr bgcolor="#e9f6fc">
                            <td style="border-right:2px solid #BBBBBB;">Reason for Leave Cancellation</td>
                            <td>'.$comment.'</td>
                        </tr>
                </tbody></table>

            </div>
            <div style="padding:20px 0 10px 0;">Please <a href="'.$url.'" target="_blank" style="color:#b3512f;">click here</a> to login and check the leave details.</div>';	
								$result = sapp_Global::_sendEmail($options);
								/* End */
								
								
											
					$messages['message'] = 'Leave request cancelled';  
					$messages['msgtype'] = 'success';				   
				}   
				else
				{
                   $messages['message'] = 'Leave request cannot be cancelled';	
					$messages['msgtype'] = 'error';				   
				}
			}
			else
			{ 
			 $messages['message'] = 'Leave request cannot be cancelled';
			 $messages['msgtype'] = 'error';
			}
			$this->_helper->json($messages);
		
	}
}

