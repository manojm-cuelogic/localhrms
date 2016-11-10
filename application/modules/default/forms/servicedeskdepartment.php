<?php

class Default_Form_servicedeskdepartment extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'servicedeskdepartment');

        $id = new Zend_Form_Element_Hidden('id');
		
		$servicedeskdepartment = new Zend_Form_Element_Text("service_desk_name");
		$servicedeskdepartment->setLabel("Category");
		$servicedeskdepartment->setAttrib('maxLength', 30);
		$servicedeskdepartment->addFilter(new Zend_Filter_StringTrim());
		$servicedeskdepartment->setRequired(true);
        $servicedeskdepartment->addValidator('NotEmpty', false, array('messages' => 'Please enter category.'));
		$servicedeskdepartment->addValidator("regex",true,array(                           
                           'pattern'=>'/^[a-zA-Z0-9\- ]+$/',
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter valid category.'
                           )
        	));
        $servicedeskdepartment->addValidator(new Zend_Validate_Db_NoRecordExists(
	                                            array(  'table'=>'main_sd_depts',
	                                                     'field'=>'service_desk_name',
	                                                     'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" AND isactive=1',    
	
	                                                      ) ) );
		$servicedeskdepartment->getValidator('Db_NoRecordExists')->setMessage('Category name already exists.');	

		$description = new Zend_Form_Element_Textarea('description');
		$description->setLabel("Description");
        $description->setAttrib('rows', 10);
        $description->setAttrib('cols', 50);
		$description ->setAttrib('maxlength', '200');

        $submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');

		 $this->addElements(array($id,$servicedeskdepartment,$description,$submit));
         $this->setElementDecorators(array('ViewHelper')); 
	}
}