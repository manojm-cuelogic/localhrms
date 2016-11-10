<?php

class Default_RejectedrequisitionsController extends Zend_Controller_Action
{

    private $_options;
    public function preDispatch()
    {	
        $session = sapp_Global::_readSession();
        if(!isset($session))
        {
            if($this->getRequest()->isXmlHttpRequest())
            {
                echo Zend_Json::encode( array('login' => 'failed') );
                die();	
            }
            else
            {
                $this->_redirect('');
            }
        }       
    }
	
    public function init()
    {
        $this->_options= $this->getInvokeArg('bootstrap')->getOptions();		
    }
	
    public function indexAction()
    {
        $reject_model = new Default_Model_Rejectedrequisitions();
        $call = $this->_getParam('call');
        if($call == 'ajaxcall')
            $this->_helper->layout->disableLayout();
		$dashboardcall = $this->_getParam('dashboardcall');
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;			
			$loginuserGroup = $auth->getStorage()->read()->group_id;
		} 
		
        $refresh = $this->_getParam('refresh');
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
            $sort = 'DESC';$by = 'r.modifiedon';$pageNo = 1;$searchData = '';$searchQuery = '';
            $searchArray = array();
        }
        else 
        {
            $sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
            $by = ($this->_getParam('by')!='')? $this->_getParam('by'):'r.modifiedon';
            if($dashboardcall == 'Yes')
				$perPage = $this->_getParam('per_page',DASHBOARD_PERPAGE);
			else $perPage = $this->_getParam('per_page',PERPAGE);
            $pageNo = $this->_getParam('page', 1);
            /** search from grid - START **/
            $searchData = $this->_getParam('searchData');	
           
            /** search from grid - END **/
        }

        $dataTmp = $reject_model->getGrid($sort, $by, $perPage, $pageNo, $searchData, $call, $dashboardcall, '', '', '', '');
        array_push($data,$dataTmp);
        $this->view->dataArray = $dataTmp;
        $this->view->call = $call ;
		$this->view->messages = $this->_helper->flashMessenger->getMessages();	
	}
    public function viewAction()
    {
        $id = $this->getRequest()->getParam('id');
        $requi_model = new Default_Model_Requisition();		                
        $data = array();
        try
        {
            if($id >0 && is_numeric($id))
            {			
                $data = $requi_model->getReqDataForView($id);    
                
                if(count($data) >0  && $data[0]['req_status'] == 'Rejected')
                {
                    $data = $data[0];
                    $auth = Zend_Auth::getInstance();
                    if($auth->hasIdentity())
                    {
                        $loginUserId = $auth->getStorage()->read()->id;
                        $loginuserRole = $auth->getStorage()->read()->emprole;
                        $loginuserGroup = $auth->getStorage()->read()->group_id;
                    }
                    												 
                    $data['jobtitlename'] = '';			
                    $data['businessunit_name'] = $data['businessunit_name'];									
                    $data['dept_name'] = $data['department_name'];									
                    $data['titlename'] = $data['jobtitle_name'];									
                    $data['posname'] = $data['position_name'];									
                    $data['empttype'] = $data['emp_type_name'];						                       
                    $data['mngrname'] = $data['reporting_manager_name'];						
                    $data['raisedby'] = $data['createdby_name'];			                        
                    $data['app1_name'] = $data['approver1_name'];
                        
                    if($data['approver2'] != '')
                    {                        
                        $data['app2_name'] = $data['approver2_name'];
                    }
                    else 
                    {
                        $data['app2_name'] = 'No Approver';
                    }
                        
                    if($data['approver3'] != '')
                    {                        
                        $data['app3_name'] = $data['approver3_name'];
                    }
                    else 
                    {
                        $data['app3_name'] = 'No Approver';
                    }                        
			
                    foreach($data as $key=>$val)
                    {
                        $data[$key] = htmlentities($val, ENT_QUOTES, "UTF-8");
                    }	            
                    $data['onboard_date'] = sapp_Global::change_date($data['onboard_date'], 'view');
			
                    //start of candidate details
                    $sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
                    $by = ($this->_getParam('by')!='')? $this->_getParam('by'):'c.createddate';
                    $perPage = $this->_getParam('per_page',10);
                    $pageNo = $this->_getParam('page', 1);            
                    $searchQuery = '';
                    $searchArray = array();
                    $tablecontent='';
                    /** search from grid - START **/
                    $searchData = $this->_getParam('searchData');	
                    if($searchData != '' && $searchData!='undefined')
                    {
                        $searchValues = json_decode($searchData);
                        if(count($searchValues) >0)
                        {
                            foreach($searchValues as $key => $val)
                            {
                                $searchQuery .= " ".$key." like '%".$val."%' AND ";
                                $searchArray[$key] = $val;
                            }
                            $searchQuery = rtrim($searchQuery," AND");					
                        }                    
                    }
                    /** search from grid - END **/


                    $objName = 'apprreqcandidates';

                    $tableFields = array('action'=>'Action',
                                         'candidate_name' => 'Candidate Name',
                                         'cand_status' => 'Candidate Status',
                                        );
                    $candidate_model = new Default_Model_Candidatedetails();
                    $tablecontent = $candidate_model->getCandidatesData_requisition($sort, $by, $pageNo, $perPage,$searchQuery,$id);     
                    $cand_status_opt = array('' => 'All','Shortlisted' => 'Shortlisted','Selected' => 'Selected','Rejected' => 'Rejected',
                                            'On hold' => 'On hold','Disqualified' => 'Disqualified','Scheduled' => 'Scheduled',
                                            'Not Scheduled' => 'Not Scheduled','Recruited' => 'Recruited','Requisition Closed/Completed' => 'Requisition Closed/Completed');
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
                            'formgrid' => 'true',
                            'searchArray' => $searchArray,
                            'menuName' => 'Candidate details',
                            'call'=>'',
                            'search_filters' => array(
                                'cand_status' => array(
                                    'type' => 'select',
                                    'filter_data' => $cand_status_opt,
                                ),
                            ),
                    );			
                    array_push($data,$dataTmp);
                    $this->view->dataArray = $dataTmp;
                    //end of candidate details
                    $this->view->data = $data;
                    $this->view->loginuserGroup = $loginuserGroup;
                }
                else 
                {
                    $this->view->nodata = 'nodata';		
                }			
            }
            else 
            {
                $this->view->nodata = 'nodata';		
            }
        }
        catch(Exception $e)
        {
            $this->view->nodata = 'nodata';		
        }
    }
}