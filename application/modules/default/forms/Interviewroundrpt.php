<?php

/**
 * This gives employee report form.
 */
class Default_Form_Interviewroundrpt extends Zend_Form
{
    public function init()
    {
        $this->setMethod('post');
        $this->setAttrib('id', 'formid');
        $this->setAttrib('name', 'frm_interview_rpt');

               
        $interview_date = new Zend_Form_Element_Text("interview_date");        
        $interview_date->setLabel("Interview Date");
        $interview_date->setAttrib('readonly', 'readonly');
		
        $req_id = new Zend_Form_Element_Select("req_id");      
        $req_id->setRegisterInArrayValidator(false); 
        $req_id->setLabel("Requisition Code")
                            ->addMultiOptions(array('' => 'Select Requisition Code'));
        
        
        $department_id = new Zend_Form_Element_Select("department_id");
        $department_id->setLabel("Department");
        $department_id->setRegisterInArrayValidator(false); 
        $department_id->addMultiOptions(array('' => 'Select Department'));
        
        $interviewer_id = new Zend_Form_Element_Text("interviewer_id");        
        $interviewer_id->setLabel("Interviewer");
        $interviewer_id->setAttrib('name', '');
        $interviewer_id->setAttrib('id', 'idinterviewer_id');
        
        $createdby = new Zend_Form_Element_Text("createdby");        
        $createdby->setLabel("Interview Planned By");
        $createdby->setAttrib('name', '');
        $createdby->setAttrib('id', 'idcreatedby');
        
        $submit = new Zend_Form_Element_Button('submit');        
        $submit->setAttrib('id', 'idsubmitbutton');
        $submit->setLabel('Report'); 
        
        $this->addElements(array($submit,$interview_date,$req_id,$department_id,$interviewer_id,$createdby));
        $this->setElementDecorators(array('ViewHelper')); 
    }
}