<?php

class Default_ConfiguresiteController extends Zend_Controller_Action
{

	private $options;
	
	/**
	 * Init
	 * 
	 * @see Zend_Controller_Action::init()
	 */
    public function init()
    {
        $this->_options= $this->getInvokeArg('bootstrap')->getOptions();
    }

	public function indexAction()
	{		
		$this->view->msg='this is index';
	}
	
	public function managemenuAction(){
		$this->view->msg='this is manage menu';
	}
	public function sitepreferencesAction()
	{
	}
}