<?php

class Default_Form_workeligibilitydoctypes extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('action',BASE_URL.'workeligibilitydoctypes/edit');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'workeligibilitydoctypes');


        $id = new Zend_Form_Element_Hidden('id');
		
		$documenttype = new Zend_Form_Element_Text('documenttype');
        $documenttype->setAttrib('maxLength', 50);
        $documenttype->setRequired(true);
        $documenttype->addValidator('NotEmpty', false, array('messages' => 'Please enter document type.'));  
		$documenttype->addValidator("regex",true,array(
									'pattern'=> '/^(?=.*[a-zA-Z])([^ ][a-zA-Z0-9\-\s]*)$/',
								   'messages'=>array(
									   'regexNotMatch'=>'Please enter valid document type.'
								   )
					));
		$documenttype->addValidator(new Zend_Validate_Db_NoRecordExists(
                                              array('table'=>'main_workeligibilitydoctypes',
                                                        'field'=>'documenttype',
                                                      'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" and isactive=1',    
                                                 ) )  
                                    );
        $documenttype->getValidator('Db_NoRecordExists')->setMessage('Document type already exists.');	

        $issuingauthority = new Zend_Form_Element_Select('issuingauthority');
        $issuingauthority->setRegisterInArrayValidator(false);
		$issuingauthority->setMultiOptions(array(
                            ''=>'Select issuing authority',		
							'1'=>'Country' ,
							'2'=>'State',
							'3'=>'City',
							));
        $issuingauthority->setRequired(true);
		$issuingauthority->addValidator('NotEmpty', false, array('messages' => 'Please select issuing authority.'));		
   	
		$description = new Zend_Form_Element_Textarea('description');
        $description->setAttrib('rows', 10);
        $description->setAttrib('cols', 50);
		$description ->setAttrib('maxlength', '200');

        $submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');

		 $this->addElements(array($id,$documenttype,$issuingauthority,$description,$submit));
         $this->setElementDecorators(array('ViewHelper')); 
	}
}