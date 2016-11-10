<?php

class Default_FeedforwardstatusController extends Zend_Controller_Action
{
    private $options;
    public function preDispatch()
    {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('getffstatusemps', 'html')->initContext();
        $ajaxContext->addActionContext('getfeedforwardstatus', 'html')->initContext();
    }
	
    public function init()
    {
        $this->_options= $this->getInvokeArg('bootstrap')->getOptions();
    }
    
	public function indexAction()
    {
    	$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;
            $businessunit_id = $auth->getStorage()->read()->businessunit_id;
            $department_id = $auth->getStorage()->read()->department_id; 
            $loginuserRole = $auth->getStorage()->read()->emprole;
            $loginuserGroup = $auth->getStorage()->read()->group_id;
        }	

        $ffinitModel = new Default_Model_Feedforwardinit();
        $ffDataArr = $ffinitModel->getFFbyBUDept('','yes');
        $this->view->ffdataarr = $ffDataArr;
    }
    
    public function getffstatusempsAction()
    {
    	$id = $this->_request->getParam('id');
    	
    	$ffEmpRatModel = new Default_Model_Feedforwardemployeeratings;
    	$ffEmpsStatusData = $ffEmpRatModel->getEmpsFFStatus($id);
    	
    	$this->view->ffEmpsStatusData = $ffEmpsStatusData;
    }
    
	public function getfeedforwardstatusAction()
    {
    	$id = $this->_request->getParam('id');
    	$ffstatus = $this->_request->getParam('ffstatus');
    	
    	$ffEmpRatModel = new Default_Model_Feedforwardemployeeratings;
    	$ffEmpsStatusData = $ffEmpRatModel->getfeedforwardstatus($id,$ffstatus);
    	
    	$this->view->ffEmpsStatusData = $ffEmpsStatusData;
    }
}