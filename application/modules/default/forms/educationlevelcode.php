<?php

class Default_Form_educationlevelcode extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('action',BASE_URL.'educationlevelcode/edit');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'educationlevelcode');


        $id = new Zend_Form_Element_Hidden('id');
		
		$educationlevelcode = new Zend_Form_Element_Text('educationlevelcode');
        $educationlevelcode->setAttrib('maxLength', 20);
        
        $educationlevelcode->setRequired(true);
        $educationlevelcode->addValidator('NotEmpty', false, array('messages' => 'Please enter education level.'));
		$educationlevelcode->addValidator("regex",true,array(                           
							   
							   'pattern'=> '/^(?=.*[a-zA-Z])([^ ][a-zA-Z ]*)$/',
							   'messages'=>array(
								   'regexNotMatch'=>'Please enter valid education level.'
							   )
					));		
		$educationlevelcode->addValidator(new Zend_Validate_Db_NoRecordExists(
                                              array('table'=>'main_educationlevelcode',
                                                        'field'=>'educationlevelcode',
                                                      'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" and isactive=1',    
                                                 ) )  
                                    );
        $educationlevelcode->getValidator('Db_NoRecordExists')->setMessage('Education level already exists.');		
   	
		$description = new Zend_Form_Element_Textarea('description');
        $description->setAttrib('rows', 10);
        $description->setAttrib('cols', 50);
		$description ->setAttrib('maxlength', '200');

        $submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');

		 $this->addElements(array($id,$educationlevelcode,$description,$submit));
         $this->setElementDecorators(array('ViewHelper')); 
	}
}