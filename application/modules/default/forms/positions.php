<?php

class Default_Form_positions extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'positions');


        $id = new Zend_Form_Element_Hidden('id');
		$emptyflag = new Zend_Form_Element_Hidden('emptyFlag');
		
		$positionname = new Zend_Form_Element_Text('positionname');
        $positionname->setAttrib('maxLength', 50);
        $positionname->setRequired(true);
        $positionname->addValidator('NotEmpty', false, array('messages' => 'Please enter position.')); 
		$positionname->addValidators(array(
						 array(
							 'validator'   => 'Regex',
							 'breakChainOnFailure' => true,
							 'options'     => array( 
							 'pattern' =>'/^[a-zA-Z][a-zA-Z0-9\-\s]*$/i',
							 'messages' => array(
							 'regexNotMatch'=>'Please enter valid position.'
								 )
							 )
						 )
					 )); 
		$positionname->addValidator(new Zend_Validate_Db_NoRecordExists(
                                              array('table'=>'main_positions',
                                                        'field'=>'positionname',
                                                      'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" and isactive=1',    
                                                 ) )  
                                    );
        $positionname->getValidator('Db_NoRecordExists')->setMessage('Position already exists.');
		
        $jobtitleid = new Zend_Form_Element_Select('jobtitleid');
        $jobtitleid->setAttrib('class', 'selectoption');
        $jobtitleid->setRegisterInArrayValidator(false);
        $jobtitleid->addMultiOption('','Select Job Title');
        $jobtitleid->setRequired(true);
		$jobtitleid->addValidator('NotEmpty', false, array('messages' => 'Please select job title.'));		
   	
		$description = new Zend_Form_Element_Textarea('description');
        $description->setAttrib('rows', 10);
        $description->setAttrib('cols', 50);
		$description ->setAttrib('maxlength', '200');

        $submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');

		 $this->addElements(array($id,$positionname,$jobtitleid,$description,$emptyflag,$submit));
         $this->setElementDecorators(array('ViewHelper')); 
	}
}