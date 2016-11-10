<?php

class Default_Form_shortlistedcandidates extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('action',BASE_URL.'shortlistedcandidates/edit');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'businessunits');
		
		$id = new Zend_Form_Element_Hidden('id');
		
		$selectionstatus = new Zend_Form_Element_Select('selectionstatus');
		$selectionstatus->setRequired(true)->addErrorMessage('Please change the candidate status.');
		$selectionstatus->addFilter('Int')->addValidator('NotEmpty',true, array('integer','zero'));
		$selectionstatus->setLabel('domain')
		->setMultiOptions(array(		
							'0'	=>	'Select status',
							'2'	=>	'Selected' ,
							'3'	=>	'Rejected'
							));
		
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Update');
		
		$this->addElements(array($id,$selectionstatus,$submit));
        $this->setElementDecorators(array('ViewHelper')); 
	}
}