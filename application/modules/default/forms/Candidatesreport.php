<?php

/**
 * This gives employee report form.
 */
class Default_Form_Candidatesreport extends Zend_Form{
    public function init(){
        $this->setMethod('post');
        $this->setAttrib('id', 'formid');
        $this->setAttrib('name', 'frm_requisition_report');

        $requisition_code = new Zend_Form_Element_Text("requisition_code");
        $requisition_code->setLabel("Requisition Code");
		$requisition_code->setAttrib('name', '');
        $requisition_code->setAttrib('id', 'idrequisition_code');
		
        $cand_status = new Zend_Form_Element_Select("cand_status");
        $cand_status->setRegisterInArrayValidator(false);
        $cand_status->addMultiOptions(
        	array(
        		'' => 'Select Candidate Status',
	        	'Shortlisted' => 'Shortlisted',
	        	'Selected' => 'Selected',
	        	'Rejected' => 'Rejected',
	        	'On hold' => 'On hold',
	        	'Disqualified' => 'Disqualified',
	        	'Scheduled' => 'Scheduled',
	        	'Not Scheduled' => 'Not Scheduled',
	        	'Recruited' => 'Recruited',
	        	'Requisition Closed/Completed' => 'Requisition Closed/Completed'
        	)
        );
        $cand_status->setLabel("Candidate Status");
        $cand_status->setAttrib('title', 'Candidate Status');
        $cand_status->setAttrib('id', 'idcand_status');
        
        $submit = new Zend_Form_Element_Button('submit');        
        $submit->setAttrib('id', 'idsubmitbutton');
        $submit->setLabel('Report'); 
        
        $this->addElements(array($requisition_code, $cand_status, $submit));
        $this->setElementDecorators(array('ViewHelper'));
    }
}