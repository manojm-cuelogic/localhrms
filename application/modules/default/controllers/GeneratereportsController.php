<?php

class Default_GeneratereportsController extends Zend_Controller_Action
{

	private $options;
	private $userlog_model;
	public function preDispatch()
	{
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('empauto', 'json')->initContext();
		
	}

	/**
	 * Init
	 *
	 * @see Zend_Controller_Action::init()
	 */
	public function init()
	{
            $this->_options= $this->getInvokeArg('bootstrap')->getOptions();
            $this->userlog_model = new Default_Model_Userloginlog();
	}
   
	public function agencyAction()
	{
		
	}
}