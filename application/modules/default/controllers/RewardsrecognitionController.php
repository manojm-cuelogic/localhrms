<?php

class Default_RewardsrecognitionController extends Zend_Controller_Action
{
    
    private $options;
    public function preDispatch()
    {
    }
    
    
    public function init()
    {
        $this->_options= $this->getInvokeArg('bootstrap')->getOptions();
        
    }
    public function indexAction() {
        
    }
}