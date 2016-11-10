<?php

class Default_Form_processes extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('action',BASE_URL.'processes/add');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'empscreening');
	
		$id = new Zend_Form_Element_Hidden('id');
		
		$process_status = new Zend_Form_Element_Select('process_status');
        $process_status->setLabel('Process Status');	
		$process_status->addMultiOption('In process','In process');   	
		$process_status->addMultiOption('On hold','On hold');   	
		$process_status->addMultiOption('Complete','Complete');   	
		
		
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');
		
		$this->addElements(array($id,$process_status,$submit));
        $this->setElementDecorators(array('ViewHelper')); 		
	}
}
