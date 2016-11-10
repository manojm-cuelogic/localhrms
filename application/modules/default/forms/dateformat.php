<?php

class Default_Form_dateformat extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('action',BASE_URL.'dateformat/edit');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'dateformat');


        $id = new Zend_Form_Element_Hidden('id');
	$example = new Zend_Form_Element_Text('example');	
		$dateformat = new Zend_Form_Element_Text('dateformat');
        $dateformat->setAttrib('maxLength', 20);
        
        $dateformat->addFilter(new Zend_Filter_StringTrim());
        $dateformat->setRequired(true);
        $dateformat->addValidator('NotEmpty', false, array('messages' => 'Please enter date format.'));  
       
			
		$description = new Zend_Form_Element_Textarea('description');
        $description->setAttrib('rows', 10);
        $description->setAttrib('cols', 50);
		$description ->setAttrib('maxlength', '200');
		

        $submit = new Zend_Form_Element_Submit('submit');
		
		 $submit->setAttrib('id', 'submitbutton');
		 $submit->setLabel('Save');

		$url = "'dateformat/saveupdate/format/json'";
		$dialogMsg = "''";
		$toggleDivId = "''";
		$jsFunction = "'redirecttocontroller(\'dateformat\');'";;
		 

		 $this->addElements(array($id,$dateformat,$description,$submit,$example));
         $this->setElementDecorators(array('ViewHelper')); 
	}
}