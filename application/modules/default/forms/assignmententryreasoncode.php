<?php

class Default_Form_assignmententryreasoncode extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('action',BASE_URL.'assignmententryreasoncode/edit');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'assignmententryreasoncode');


        $id = new Zend_Form_Element_Hidden('id');
		
		$assignmententryreasoncode = new Zend_Form_Element_Text('assignmententryreasoncode');
        $assignmententryreasoncode->setAttrib('maxLength', 20);
        $assignmententryreasoncode->addFilter(new Zend_Filter_StringTrim());
        $assignmententryreasoncode->setRequired(true);
        $assignmententryreasoncode->addValidator('NotEmpty', false, array('messages' => 'Please enter  assignment code.'));  
   	
		$description = new Zend_Form_Element_Textarea('description');
        $description->setAttrib('rows', 10);
        $description->setAttrib('cols', 50);
		$description ->setAttrib('maxlength', '200');

        $submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');

		 $this->addElements(array($id,$assignmententryreasoncode,$description,$submit));
         $this->setElementDecorators(array('ViewHelper')); 
	}
}