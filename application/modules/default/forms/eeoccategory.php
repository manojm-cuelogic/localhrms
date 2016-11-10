<?php

class Default_Form_eeoccategory extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('action',BASE_URL.'eeoccategory/edit');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'eeoccategory');


        $id = new Zend_Form_Element_Hidden('id');
		
		$eeoccategory = new Zend_Form_Element_Text('eeoccategory');
        $eeoccategory->setAttrib('maxLength', 20);
        
        $eeoccategory->setRequired(true);
        $eeoccategory->addValidator('NotEmpty', false, array('messages' => 'Please enter EEOC category.'));
		$eeoccategory->addValidator("regex",true,array(
									
									'pattern'=> '/^(?=.*[a-zA-Z])([^ ][a-zA-Z0-9 ]*)$/',
								   
								   'messages'=>array(
									   'regexNotMatch'=>'Please enter valid EEOC category.'
								   )
					));	
        $eeoccategory->addValidator(new Zend_Validate_Db_NoRecordExists(
                                              array('table'=>'main_eeoccategory',
                                                        'field'=>'eeoccategory',
                                                      'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" and isactive=1',    
                                                 ) )  
                                    );
        $eeoccategory->getValidator('Db_NoRecordExists')->setMessage('EEOC category already exists.');	
        	
      
		$description = new Zend_Form_Element_Textarea('description');
        $description->setAttrib('rows', 10);
        $description->setAttrib('cols', 50);
		$description ->setAttrib('maxlength', '200');

        $submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');

		 $this->addElements(array($id,$eeoccategory,$description,$submit));
         $this->setElementDecorators(array('ViewHelper')); 
	}
}