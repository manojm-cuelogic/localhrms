<?php

class Default_Form_employmentstatus extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('action',BASE_URL.'employmentstatus/edit');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'employmentstatus');


        $id = new Zend_Form_Element_Hidden('id');
		
		$workcode = new Zend_Form_Element_Text('workcode');
        $workcode->setAttrib('maxLength', 20);
        
        $workcode->setRequired(true);
        $workcode->addValidator('NotEmpty', false, array('messages' => 'Please enter work short code.')); 
        $workcode->addValidator("regex",true,array(
									
									'pattern'=> '/^(?=.*[a-zA-Z])([^ ][a-zA-Z0-9 ]*)$/',
								   
								   'messages'=>array(
									   'regexNotMatch'=>'Please enter valid work short code.'
								   )
					)); 		
      
		$workcodename = new Zend_Form_Element_Select('workcodename');
        $workcodename->setAttrib('class', 'selectoption');
        $workcodename->setRegisterInArrayValidator(false);
        $workcodename->setRequired(true);
		$workcodename->addValidator('NotEmpty', false, array('messages' => 'Please select work code.'));
		

		$description = new Zend_Form_Element_Textarea('description');
        $description->setAttrib('rows', 10);
        $description->setAttrib('cols', 50);
		$description ->setAttrib('maxlength', '200');

        $submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');

		 $this->addElements(array($id,$workcode,$workcodename,$description,$submit));
         $this->setElementDecorators(array('ViewHelper')); 
	}
}