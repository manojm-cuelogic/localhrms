<?php

class Default_Form_Servicerequest extends Zend_Form
{
    public function init()
    {
        $this->setMethod('post');	
        $this->setAttrib('id', 'formid');
        $this->setAttrib('name', 'servicerequest');

        $id = new Zend_Form_Element_Hidden('id');	
        $service_desk_id = new Zend_Form_Element_Hidden('service_desk_id');        
        
        $service_desk_conf_id = new Zend_Form_Element_Select('service_desk_conf_id');        
        $service_desk_conf_id->setLabel("Category");		
        $service_desk_conf_id->setRequired(true);
        $service_desk_conf_id->addValidator('NotEmpty', false, array('messages' => 'Please select category.'));
        $service_desk_conf_id->addMultiOptions(array('' => 'Select category'));
        $service_desk_conf_id->setRegisterInArrayValidator(false);  
		
       	$service_request_id = new Zend_Form_Element_Select('service_request_id');        
        $service_request_id->setLabel("Request Type");		
        $service_request_id->setRequired(true);
        $service_request_id->addValidator('NotEmpty', false, array('messages' => 'Please select request type.'));
        $service_request_id->addMultiOptions(array('' => 'Select request'));
        $service_request_id->setRegisterInArrayValidator(false);  
        
        $priority = new Zend_Form_Element_Select('priority');        
        $priority->setLabel("Priority");		
        $priority->setRequired(true);
        $priority->addValidator('NotEmpty', false, array('messages' => 'Please select priority.'));
        $priority->addMultiOptions(array('' => 'Select priority','1' => 'Low','2' => 'Medium','3' => 'High'));
        $priority->setRegisterInArrayValidator(false);  

        $description = new Zend_Form_Element_Textarea('description');
        $description->setLabel("Description");
        $description->setAttrib('rows', 10);
        $description->setAttrib('cols', 50);
        $description->setRequired(true);
        $description ->setAttrib('maxlength', '200');
        $description->addValidator('NotEmpty', false, array('messages' => 'Please enter description.'));

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setAttrib('id', 'submitbutton');
        $submit->setLabel('Save');

        $this->addElements(array($id,$service_desk_conf_id,$service_request_id,$priority,$description,$submit,$service_desk_id));
        $this->setElementDecorators(array('ViewHelper')); 
    }//end of init
}