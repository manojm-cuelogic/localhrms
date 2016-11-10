<?php

class Default_Form_timeformat extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('action',BASE_URL.'timeformat/edit');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'timeformat');


        $id = new Zend_Form_Element_Hidden('id');
		
		$timeformat = new Zend_Form_Element_Text('timeformat');
        $timeformat->setAttrib('maxLength', 20);
        $timeformat->addFilter(new Zend_Filter_StringTrim());
        $timeformat->setRequired(true);
        $timeformat->addValidator('NotEmpty', false, array('messages' => 'Please enter time format.'));  
			
		$description = new Zend_Form_Element_Textarea('description');
        $description->setAttrib('rows', 10);
        $description->setAttrib('cols', 50);
		$description ->setAttrib('maxlength', '200');

        $submit = new Zend_Form_Element_Submit('submit');
		 $submit->setAttrib('id', 'submitbutton');
		 $submit->setLabel('Save');

		$url = "'timeformat/saveupdate/format/json'";
		$dialogMsg = "''";
		$toggleDivId = "''";
		$jsFunction = "'redirecttocontroller(\'timeformat\');'";;

		 $this->addElements(array($id,$timeformat,$description,$submit));
         $this->setElementDecorators(array('ViewHelper')); 
	}
}