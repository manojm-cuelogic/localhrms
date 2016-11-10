<?php

/**
 * This gives service desk report form.
 */
class Default_Form_Servicedeskreport extends Zend_Form
{
    public function init()
    {
        $this->setMethod('post');
        $this->setAttrib('id', 'formid');
        $this->setAttrib('name', 'frm_servicedesk_report');
        
        $raised_by = new Zend_Form_Element_Text("raised_by");        
        $raised_by->setLabel("Raised by");
        $raised_by->setAttrib('name', '');
        $raised_by->setAttrib('id', 'idraised_by');
        
        $service_desk_type = new Zend_Form_Element_Select('service_desk_id');        
        $service_desk_type->setLabel("Category");		
        $service_desk_type->addMultiOptions(array('' => 'Select category'));
		
       	$service_request_id = new Zend_Form_Element_Select('service_request_id');        
        $service_request_id->setLabel("Request Type");		
        $service_request_id->addMultiOptions(array('' => 'Select request'));
        
        $priority = new Zend_Form_Element_Select('priority');        
        $priority->setLabel("Priority");		
        $priority->addMultiOptions(array('' => 'Select priority','1' => 'Low','2' => 'Medium','3' => 'High'));
        
        $status = new Zend_Form_Element_Select('status');        
        $status->setLabel("Status");		
        $status->addMultiOptions(array('' => 'Select status','Open' => 'Open','Cancelled' => 'Cancelled',
                                       
                                        'To management approve' => 'To management approve', 
                                        'To manager approve' => 'To manager approve',
                                        'Manager approved' => 'Manager approved','Management approved' => 'Management approved',
                                        'Management rejected' => 'Management rejected',    
                                        'Manager rejected' => 'Manager rejected',
                                        'Closed' => 'Closed','Rejected' => 'Rejected',
            ));
        
        $raised_date = new Zend_Form_Element_Text("raised_date");        
        $raised_date->setLabel("Raised On");
        $raised_date->setAttrib('readonly', 'readonly');
        
        $submit = new Zend_Form_Element_Button('submit');        
        $submit->setAttrib('id', 'idsubmitbutton');
        $submit->setLabel('Report'); 
        
        $this->addElements(array($raised_by, $service_desk_type, $service_request_id, $priority, $status, $raised_date, $submit));
        $this->setElementDecorators(array('ViewHelper')); 
    }
}