<?php

class Default_Form_maritalstatus extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('action',BASE_URL.'maritalstatus/edit');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'maritalstatus');


        $id = new Zend_Form_Element_Hidden('id');
		
		$maritalcode = new Zend_Form_Element_Text('maritalcode');
        $maritalcode->setAttrib('maxLength', 20);
        $maritalcode->addFilter(new Zend_Filter_StringTrim());
        $maritalcode->setRequired(true);
        $maritalcode->addValidator('NotEmpty', false, array('messages' => 'Please enter marital code.'));  
        $maritalcode->addValidators(array(
						 array(
							 'validator'   => 'Regex',
							 'breakChainOnFailure' => true,
							 'options'     => array( 
							 'pattern'=> '/^(?=.*[a-zA-Z])([^ ][a-zA-Z0-9 ]*)$/',
								 'messages' => array(
										 'regexNotMatch'=>'Please enter valid marital code.'
								 )
							 )
						 )
					 ));
		$maritalcode->addValidator(new Zend_Validate_Db_NoRecordExists(
                                              array('table'=>'main_maritalstatus',
                                                        'field'=>'maritalcode',
                                                      'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" and isactive=1',    
                                                 ) )  
                                    );
        $maritalcode->getValidator('Db_NoRecordExists')->setMessage('Marital code already exists.'); 	
		
		$maritalstatusname = new Zend_Form_Element_Text('maritalstatusname');
        $maritalstatusname->setAttrib('maxLength', 20);
        $maritalstatusname->addFilter(new Zend_Filter_StringTrim());
        $maritalstatusname->setRequired(true);
        $maritalstatusname->addValidator('NotEmpty', false, array('messages' => 'Please enter marital status.'));  
		$maritalstatusname->addValidator(new Zend_Validate_Db_NoRecordExists(
                                              array('table'=>'main_maritalstatus',
                                                        'field'=>'maritalstatusname',
                                                      'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" and isactive=1',    
                                                 ) )  
                                    );
        $maritalstatusname->getValidator('Db_NoRecordExists')->setMessage('Marital status already exists.'); 	
        $maritalstatusname->addValidators(array(
						 array(
							 'validator'   => 'Regex',
							 'breakChainOnFailure' => true,
							 'options'     => array( 
							 'pattern'=> '/^(?=.*[a-zA-Z])([^ ][a-zA-Z ]*)$/',
								 'messages' => array(
										 'regexNotMatch'=>'Please enter valid marital status.'
								 )
							 )
						 )
					 ));	
			
		$description = new Zend_Form_Element_Textarea('description');
        $description->setAttrib('rows', 10);
        $description->setAttrib('cols', 50);
		$description ->setAttrib('maxlength', '200');

        $submit = new Zend_Form_Element_Submit('submit');
		 $submit->setAttrib('id', 'submitbutton');
		 $submit->setLabel('Save');

		$url = "'maritalstatus/saveupdate/format/json'";
		$dialogMsg = "''";
		$toggleDivId = "''";
		$jsFunction = "''";;
		 

		 $this->addElements(array($id,$maritalcode,$maritalstatusname,$description,$submit));
         $this->setElementDecorators(array('ViewHelper')); 
	}
}