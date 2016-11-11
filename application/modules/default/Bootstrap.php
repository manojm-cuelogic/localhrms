<?php

class Default_Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

	protected function _initAppAutoload() {
		
		$autoloader = Zend_Loader_Autoloader::getInstance ();
		$autoloader->registerNamespace ( 'Ingot_' );
		$autoloader->registerNamespace ( 'ZendX_' );		
		$autoloader->registerNamespace ( 'Jqgrid_' );
		$autoloader->registerNamespace ( 'sapp_' );
		
		$this->options = $this->getOptions();                			
    
		return $autoloader;
	}

    protected function _initView()
    {
    	
		$theme = 'default';
		$templatePath  = APPLICATION_PATH . '/../public/themes/' . $theme . '/templates';
		Zend_Registry::set('user_date_format', 'm-d-Y');
		Zend_Registry::set('calendar_date_format', 'mm-dd-yy');
		Zend_Registry::set('db_date_format', 'Y-m-d');
		Zend_Registry::set('perpage', 10);
		Zend_Registry::set('menu', 'home');
		Zend_Registry::set('eventid', '');                
                
                $dir_name = $_SERVER['DOCUMENT_ROOT'].rtrim(str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']),'/');
                Zend_Registry::set('acess_file_path',$dir_name.SEPARATOR."application".SEPARATOR."modules".SEPARATOR."default".SEPARATOR."plugins".SEPARATOR."AccessControl.php");
                Zend_Registry::set('siteconstant_file_path',$dir_name.SEPARATOR."public".SEPARATOR."site_constants.php");
                Zend_Registry::set('emailconstant_file_path',$dir_name.SEPARATOR."public".SEPARATOR."email_constants.php");
                Zend_Registry::set('emptab_file_path',$dir_name.SEPARATOR."public".SEPARATOR."emptabconfigure.php");
                Zend_Registry::set('emailconfig_file_path',$dir_name.SEPARATOR."public".SEPARATOR."mail_settings_constants.php");
                Zend_Registry::set('application_file_path',$dir_name.SEPARATOR."public".SEPARATOR."application_constants.php");
                
		$date=new Zend_Date();
		Zend_Registry::set('currentdate', ($date->get('yyyy-MM-dd HH:mm:ss')));
		
		Zend_Registry::set('currenttime', ($date->get('HH:mm:ss')));								
		
		Zend_Registry::set('logo_url','/public/images/landing_header.jpg');
		$view = new Zend_View ();
		$view->setEscape('stripslashes');
		$view->setBasePath ( $templatePath );		
		$view->setScriptPath ( APPLICATION_PATH );
		$view->addHelperPath ( 'ZendX/JQuery/View/Helper', 'ZendX_JQuery_View_Helper' );				
		
		$viewRenderer = new Zend_Controller_Action_Helper_ViewRenderer ();
		$viewRenderer->setView ( $view );

		Zend_Controller_Action_HelperBroker::addHelper ( $viewRenderer );
        return $this;
    }
    
	public function _initViewHelpers()
	{
		$this->bootstrap('layout');
		$layout = $this->getResource('layout');
		$view = $layout->getView();
		$view->doctype('HTML5');
		
		
	}

	
	protected function _initDbProfiler()
	{
	  
	        $this->bootstrap('db');
	        $profiler = new Zend_Db_Profiler_Firebug('All DB Queries');
	        $profiler->setEnabled(true);
	        $this->getPluginResource('db')->getDbAdapter()->setProfiler($profiler);
	
	} 
	public function _initFilter()
	{		
	}
	
	public function _initRoutes()
       {
       
    	$router = Zend_Controller_Front::getInstance()->getRouter();

	
		$route = new Zend_Controller_Router_Route('login', array(
		'module' => 'default',
		'controller' => 'index',
		'action' => 'login'
		));
		
		$route = new Zend_Controller_Router_Route('cronjob', array(
		'module' => 'default',
		'controller' => 'cronjob',
		'action' => 'index'
		));

		$welcomeroute = new Zend_Controller_Router_Route('welcome', array(
		'module' => 'default',
		'controller' => 'index',
		'action' => 'welcome'
		));
		
		$viewprofileroute = new Zend_Controller_Router_Route('viewprofile', array(
		'module' => 'default',
		'controller' => 'dashboard',
		'action' => 'viewprofile'
		));
		
		$viewsettingsroute = new Zend_Controller_Router_Route('viewsettings/:tab', array(
			 'module' => 'default',
			 'controller' => 'dashboard',
			 'action' => 'viewsettings',
			));
			
		$changepasswordroute = new Zend_Controller_Router_Route('changepassword', array(
		'module' => 'default',
		'controller' => 'dashboard',
		'action' => 'changepassword'
		));	
		
        $empleavesummaryroute = new Zend_Controller_Router_Route('empleavesummary/:status', array(
			 'module' => 'default',
			 'controller' => 'empleavesummary',
			 'action' => 'index',
			));  

		$approvedrequisitionroute = new Zend_Controller_Router_Route('approvedrequisitions/:status', array(
			 'module' => 'default',
			 'controller' => 'approvedrequisitions',
			 'action' => 'index',
			));  
		
		$shortlistedroute = new Zend_Controller_Router_Route('shortlistedcandidates/:status', array(
			 'module' => 'default',
			 'controller' => 'shortlistedcandidates',
			 'action' => 'index',
			));  
	
		$empscreeningroute = new Zend_Controller_Router_Route('empscreening/con/:status', array(
			 'module' => 'default',
			 'controller' => 'empscreening',
			 'action' => 'index',
			));
                
         $error_route = new Zend_Controller_Router_Route('error', array(
			 'module' => 'default',
			 'controller' => 'error',
			 'action' => 'error',
			));
	
		/** route for policy documents **/
		$polidydocs_route = new Zend_Controller_Router_Route('policydocuments/id/:id/*',array(
				'module' => 'default',
				'controller' => 'policydocuments',
				'action' => 'index',
			));

		/** route for adding multiple policy documents **/
		$multiplepolidydocs_route = new Zend_Controller_Router_Route('policydocuments/addmultiple/:id',array(
				'module' => 'default',
				'controller' => 'policydocuments',
				'action' => 'addmultiple',
			));
		/** route for adding multiple policy documents **/
		$csi_employees_route = new Zend_Controller_Router_Route('csi/employees',array(
				'module' => 'default',
				'controller' => 'csi',
				'action' => 'getemployeedetails',
			));
		$csi_employee_leaves_route = new Zend_Controller_Router_Route('csi/employee/leaves',array(
				'module' => 'default',
				'controller' => 'csi',
				'action' => 'getemployeeleaves',
			));
		$csi_holiday_route = new Zend_Controller_Router_Route('csi/holidays',array(
				'module' => 'default',
				'controller' => 'csi',
				'action' => 'getholidays',
			));
		$credit_monthly_leave_route = new Zend_Controller_Router_Route('cronjob/creditmonthlyemployeeleaves/:key',array(
				'module' => 'default',
				'controller' => 'cronjob',
				'action' => 'creditmonthlyemployeeleaves',
			));
		$leave_reminder_route = new Zend_Controller_Router_Route('cronjob/leavereminders/:key',array(
				'module' => 'default',
				'controller' => 'cronjob',
				'action' => 'leavereminders',
			));
                /*
                 * Auto Approve Cron Set
                 */
                $cronautoapproveleave_route = new Zend_Controller_Router_Route('cronjob/cronautoapproveleave/:key',array(
				'module' => 'default',
				'controller' => 'cronjob',
				'action' => 'cronautoapproveleave',
			));
		$router->addRoute('login', $route); 
		$router->addRoute('welcome', $welcomeroute);		
		$router->addRoute('viewsettings', $viewsettingsroute);
		$router->addRoute('empleavesummary', $empleavesummaryroute);
		$router->addRoute('approvedrequisitions', $approvedrequisitionroute);
		$router->addRoute('shortlistedcandidates', $shortlistedroute);
		$router->addRoute('empscreening', $empscreeningroute);                		
		$router->addRoute('policydocuments',$polidydocs_route);
		$router->addRoute('csi_employees_route',$csi_employees_route);
		$router->addRoute('csi_employee_leaves_route',$csi_employee_leaves_route);
		$router->addRoute('csi_holiday_route',$csi_holiday_route);
		$router->addRoute('multiplepolicydocuments',$multiplepolidydocs_route);
		$router->addRoute('credit_monthly_leave_route',$credit_monthly_leave_route);
		$router->addRoute('leave_reminder_route',$leave_reminder_route);
                $router->addRoute('cronautoapproveleave_route',$cronautoapproveleave_route);
    }  
}

