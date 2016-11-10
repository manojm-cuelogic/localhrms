<?php

class Default_Model_Rejectedrequisitions extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_requisition';
    protected $_primary = 'id';
        	
    public function getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$statusid,$a1,$a2,$a3)
    {
        $searchQuery = '';
        $searchArray = array();
        $data = array();
	$requi_model = new Default_Model_Requisition();	
        $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;			
            $loginuserGroup = $auth->getStorage()->read()->group_id;
        } 
        if($searchData != '' && $searchData!='undefined')
        {
            $searchValues = json_decode($searchData);
            if(count($searchValues) >0)
            {
                foreach($searchValues as $key => $val)
                {
                    if($key == 'onboard_date' || $key == 'r.createdon')
                    {
                        $searchQuery .= " date(".$key.")  = '".  sapp_Global::change_date($val,'database')."' AND ";
                    }
                    else
                        $searchQuery .= " ".$key." like '%".$val."%' AND ";
                    $searchArray[$key] = $val;
                }
                $searchQuery = rtrim($searchQuery," AND");					
            }
        }
        $objName = 'rejectedrequisitions';

        $tableFields = array('action'=>'Action',
                             'requisition_code' => 'Requisition Code',                             
                            'jobtitle_name' => 'Job Title',  
                             'createdby_name'	=> 'Raised By',
                            'reporting_manager_name' => 'Reporting Manager',
                             'req_no_positions' => 'No. of Positions',
                            'filled_positions' => 'Filled Positions',			 
                            'r.createdon'=> 'Raised On',
                           'onboard_date' => 'Due Date',
							
                            );

        $tablecontent = $requi_model->getRequisitionData($sort, $by, $pageNo, $perPage,$searchQuery,$loginUserId,$loginuserGroup,3);     

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
                'add' =>'add',
                'searchArray' => $searchArray,
                'menuName'=>'Rejected Requisitions',
                'call'=>$call,
				'dashboardcall'=>$dashboardcall,
                'search_filters' => array(
                    'r.createdon' =>array('type'=>'datepicker'),
                    'onboard_date'=>array('type'=>'datepicker'),                    
                ),
        );
        return $dataTmp;
    }            
}//end of class