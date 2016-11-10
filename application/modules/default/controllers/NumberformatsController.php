<?php

class Default_NumberformatsController extends Zend_Controller_Action
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
		$numberformatsmodel = new Default_Model_Numberformats();	
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
			/** search from grid - START **/
			$searchData = $this->_getParam('searchData');	
			$searchData = rtrim($searchData,',');
			/** search from grid - END **/
		}
		
        $dataTmp = $numberformatsmodel->getGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall);		 		
					
		array_push($data,$dataTmp);
		$this->view->dataArray = $data;
		$this->view->call = $call ;
		$this->view->messages = $this->_helper->flashMessenger->getMessages();
    }
	
	 public function addAction()
	{
	   $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
		}
		
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
			$this->_helper->layout->disableLayout();
			
		$numberformatform = new Default_Form_numberformats();
     	$numberformatform->setAttrib('action',BASE_URL.'numberformats/add');
        $this->view->form = $numberformatform; 	
        if($this->getRequest()->getPost()){
		     $result = $this->save($numberformatform);	
		     $this->view->msgarray = $result; 
        }  		
		
	}

    public function viewAction()
	{	
		$id = $this->getRequest()->getParam('id');
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
			$this->_helper->layout->disableLayout();
		$objName = 'numberformats';
		$numberformatform = new Default_Form_numberformats();
		$numberformatsmodel = new Default_Model_Numberformats();
		$numberformatform->removeElement("submit");
		$elements = $numberformatform->getElements();
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
			if(is_numeric($id) && $id>0)
			{
				$data = $numberformatsmodel->getNumberFormatDataByID($id);
				if(!empty($data))
				{
					$numberformatform->populate($data[0]);
					$this->view->controllername = $objName;
					$this->view->id = $id;
					$this->view->form = $numberformatform;
					$this->view->id = $id;
					$this->view->ermsg = '';
				}
				else
				{
					$this->view->ermsg = 'norecord';
				}
			}
			else
			{
				$this->view->ermsg = 'norecord';
			}
			
		}
		catch(Exception $e)
		{
			$this->view->ermsg = 'nodata';
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
		
		$numberformatform = new Default_Form_numberformats();
		$numberformatsmodel = new Default_Model_Numberformats();
		try
		{
		    if($id)
			{
				if(is_numeric($id) && $id>0)
				{
					$data = $numberformatsmodel->getNumberFormatDataByID($id);
					if(!empty($data))
					{
						$numberformatform->populate($data[0]);
						$numberformatform->submit->setLabel('Update');
						$this->view->id = $id;
						$this->view->ermsg = '';
						$numberformatform->setAttrib('action',BASE_URL.'numberformats/edit/id/'.$id);
					}
					else
					{
						$this->view->ermsg = 'norecord';
					}
					
				}
				else
				{
					$this->view->ermsg = 'norecord';
				}
			}	
			else
            {
			   $this->view->ermsg = '';
            }
		}
		catch(Exception $e)
		{
			 $this->view->ermsg = 'nodata';
		}
		$this->view->form = $numberformatform;
		 if($this->getRequest()->getPost()){
		     $result = $this->save($numberformatform);	
		     $this->view->msgarray = $result; 
        } 
	}
	
	public function save($numberformatform)
	{
	  $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
		} 
     		if($numberformatform->isValid($this->_request->getPost())){
			    $numberformatsmodel = new Default_Model_Numberformats();
			    $id = $this->_request->getParam('id'); 
			    $numberformattype = $this->_request->getParam('numberformattype');
				$description = $this->_request->getParam('description');
				$date = new Zend_Date();
				$actionflag = '';
				$tableid  = ''; 
				   $data = array( 'numberformattype'=>trim($numberformattype),
				               	  'description'=>trim($description),
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
						$data['createddate'] = $date->get('yyyy-MM-dd HH:mm:ss');
						$data['createddate'] = gmdate("Y-m-d H:i:s");
						$data['isactive'] = 1;
						$where = '';
						$actionflag = 1;
					}
					$Id = $numberformatsmodel->SaveorUpdateNumberFormatData($data, $where);
					if($Id == 'update')
					{
					   $tableid = $id;
					   $this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Number format updated successfully."));
					}   
					else
					{
                       $tableid = $Id; 	
                        $this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Number format added successfully."));					   
					}   
					$menuID = NUMBERFORMATS;
					$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$tableid);
    			    $this->_redirect('numberformats');		
			}else
			{
     			$messages = $numberformatform->getMessages();
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
		 $messages['msgtype'] = '';
		 $actionflag = 3;
		    if($id)
			{
			$numberformatsmodel = new Default_Model_Numberformats();
			  $numberformatdata = $numberformatsmodel->getNumberFormatDataByID($id);
			  $data = array('isactive'=>0,'modifieddate'=>gmdate("Y-m-d H:i:s"));
			  $where = array('id=?'=>$id);
			  $Id = $numberformatsmodel->SaveorUpdateNumberFormatData($data, $where);
			    if($Id == 'update')
				{
				   $menuID = NUMBERFORMATS;
				   $result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id);
                   $configmail = sapp_Global::send_configuration_mail('Number Format',$numberformatdata[0]['numberformattype']);								   
				   $messages['message'] = 'Number format deleted successfully.';
				   $messages['msgtype'] = 'success';
				}   
				else
                  { $messages['message'] = 'Number format cannot be deleted.';
					$messages['msgtype'] = 'success';				   }
			}
			else
			{ 
			 $messages['message'] = 'Number format cannot be deleted.';
			 $messages['msgtype'] = 'success';
			}
			$this->_helper->json($messages);
		
	}
	
	

}
