<?php

class Default_Form_attendancestatuscode extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('action',BASE_URL.'attendancestatuscode/edit');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'attendancestatuscode');


        $id = new Zend_Form_Element_Hidden('id');
		
		$attendancestatuscode = new Zend_Form_Element_Text('attendancestatuscode');
        $attendancestatuscode->setAttrib('maxLength', 20);
        //$attendancestatuscode->addFilter(new Zend_Filter_StringTrim());
        $attendancestatuscode->setRequired(true);
        $attendancestatuscode->addValidator('NotEmpty', false, array('messages' => 'Please enter attendance status.'));  
		$attendancestatuscode->addValidator("regex",true,array(
									
									'pattern'=> '/^(?=.*[a-zA-Z])([^ ][a-zA-Z0-9 ]*)$/',
								   
								   'messages'=>array(
									   'regexNotMatch'=>'Please enter valid attendance status.'
								   )
					));
   		$attendancestatuscode->addValidator(new Zend_Validate_Db_NoRecordExists(
                                              array('table'=>'main_attendancestatuscode',
                                                        'field'=>'attendancestatuscode',
                                                      'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" and isactive=1',    
                                                 ) )  
                                    );
        $attendancestatuscode->getValidator('Db_NoRecordExists')->setMessage('Attendance status already exists.');		
		
		
   	
		$description = new Zend_Form_Element_Textarea('description');
        $description->setAttrib('rows', 10);
        $description->setAttrib('cols', 50);
		$description ->setAttrib('maxlength', '200');

        $submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');

		 $this->addElements(array($id,$attendancestatuscode,$description,$submit));
         $this->setElementDecorators(array('ViewHelper')); 
	}
}