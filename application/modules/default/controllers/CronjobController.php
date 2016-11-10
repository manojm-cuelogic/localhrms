<?php

class Default_CronjobController extends Zend_Controller_Action
{

    private $options;
    private $cron_key = "75EB3BBDDA977B539FE75E38776B6";
    public function preDispatch()
    {               
    }
    
    public function init()
    {
        $this->_options= $this->getInvokeArg('bootstrap')->getOptions();        
    }

    public function indexAction()
    {
    $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout()->disableLayout();
        
        $date = new Zend_Date();
   
        $email_model = new Default_Model_EmailLogs();
        $cron_model = new Default_Model_Cronstatus();
        // appraisal notifications
       $this->checkperformanceduedate();
        // feed forward notifications        
       // $this->checkffduedate();
        
        $cron_status = $cron_model->getActiveCron('General');
        if($cron_status == 'yes')
        {
            try
            {
                //updating cron status table to in-progress
                $cron_data = array(
                    'cron_status' => 1,
                    'cron_type' => 'General',
                    'started_at' => gmdate("Y-m-d H:i:s"),
                );

                $cron_id = $cron_model->SaveorUpdateCronStatusData($cron_data, '');

                if($cron_id != '')
                {
                    $mail_data = $email_model->getNotSentMails();
                    if(count($mail_data) > 0)
                    {
                        foreach($mail_data as $mdata)
                        {
                            $options = array();
                            $options['header'] = $mdata['header'];
                            $options['message'] = $mdata['message'];
                            $options['subject'] = $mdata['emailsubject'];
                            $options['toEmail'] = $mdata['toEmail'];
                            $options['toName'] = $mdata['toName'];
                            if($mdata['cc'] != '')
                                $options['cc'] = $mdata['cc'];
                            if($mdata['bcc'] != '')
                                $options['bcc'] = $mdata['bcc'];
                            // to send email
                            
                            $mail_status = sapp_Mail::_email($options);
                          
                            $mail_where = array('id=?' => $mdata['id']);
                            $new_maildata['modifieddate'] = gmdate("Y-m-d H:i:s");
                          
                            if($mail_status === true)
                            {      
                                $new_maildata['is_sent'] = 1;                          
                                //to udpate email log table that mail is sent.
                                $id = $email_model->SaveorUpdateEmailData($new_maildata,$mail_where);                                 
                            }                                               
                        }//end of for loop
                        
                    }//end of mails count if
                    //updating cron status table to completed.                    
                    $cron_data = array(
                        'cron_status' => 0,                      
                        'completed_at' => gmdate("Y-m-d H:i:s"),
                    );
                    $cron_id = $cron_model->SaveorUpdateCronStatusData($cron_data, "id = ".$cron_id);
                }//end of cron status id if  
            }
            catch(Exception $e)
            {
                
            }
        }//end of cron status if
       
        
    }//end of index action
    
    
    /**
     * This action is used to send mails to employees for passport expiry,and credit card expiry(personal details screen)
     */
    public function empdocsexpiryAction()
    {
        
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout()->disableLayout();
        
        $email_model = new Default_Model_EmailLogs();
        $cron_model = new Default_Model_Cronstatus();
        
        $cron_status = $cron_model->getActiveCron('Emp docs expiry');
                
        if($cron_status == 'yes')
        {
            try
            {
                //updating cron status table to in-progress
                $cron_data = array(
                    'cron_status' => 1,
                    'cron_type' => 'Emp docs expiry',
                    'started_at' => gmdate("Y-m-d H:i:s"),
                );

                $cron_id = $cron_model->SaveorUpdateCronStatusData($cron_data, '');

                if($cron_id != '')
                {
                    $calc_date = new DateTime(date('Y-m-d'));
                    $calc_date->add(new DateInterval('P1M'));
                    $print_date = $calc_date->format(DATEFORMAT_PHP);
                    $calc_date = $calc_date->format('Y-m-d');
                    $mail_data = $email_model->getEmpDocExpiryData($calc_date);
                    if(count($mail_data) > 0)
                    {
                        foreach($mail_data as $mdata)
                        {                            
                            $view = $this->getHelper('ViewRenderer')->view;
                            $this->view->emp_name = $mdata['name'];                           
                            $this->view->docs_arr = $mdata['docs'];
                            $this->view->expiry_date = $print_date;
                            $text = $view->render('mailtemplates/empdocsexpirycron.phtml');
                            $options['subject'] = APPLICATION_NAME.': Documents expiry';
                            $options['header'] = 'Greetings from '.APPLICATION_NAME;
                            $options['toEmail'] = $mdata['email'];  
                            $options['toName'] = $mdata['name'];
                            $options['message'] = $text;                            
                            
                            sapp_Global::_sendEmail($options);
                        }
                    }
                    $cron_data = array(
                            'cron_status' => 0,
                            'completed_at' => gmdate("Y-m-d H:i:s"),
                        );
                    $cron_id = $cron_model->SaveorUpdateCronStatusData($cron_data, "id = ".$cron_id);
                }//end of cron status id if  
            }
            catch(Exception $e)
            {
                
            }
        }//end of cron status if
    }//end of emp expiry action
    
    /**
     * This action is used to send mails to employees for passport expiry,and credit card expiry(visa and immigration screen)
     */
    public function empexpiryAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout()->disableLayout();
        
        $email_model = new Default_Model_EmailLogs();
        $cron_model = new Default_Model_Cronstatus();
        
        $cron_status = $cron_model->getActiveCron('Employee expiry');
        
        $earr = array('i94' => 'I94','visa' => 'Visa' ,'passport' => 'Passport');
        if($cron_status == 'yes')
        {
            try
            {
                //updating cron status table to in-progress
                $cron_data = array(
                    'cron_status' => 1,
                    'cron_type' => 'Employee expiry',
                    'started_at' => gmdate("Y-m-d H:i:s"),
                );

                $cron_id = $cron_model->SaveorUpdateCronStatusData($cron_data, '');

                if($cron_id != '')
                {
                    $calc_date = new DateTime(date('Y-m-d'));
                    $calc_date->add(new DateInterval('P1M'));
                    $print_date = $calc_date->format(DATEFORMAT_PHP);
                    $calc_date = $calc_date->format('Y-m-d');
                    $mail_data = $email_model->getEmpExpiryData($calc_date);
                    if(count($mail_data) > 0)
                    {
                        foreach($mail_data as $mdata)
                        {
                            $base_url = 'http://'.$this->getRequest()->getHttpHost() . $this->getRequest()->getBaseUrl();
                            $view = $this->getHelper('ViewRenderer')->view;
                            $this->view->emp_name = $mdata['userfullname'];                           
                            $this->view->etype = $earr[$mdata['etype']];                                                                                                                
                            $this->view->expiry_date = $print_date;
                            $text = $view->render('mailtemplates/empexpirycron.phtml');
                            $options['subject'] = APPLICATION_NAME.': '.$earr[$mdata['etype']].' renewal';
                            $options['header'] = 'Greetings from '.APPLICATION_NAME;
                            $options['toEmail'] = $mdata['emailaddress'];  
                            $options['toName'] = $mdata['userfullname'];
                            $options['message'] = $text;
                            $options['cron'] = 'yes';
                            
                            sapp_Global::_sendEmail($options);
                        }
                    }
                    $cron_data = array(
                            'cron_status' => 0,
                            'completed_at' => gmdate("Y-m-d H:i:s"),
                        );
                    $cron_id = $cron_model->SaveorUpdateCronStatusData($cron_data, "id = ".$cron_id);
                }//end of cron status id if  
            }
            catch(Exception $e)
            {
                
            }
        }//end of cron status if
    }//end of emp expiry action
    
    /**
     * This action is to remind managers to approve leaves of his team members before end of month.
     */
    public function leaveapproveAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout()->disableLayout();
        
        $email_model = new Default_Model_EmailLogs();
        $cron_model = new Default_Model_Cronstatus();
        
        $cron_status = $cron_model->getActiveCron('Approve leave');
                
        if($cron_status == 'yes')
        {
            try
            {
                //updating cron status table to in-progress
                $cron_data = array(
                    'cron_status' => 1,
                    'cron_type' => 'Approve leave',
                    'started_at' => gmdate("Y-m-d H:i:s"),
                );

                $cron_id = $cron_model->SaveorUpdateCronStatusData($cron_data, '');

                if($cron_id != '')
                {
                    $from_date = date('Y-m-01');
                    $to_date = date('Y-m-d');
                    $mail_data = $email_model->getLeaveApproveData($from_date,$to_date);
                    if(count($mail_data) > 0)
                    {
                        foreach($mail_data as $mdata)
                        {
                            $base_url = 'http://'.$this->getRequest()->getHttpHost() . $this->getRequest()->getBaseUrl();
                            $view = $this->getHelper('ViewRenderer')->view;
                            $this->view->emp_name = $mdata['mng_name'];
                            $this->view->team = $mdata['team'];
                            $text = $view->render('mailtemplates/leaveapprovecron.phtml');
                            $options['subject'] = APPLICATION_NAME.': Leave(s) pending for approval';
                            $options['header'] = 'Pending Leaves';
                            $options['toEmail'] = $mdata['mng_email'];
                            $options['toName'] = $mdata['mng_name'];
                            $options['message'] = $text;
                            $options['cron'] = 'yes';
                           
                            sapp_Global::_sendEmail($options);
                        }
                    }
                    $cron_data = array(
                            'cron_status' => 0,
                            'completed_at' => gmdate("Y-m-d H:i:s"),
                        );
                    $cron_id = $cron_model->SaveorUpdateCronStatusData($cron_data, "id = ".$cron_id);
                }//end of cron status id if  
            }
            catch(Exception $e)
            {
                
            }
        }//end of cron status if
    }//end of leave approve action
    /**
     * This action is to send email to HR group when due date of requisition is completed.
     */
    public function requisitionAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout()->disableLayout();
        
        $email_model = new Default_Model_EmailLogs();
        $cron_model = new Default_Model_Cronstatus();
        
        $cron_status = $cron_model->getActiveCron('Requisition expiry');
                
        if($cron_status == 'yes')
        {
            try
            {
                //updating cron status table to in-progress
                $cron_data = array(
                    'cron_status' => 1,
                    'cron_type' => 'Requisition expiry',
                    'started_at' => gmdate("Y-m-d H:i:s"),
                );

                $cron_id = $cron_model->SaveorUpdateCronStatusData($cron_data, '');

                if($cron_id != '')
                {
                    $calc_date = new DateTime(date('Y-m-d'));
                    $calc_date->add(new DateInterval('P15D'));
                    $print_date = $calc_date->format(DATEFORMAT_PHP);
                    $calc_date = $calc_date->format('Y-m-d');
                    $mail_data = $email_model->getRequisitionData($calc_date);
                    if(count($mail_data) > 0)
                    {
                        foreach($mail_data as $did => $mdata)
                        {
                            if(defined("REQ_HR_".$did))
                            {
                                $base_url = 'http://'.$this->getRequest()->getHttpHost() . $this->getRequest()->getBaseUrl();
                                $view = $this->getHelper('ViewRenderer')->view;
                                $this->view->emp_name = "HR";
                                $this->view->print_date = $print_date;
                                $this->view->req = $mdata['req'];
                                $this->view->base_url = $base_url;
                                $text = $view->render('mailtemplates/requisitioncron.phtml');
                                $options['subject'] = APPLICATION_NAME.': Renew requisition expiry';
                                $options['header'] = 'Requisition Expiry';
                                $options['toEmail'] = constant("REQ_HR_".$did);
                                $options['toName'] = "HR";
                                $options['message'] = $text;
                                $options['cron'] = 'yes';

                                sapp_Global::_sendEmail($options);
                            }
                        }
                    }
                    $cron_data = array(
                            'cron_status' => 0,
                            'completed_at' => gmdate("Y-m-d H:i:s"),
                        );
                    $cron_id = $cron_model->SaveorUpdateCronStatusData($cron_data, "id = ".$cron_id);
                }//end of cron status id if  
            }
            catch(Exception $e)
            {
             
            }
        }//end of cron status if
    }//end of requisition action.
    
    /**
     * This action is used to send notification to inactive users.
     */
    public function inactiveusersAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout()->disableLayout();
        
        $email_model = new Default_Model_EmailLogs();
        $cron_model = new Default_Model_Cronstatus();
        
        $cron_status = $cron_model->getActiveCron('Inactive users');
                
        if($cron_status == 'yes')
        {
            try
            {
                //updating cron status table to in-progress
                $cron_data = array(
                    'cron_status' => 1,
                    'cron_type' => 'Inactive users',
                    'started_at' => gmdate("Y-m-d H:i:s"),
                );

                $cron_id = $cron_model->SaveorUpdateCronStatusData($cron_data, '');

                if($cron_id != '')
                {
                    $calc_date = new DateTime(date('Y-m-d'));
                    $calc_date->sub(new DateInterval('P3M'));
                    $print_date = $calc_date->format(DATEFORMAT_PHP);
                    $calc_date = $calc_date->format('Y-m-d');
                    $mail_data = $email_model->getInactiveusersData($calc_date);
                    if(count($mail_data) > 0)
                    {
                        foreach($mail_data as $did => $mdata)
                        {                            
                            $base_url = 'http://'.$this->getRequest()->getHttpHost() . $this->getRequest()->getBaseUrl();
                            $view = $this->getHelper('ViewRenderer')->view;
                            $this->view->emp_name = $mdata['userfullname'];
                            $this->view->print_date = $print_date;
                            $this->view->user_id = $mdata['employeeId'];
                            $this->view->base_url = $base_url;
                            $text = $view->render('mailtemplates/inactiveusercron.phtml');
                            $options['subject'] = APPLICATION_NAME.': Cuelogic HRMS account inactivated';
                            $options['header'] = 'Employee inactivated';
                            $options['toEmail'] = $mdata['emailaddress'];
                            $options['toName'] = $mdata['userfullname'];
                            $options['message'] = $text;
                            $options['cron'] = 'yes';

                            sapp_Global::_sendEmail($options);
                        }
                    }
                    $cron_data = array(
                            'cron_status' => 0,
                            'completed_at' => gmdate("Y-m-d H:i:s"),
                        );
                    $cron_id = $cron_model->SaveorUpdateCronStatusData($cron_data, "id = ".$cron_id);
                }//end of cron status id if  
            }
            catch(Exception $e)
            {
                
            }
        }//end of cron status if
    }//end of inactiveusers action.
    

    /**
     * This action is used to save update json in logmanager(removes 30 days before content  and saves in logmanagercron) .
     */
      
    public function logcronAction(){
    
     $this->_helper->viewRenderer->setNoRender(true);
     $this->_helper->layout()->disableLayout();
        
       $logmanager_model = new Default_Model_Logmanager();
       $logmanagercron_model = new Default_Model_Logmanagercron();
       $logData = $logmanager_model->getLogManagerData();
       $i = 0;
       if(count($logData) > 0){
         foreach($logData as $record){
             if(isset($record['log_details']) && !empty($record['log_details'])){
                $id = $record['id'];
                $menuId = $record['menuId'];
                $actionflag = $record['user_action'];
                $userid = $record['last_modifiedby'];
                $keyflag = $record['key_flag'];
                $date = $record['last_modifieddate'];
                $jsondetails = '{"testjson":['.$record['log_details'].']}';
                $jsonarr = @get_object_vars(json_decode($jsondetails));
                $mainTableJson = '';
                $cronTableJson = '';
                if(!empty($jsonarr))
                {
                  $mainJsonArrayCount = count($jsonarr['testjson']);
                  foreach($jsonarr['testjson'] as $key => $json){
                   $jsonVal = @get_object_vars($json);
                   if(!empty($jsonVal)){
                        $jsondate = explode(' ',$jsonVal['date']);
                        $datetime1 = new DateTime($jsondate[0]);
                        $datetime2 = new DateTime();                 
                        $interval = $datetime1->diff($datetime2);
                        $interval = $interval->format('%a');
                        if($interval > 30){
                          if($cronTableJson == ''){
                             $cronTableJson .=  json_encode($jsonVal);
                           }else{
                             $cronTableJson .=  ','.json_encode($jsonVal);
                           }
                         if(isset($jsonVal['recordid']) && $jsonVal['recordid'] != ''){
                            $keyflag = $jsonVal['recordid'];
                         }                                                             
                        }else{
                           if($mainTableJson == ''){
                             $mainTableJson .=  json_encode($jsonVal);
                           }else{
                             $mainTableJson .=  ','.json_encode($jsonVal);
                           }                          
                        }
                   }
                   if(($mainJsonArrayCount-1) == $key){ // if all are greater than 30 days 
                     if($mainTableJson == ''){
                        $mainTableJson .=  json_encode($jsonVal);
                    }
                   }
                 }  
                 try{ 
                 
                     if($cronTableJson != '' && $mainTableJson != ''){
                        $result = $logmanager_model->UpdateLogManagerWhileCron($id,$mainTableJson);
                         if($result){
                           $InsertId = $logmanagercron_model->InsertLogManagerCron($menuId,$actionflag,$cronTableJson,$userid,$keyflag,$date);
                         }
                         $i++;
                     }
                                
                  }catch(Exception $e){
                     echo $e->getMessage(); exit;
                  }
                }
             }       
         
         }    
        
       }
    }
    
    public function checkperformanceduedate()
    {
         $app_init_model = new Default_Model_Appraisalinit();
         $app_ratings_model = new Default_Model_Appraisalemployeeratings();
         $active_appraisal_Arr = $app_init_model->getActiveAppraisals();
         $appraisalPrivMainModel = new Default_Model_Appraisalqsmain();
         $app_manager_model = new Default_Model_Appraisalmanager();
         $usersmodel = new Default_Model_Users();
         $current_day = new DateTime('now');
         $current_day->sub(new DateInterval('P1D'));
         if(!empty($active_appraisal_Arr))
         {
                foreach($active_appraisal_Arr as $appval)
                {
                    
                        if($appval['managers_due_date'])
                            $manager_due_date = new DateTime($appval['managers_due_date']);
                        else
                            $manager_due_date = ''; 
                        if($appval['employees_due_date'])   
                            $emp_due_date = new DateTime($appval['employees_due_date']);
                        else
                            $emp_due_date = ''; 
                            
                            $due_date = ($appval['enable_step'] == 2)? $emp_due_date : $manager_due_date;
                            
                            $interval = $current_day->diff($due_date);
                            $interval->format('%d');
                            $interval=$interval->days;
                            
                            $appIdArr = array();
                            $appIdList = '';
                            if($interval<=2)
                            {
                                
                            if($appval['enable_step'] == 2)
                            {
                                
                            $employeeidArr = $app_ratings_model->getEmployeeIds($appval['id'],'cron');
                            if(!empty($employeeidArr))
                            {
                                $empIdArr = array();
                                $empIdList = '';
                                foreach($employeeidArr as $empval)
                                {
                                    array_push($empIdArr,$empval['employee_id']);
                                }
                                if(!empty($empIdArr))
                                {
                                    $empIdList = implode(',',$empIdArr);
                                    $employeeDetailsArr = $app_manager_model->getUserDetailsByEmpID($empIdList); //Fetching employee details
                                    
                                 if(!empty($employeeDetailsArr))
                                        {
                                            $empArr = array();
                                            foreach($employeeDetailsArr as $emp)
                                            {  
                                                array_push($empArr,$emp['emailaddress']); //preparing Bcc array
                                                
                                            }
                                        
                                        $optionArr = array('subject'=>'Self Appraisal Submission Pending',
                                                          'header'=>'Performance Appraisal',
                                                          'toemail'=>SUPERADMIN_EMAIL,  
                                                          'toname'=>'Super Admin',
                                                          'bcc'   => $empArr,
                                                          'message'=>"<div style='padding: 0; text-align: left; font-size:14px; font-family:Arial, Helvetica, sans-serif;'>             
                                                        <span style='color:#3b3b3b;'>Hi, </span><br />
                                                        <div style='padding:20px 0 0 0;color:#3b3b3b;'>Self appraisal submission is pending.</div>
                                                        <div style='padding:20px 0 10px 0;'>Please <a href=".BASE_URL." target='_blank' style='color:#b3512f;'>click here</a> to login  to your <b>".APPLICATION_NAME."</b> account to check the details.</div>
                                                        </div> ",
                                                          'cron'=>'yes');
                                        sapp_PerformanceHelper::saveCronMail($optionArr);
                                    }
                                }   
                        }
                     
                    }
                    else
                    {
                        
                        $getLine1ManagerId = $appraisalPrivMainModel->getLine1ManagerIdMain($appval['id']); 
                        if(!empty($getLine1ManagerId))
                        {
                            $empArr = array();
                            foreach($getLine1ManagerId as $val)
                            {
                              array_push($empArr,$val['emailaddress']); //preparing Bcc array
                            }
                                        $optionArr = array('subject'=>'Manager Appraisal Submission Pending',
                                                          'header'=>'Performance Appraisal',
                                                          'toemail'=>SUPERADMIN_EMAIL,  
                                                          'toname'=>'Super Admin',
                                                          'bcc'   => $empArr,
                                                          'message'=>"<div style='padding: 0; text-align: left; font-size:14px; font-family:Arial, Helvetica, sans-serif;'>             
                                                        <span style='color:#3b3b3b;'>Hi, </span><br />
                                                        <div style='padding:20px 0 0 0;color:#3b3b3b;'>Manager appraisal submission is pending.</div>
                                                        <div style='padding:20px 0 10px 0;'>Please <a href=".BASE_URL." target='_blank' style='color:#b3512f;'>click here</a> to login  to your <b>".APPLICATION_NAME."</b> account to check the details.</div>
                                                        </div> ",
                                                          'cron'=>'yes');
                                        sapp_PerformanceHelper::saveCronMail($optionArr);
                                        
                                    
                        }   
                }
            }
           }
         }
    }
    
    public function checkffduedate()
    {
        $ffinitModel = new Default_Model_Feedforwardinit();
        $ffEmpRatModel = new Default_Model_Feedforwardemployeeratings;
        
        $ffDataArr = $ffinitModel->getFFbyBUDept('','yes');
        $ffIdArr = array();
        $ffIdList = '';
        $current_day = new DateTime('now');
        $current_day->sub(new DateInterval('P1D'));
        
        if(!empty($ffDataArr))
        {
            foreach($ffDataArr as $ffval)
            {
                if($ffval['status'] == 1)
                {
                    if($ffval['ff_due_date'])
                        $due_date = new DateTime($ffval['ff_due_date']);
                    else
                        $due_date = '';
                        $interval = $current_day->diff($due_date);
                                    $interval->format('%d');
                                    $interval=$interval->days;
                                    if($interval<=2)
                                    {
                                        array_push($ffIdArr,$ffval['id']);
                                    }
                }
            }
        }
        if(!empty($ffIdArr))
        {
             $ffIdList = implode(',',$ffIdArr);
        }
                  if($ffIdList != '')
                  {
                    $ffEmpsStatusData = $ffEmpRatModel->getEmpsFFStatus($ffIdList,'cron');
                    if(!empty($ffEmpsStatusData))
                        {
                            $empIdArr = array();
                            foreach($ffEmpsStatusData as $empval)
                            {
                                array_push($empIdArr,$empval['emailaddress']);
                            }
                                        $optionArr = array('subject'=>'Manager Feedforward submission pending',
                                                          'header'=>'Feedforward',
                                                          'toemail'=>SUPERADMIN_EMAIL,  
                                                          'toname'=>'Super Admin',
                                                          'bcc'   => $empIdArr, 
                                                          'message'=>"<div style='padding: 0; text-align: left; font-size:14px; font-family:Arial, Helvetica, sans-serif;'>             
                                                        <span style='color:#3b3b3b;'>Hi, </span><br />
                                                        <div style='padding:20px 0 0 0;color:#3b3b3b;'>Mangaer feedforward is pending.</div>
                                                        <div style='padding:20px 0 10px 0;'>Please <a href=".BASE_URL." target='_blank' style='color:#b3512f;'>click here</a> to login  to your <b>".APPLICATION_NAME."</b> account to check the details.</div>
                                                        </div> ",
                                                          'cron'=>'yes');
                                        sapp_PerformanceHelper::saveCronMail($optionArr);
                                    
                                
                            
                        }
                  }
                
            
        
    }
    /**/

    public function creditmonthlyemployeeleavesAction(){
        $key = $this->getRequest()->getParam('key');
        if($key == $this->cron_key) {
            $usersmodel = new Default_Model_Users;
            $employees_id=$usersmodel->getActiveUsersId();
            
            //assigining PL/SL to each employee
            $employeeleavesmodel = new Default_Model_Employeeleaves;
            if(!$employeeleavesmodel->isCronExecuted()) { 
                $employees_id=$employeeleavesmodel->creditEmployeeMonthlyLeaves($employees_id);
            } else {
                echo "Monthly leaves have already credited for this month";exit;
            }
        } else {
                echo "Invalid Request";exit;
        }

        
    }
    public function leaveremindersAction () {
        $key = $this->getRequest()->getParam('key');
        if($key == $this->cron_key) {
            $holidaydatesmodel = new Default_Model_Holidaydates();
            $holidayList = $holidaydatesmodel->getHolidayDatesList();
            $holidays = array();
            foreach($holidayList as $h) {
                $holidays[] = strtotime($h['holidaydate']);
            }
            $dt = date("Y-m-d");
            $week = date("w");
            if($week == 0 || $week == 6) {echo "Today is weekoff";exit;}
            $tm = strtotime($dt);
            if(in_array($tm, $holidays)) { echo "Today is company holiday"; exit; }
            $managerIdP = $this->pastLeaveReminders();
            $managerIdF = $this->futureLeaveReminders();
            $managers = array_unique(array_merge($managerIdP,$managerIdF));
            $this->sendReminderEmail($managers);
            exit;
        } else {
                echo "Invalid Request";exit;
        }
    }

    public function pastLeaveReminders () {

        $leaveRequest = new Default_Model_Leaverequest();
        $dateObj = $this->getFromToWorkingDate("-", 5, false);
        $leaves = $leaveRequest->getPendingLeavesBetweenDate($dateObj['fromDate'], $dateObj['toDate']);
        $managerId = array();
        foreach($leaves as $leave) {
            if(in_array($leave['rep_mang_id'], $managerId))
                continue;
            $managerId[] = $leave['rep_mang_id'];
        }
        return $managerId;
    }

    public function futureLeaveReminders () {
        $leaveRequest = new Default_Model_Leaverequest();
        $dateObj = $this->getFromToWorkingDate();
        $leaves = $leaveRequest->getPendingLeavesBetweenDate($dateObj['fromDate'], $dateObj['toDate']);
        $managerId = array();
        foreach($leaves as $leave) {
            if(in_array($leave['rep_mang_id'], $managerId))
                continue;
            $managerId[] = $leave['rep_mang_id'];
        }
        return $managerId;
    }

    public function sendReminderEmail($managers) {
        $usersmodel = new Default_Model_Users();
        $url = BASE_URL . "manageremployeevacations";
        foreach($managers as $mid) {
            $manager = $usersmodel->getUserDetailsByID($mid);
            $manager = $manager[0];
            $options = array();
            $options['header'] = 'HRMS - Reminder for the pending leaves of your team';
            $options['toEmail'] = $manager['emailaddress'];        
            $options['cc'] = array();
            $options['toName'] =  $manager['userfullname'];
            $options['subject'] = 'HRMS - Reminder for the pending leaves of your team';
            $options['message'] = '<div>Dear ' . $manager['firstname'] . ',</div><div>This is a reminder email to take any action about the pending leaves of your team members.</div>';
            $options['message'] .= '<div style="padding:20px 0 10px 0;">Please <a href="'.$url.'" target="_blank" style="color:#b3512f;">click here</a> to login and check the leave details.</div>';
            
            $result = sapp_Global::_sendEmail($options);
        }
    }

    public function getFromToWorkingDate($sign = '+', $limit = 3, $includeToday = true) {
        $holidaydatesmodel = new Default_Model_Holidaydates();
        $holidayList = $holidaydatesmodel->getHolidayDatesList();
        $holidays = array();
        foreach($holidayList as $h) {
            $holidays[] = strtotime($h['holidaydate']);
        }
        $dateArr = array();
        $count = ($includeToday) ? 0 : 1;
        $fromDate = "";
        $toDate = "";
        while(true) {
            $dt = date('Y-m-d',strtotime($sign ."".$count++." days"));
            $dayofweek = date('w',strtotime($dt));
            if($dayofweek == 0 || $dayofweek == 6){

            } else {
                if(!in_array(strtotime($dt),$holidays)){
                    if(count($dateArr) == 0)
                        $fromDate = $dt;
                    else if(count($dateArr) < $limit)
                        $toDate = $dt;
                    else break;
                    $dateArr[] = strtotime($dt);
                }
            }
        }
        if($sign == "+")
            return array('fromDate' => $fromDate, 'toDate' => $toDate);
        else
            return array('fromDate' => $toDate, 'toDate' => $fromDate);
    }

    
/**
    * @author Aju John
    * This function provides an interface to import employee leave summary
    * User needs to upload the file to import leaves
    **/
    public function importemployeeleavesAction () { 
        if(isset($_POST) && count($_POST) > 0) {
            if(isset($_FILES["emp_leave_excel"]["error"])){
                if($_FILES["emp_leave_excel"]["error"] > 0){
                    echo "Error: " . $_FILES["emp_leave_excel"]["error"] . "<br>";
                } else {

                    $allowed = array("csv" => "csv");
                    $filename = $_FILES["emp_leave_excel"]["name"];
                    $filesize = $_FILES["emp_leave_excel"]["size"];
                
                    // Verify file extension
                    $ext = pathinfo($filename, PATHINFO_EXTENSION);
                    
                    if(array_key_exists($ext, $allowed)){
                        $maxsize = 10 * 1024 * 1024;
                        if($filesize <= $maxsize) {
                            $filename = "employee_leave.csv";
                            copy($_FILES["emp_leave_excel"]["tmp_name"], EMP_DOC_UPLOAD_PATH.$filename);
                            $leaves = $this->csvtoarray(EMP_DOC_UPLOAD_PATH.$filename, $ids);
                            $usersmodel = new Default_Model_Users;
                            $employees = $usersmodel->getUserModelsFromEmployeeIds($ids);
                            $employeesData = array();
                            foreach($employees as $emp) {
                                $employeesData[$emp['employeeId']] = $emp;
                            }
                            $this->importtodb_leave($leaves, $employeesData);
                            echo "Uploaded Succesfully..!!";
                        } else die("Error: File size is larger than the allowed limit. Given file".$filesize);
                     } else die("Error: Please select a valid file format.");
                }
            } else{
                echo "Error: Invalid parameters - please contact your server administrator.";
            }
        }
        $this->view->title = "Import Employee Leaves";
    }

    private function importtodb_leave ($leaves, $employees) { //echo "<pre>";print_r($leaves);;die();
        $db = Zend_Db_Table::getDefaultAdapter();
        foreach($leaves as $leave) {
            if(array_key_exists($leave[1], $employees)) {
                $leavetypeid_SL = 2;
                $leavetypeid_PL = 1;
                $leave_limit_SL = $leave[4];
                $leave_limit_PL = $leave[3];
                

                $qry = "insert into main_employeeleaves 
                                (user_id, emp_leave_limit, leavetypeid, used_leaves, alloted_year, createdby, modifiedby, createddate, modifieddate) 
                        values(
                            ".$employees[$leave[1]]['id'].",
                            ".$leave_limit_SL.",
                            '".$leavetypeid_SL."',
                            '0',
                            '2016',
                            1,
                            1,
                            now(),
                            now()
                        )
                    ";
                $db->query($qry);


                $qry = "insert into main_employeeleaves 
                                (user_id, emp_leave_limit, leavetypeid, used_leaves, alloted_year, createdby, modifiedby, createddate, modifieddate) 
                        values(
                            ".$employees[$leave[1]]['id'].",
                            ".$leave_limit_PL.",
                            '".$leavetypeid_PL."',
                            '0',
                            '2016',
                            1,
                            1,
                            now(),
                            now()
                        )
                    ";
                $db->query($qry);

            }
            

        }
    }


    /**
    * @author Aju John
    * This function provides an interface to import employee leave request and the summary
    * User needs to upload the file to import leaves
    **/
    public function importemployeeleaverequestAction () {
        if(isset($_POST) && count($_POST) > 0) {
            if(isset($_FILES["emp_leave_excel"]["error"])){
                if($_FILES["emp_leave_excel"]["error"] > 0){
                    echo "Error: " . $_FILES["emp_leave_excel"]["error"] . "<br>";
                } else {

                    $allowed = array("csv" => "csv");
                    $filename = $_FILES["emp_leave_excel"]["name"];
                    $filesize = $_FILES["emp_leave_excel"]["size"];
                
                    // Verify file extension
                    $ext = pathinfo($filename, PATHINFO_EXTENSION);
                    
                    if(array_key_exists($ext, $allowed)){
                        $maxsize = 10 * 1024 * 1024;
                        if($filesize <= $maxsize) {
                            $filename = "employee_leave_request.csv";
                            copy($_FILES["emp_leave_excel"]["tmp_name"], EMP_DOC_UPLOAD_PATH.$filename);
                            $leaves = $this->csvtoarray(EMP_DOC_UPLOAD_PATH.$filename, $ids);
                            $usersmodel = new Default_Model_Users;
                            $employees = $usersmodel->getUserModelsFromEmployeeIds($ids);
                            $employeesData = array();
                            foreach($employees as $emp) {
                                $employeesData[$emp['employeeId']] = $emp;
              
                            }
                            $this->importtodb_request($leaves, $employeesData);
                            echo "Uploaded Succesfully..!!";

                        } else die("Error: File size is larger than the allowed limit. Given file".$filesize);
                    } else die("Error: Please select a valid file format.");
                }
            } else{
                echo "Error: Invalid parameters - please contact your server administrator.";
            }
        }
        $this->view->title = "Import Leave Summary"; // die("sdsj");
    }

    private function csvtoarray ($filename, &$ids) {
        $file = fopen($filename,"r");
        $index = 0; $leaves = array();
        $ids = array();
        while(! feof($file))
        {
            if($index++ == 0) {
                fgetcsv($file);
            }
            else {
                 $data = fgetcsv($file);
                 if($data > 0){
                    $leaves[]   = $data;
                    $ids[]      = $data[1];
                }
            }
        }
        fclose($file);
        return $leaves;
    }

    private function importtodb_request ($leaves, $employees) {
        $db = Zend_Db_Table::getDefaultAdapter();

        foreach($leaves as $leave) {
            if(array_key_exists($leave[1], $employees)) {
                $leavetype = ($leave[7] == "Sick Leave"?2:1);
                $appliedleavescount = ($leave[7] == "Sick Leave"?$leave[6]:$leave[5]);
                $no_of_days = ($leave[7] == "Sick Leave"?$leave[6]+$leave[9]:$leave[5]+$leave[8]);
                $leavetype_name = ($leave[7] == "Sick Leave"?"Sick Leave" : "Privilege Leave");
                $usersmodel = new Default_Model_Users; 
                //echo $employees[$leave[1]]['id'];
                $managermodel = $usersmodel->getUserModelFromEmployeeName($leave[0]);
                $manager_id= (isset($managermodel[0])?$managermodel[0]['id']:1);
                if($appliedleavescount != (int)$appliedleavescount) {
                    $decimal_leave = $appliedleavescount - (int)$appliedleavescount;
                    $no_of_days = ($leave[7] == "Sick Leave"?$decimal_leave+$leave[9]:$decimal_leave+$leave[8]);
                    $leaveday = 2;
                    $qry = "insert into main_leaverequest (user_id, reason, approver_comments, leavetypeid, leaveday, from_date, to_date,
                        leavestatus, rep_mang_id, no_of_days, appliedleavescount, createdby, modifiedby, createddate, modifieddate) 
                        values(
                            ".$employees[$leave[1]]['id'].",
                            '".$leave[7]."',
                            '',
                            ".$leavetype.",
                            2,
                            '".date("Y-m-d", strtotime($leave[3]))."',
                            '".date("Y-m-d", strtotime($leave[4]))."',
                            'Approved',
                            ".$manager_id.",
                            ".$no_of_days.",
                            ".$decimal_leave.",
                            1,
                            1,
                            now(),
                            now()
                        )
                    ";
                    $db->query($qry);
                    $appliedleavescount = $appliedleavescount - $decimal_leave;

                }
                if($appliedleavescount > 0) {
                    $leaveday = 1;
                    $no_of_days = ($leave[7] == "Sick Leave"?$appliedleavescount+$leave[9]:$appliedleavescount+$leave[8]);
                    $qry = "insert into main_leaverequest (user_id, reason, approver_comments, leavetypeid, leaveday, from_date, to_date,
                        leavestatus, rep_mang_id, no_of_days, appliedleavescount, createdby, modifiedby, createddate, modifieddate) 
                        values(
                            ".$employees[$leave[1]]['id'].",
                            '".$leave[7]."',
                            '',
                            ".$leavetype.",
                            1,
                            '".date("Y-m-d", strtotime($leave[3]))."',
                            '".date("Y-m-d", strtotime($leave[4]))."',
                            'Approved',
                            ".$manager_id.",
                            ".$no_of_days.",
                            ".$appliedleavescount.",
                            1,
                            1,
                            now(),
                            now()
                        )
                    ";
                    $db->query($qry);
                }
            }
        }
        //insert into main_employeeleaves (user_id, emp_leave_limit, leavetypeid, used_leaves, alloted_year, createdby, modifiedby, createddate, modifieddate)
    }

    public function autoapproveleaveAction()
    {


        echo "Autoapproveleave";

    }

}

