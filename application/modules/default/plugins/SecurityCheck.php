<?php 

class Default_Plugin_SecurityCheck extends Zend_Controller_Plugin_Abstract
{
	
	
	const MODULE='default';
	private $_controller;
	private $_module;
	private $_action;
	private $_role;
	
    /**
     * preDispatch
     * 
     * @param Zend_Controller_Request_Abstract $request
     */
    public function preDispatch (Zend_Controller_Request_Abstract $request)
    {
    
    	$storage = new Zend_Auth_Storage_Session();
       	$data = $storage->read();
       		
        $this->_controller = $this->getRequest()->getControllerName();
        
       	$this->_module= $this->getRequest()->getModuleName();
        
       	$this->_action= $this->getRequest()->getActionName();  
		
        $withoutloginActionArr = array('index','login','loginsave','loginpopupsave','forgotpassword','editforgotpassword','sendpassword','popup','getemployeedetails', 'getemployeeleaves', 'getholidays','creditmonthlyemployeeleaves','leavereminders');
		
        if($this->_module == self::MODULE && $data['employeeId']){

       	   	
			if(($this->_controller == 'index' || $this->_controller == 'csi' || $this->_controller == 'cronjob') && $this->_module == 'default' && in_array($this->_action,$withoutloginActionArr))
			{
	        		
       	   		$front = Zend_Controller_Front::getInstance();
					
       	   		//$this->_response->setRedirect($front->getBaseUrl().'/welcome');
				$this->_response->setRedirect(BASE_URL.'welcome');
       	   		
			}

        }
       
       
	    
        $auth= Zend_Auth::getInstance();
		
        $redirect = '';
       
        $withoutloginArr = array('default_cronjob_logcron','default_cronjob_inactiveusers','default_cronjob_requisition','default_cronjob_leaveapprove','default_cronjob_empexpiry','default_cronjob_empdocsexpiry','default_cronjob_index','default_index_index','default_index_loginpopupsave','default_csi_getemployeedetails', 'default_csi_getemployeeleaves', 'default_csi_getholidays','default_index_login','default_index_loginsave','default_index_browserfailure','default_index_forgotpassword','default_index_editforgotpassword','default_index_sendpassword','default_index_popup','services_index_index','services_index_post','services_index_get','services_index_login','timemanagement_cronjob_index','timemanagement_cronjob_mailreminder','default_cronjob_leavereminders','default_cronjob_creditmonthlyemployeeleaves','timemanagement_cronjob_monthlyempblockremainder','timemanagement_cronjob_monthlyblockedemp');
        $contolleractionstring = $this->_module.'_'.$this->_controller.'_'.$this->_action; 
      
        if(!in_array($contolleractionstring,$withoutloginArr))
	        {
	        
		        if ($this->_isAuth($auth))
		        {
	      	        $user= $auth->getStorage()->read();
				    
	        		$bootstrap = Zend_Controller_Front::getInstance()->getParam('bootstrap');
				    
	        		$db= $bootstrap->getResource('db');
				    
	        		 $redirect= "session";
				}
				else 
				{
					$redirect= "nosession";
				}
	        	
	        }
	        
	    
        
       
      
        if ($redirect == 'nosession') {
			
        	if($this->getRequest()->isXmlHttpRequest()) {
        		
        		$auth = Zend_Auth::getInstance();
				Zend_Session::namespaceUnset('recentlyViewed');
				Zend_Session::namespaceUnset('prevUrl');
        		$auth->clearIdentity();
        		
        		$content = array(
					'login' => 'failed'
				);
				
			$jsonData = Zend_Json::encode($content);
				$this->getResponse()
					 ->setHeader('Content-Type', 'text/json')
					 ->setBody($jsonData)
					 ->sendResponse();
				
        		exit;
        	
        	}else{
        		
				/*** Previous URL redirection after login - start ***/
				$prevUrl = new Zend_Session_Namespace('prevUrl');        
				$prevUrl->prevUrlObject = array(); 
				array_push($prevUrl->prevUrlObject,$_SERVER['REQUEST_URI']);
				/*** Previous URL redirection after login - end ***/
				Zend_Session::namespaceUnset('recentlyViewed');
        		$auth = Zend_Auth::getInstance();
        		$auth->clearIdentity();
        		
        		$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
				$redirector->gotoUrl('/')
						   ->redirectAndExit();
        		
            	
            	
        	}

        }
       
    }
    /**
     * Check user identity using Zend_Auth
     * 
     * @param Zend_Auth $auth
     * @return boolean
     */
    private function _isAuth (Zend_Auth $auth)
    {
    	if (!empty($auth) && ($auth instanceof Zend_Auth)) {
        	return $auth->hasIdentity();
    	} 
    	return false;	
    }
    /**
     * Check permission using Zend_Auth and Zend_Acl
     * 
     * @param Zend_Auth $auth
     * @param Zend_Acl $acl
     * @return boolean
     */
    private function _isAllowed(Zend_Auth $auth, Zend_Acl $acl) 
    {
    	if (empty($auth) || empty($acl) ||
    		!($auth instanceof Zend_Auth) ||
    		 !($acl instanceof Zend_Acl)) {
    			return false;
    	}
    	$resources= array (
    		'*/*/*',
    		$this->_module.'/*/*', 
    		$this->_module.'/'.$this->_controller.'/*', 
    		$this->_module.'/'.$this->_controller.'/'.$this->_action
    	);
    	$result=false;
    	foreach ($resources as $res) {
    		if ($acl->has($res)) { 
    			$result= $acl->isAllowed($this->_role,$res);
    		}
    	}    
    	$storage = new Zend_Auth_Storage_Session();
        $data = $storage->read();
    	return true;
    	return $result;
    }
}
