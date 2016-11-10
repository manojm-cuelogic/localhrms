<?php

class Default_StructureController extends Zend_Controller_Action
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
		$structureModel = new Default_Model_Structure();
		$orgData = $structureModel->getOrgData();
		$unitData = $structureModel->getUnitData();
		$deptData = $structureModel->getDeptData();
		$nobu = 'no';
		foreach($deptData as $rec)
		{			
			if($rec['unitid'] == '0')
			$nobu = 'exists';
			
		}
		$this->view->orgData = $orgData;
		$this->view->unitData = $unitData;
		$this->view->deptData = $deptData;
		$this->view->nobu = $nobu;
		$this->view->msg = 'This is organization structure';
	}
}
?>