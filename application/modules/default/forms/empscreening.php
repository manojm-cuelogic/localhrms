<?php

class Default_Form_empscreening extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('action',BASE_URL.'empscreening/add');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'empscreening');
	
		$id = new Zend_Form_Element_Hidden('id');
		
		
		$employee = new Zend_Form_Element_Select('employee');
        $employee->setLabel('employee');	
		$employee->setRequired(true)->addErrorMessage('Please select employee.');
		$employee->setAttrib('onchange', 'getemployeeData(this)');
		$employee->setRegisterInArrayValidator(false);			
		
		$bgcheck_status = new Zend_Form_Element_Select('bgcheck_status');
        $bgcheck_status->setLabel('employee');	
		$bgcheck_status->setRequired(true)->addErrorMessage('Please select bgcheck status.');		
		$bgcheck_status->addMultiOption('0','Select status');
		
		$bgcheck_status->addMultiOption('In process','In process');
		$bgcheck_status->addMultiOption('On hold','On hold');
		$bgcheck_status->addMultiOption('Complete','Complete');
	    	
		$bgcheck_status->setRegisterInArrayValidator(false);	
		
		$checktype = new Zend_Form_Element_MultiCheckbox('checktype');
		$checktype->setAttrib('onclick', 'displayAgencyList()');
		$bgcheckModal = new Default_Model_Bgscreeningtype();
	    $checktypesData = $bgcheckModal->fetchAll('isactive=1','type');
		foreach ($checktypesData->toArray() as $data){
				$checktype->addMultiOption($data['id'],$data['type']);
	    	}
		$checktype->setSeparator(PHP_EOL);		
		
		$checkagency = new Zend_Form_Element_Hidden('checkagency');
		
		$agency = new Zend_Form_Element_Hidden('agencyids');
		$agencyData = Zend_Controller_Front::getInstance()->getRequest()->getParam('checkagency',null);
		if($agencyData == 'checked')
		{
			$agency->setRequired(true);
			$agency->addValidator('NotEmpty', false, array('messages' => 'Please select agency.'));
		}
		
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');        
		$submit->setLabel('Save');
		
		$this->addElements(array($id,$employee,$bgcheck_status,$checkagency,$checktype,$agency,$submit));
        $this->setElementDecorators(array('ViewHelper')); 		
	}
	
}