<?php

class Default_Form_racecode extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('action',BASE_URL.'racecode/edit');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'racecode');


        $id = new Zend_Form_Element_Hidden('id');
		
		$racecode = new Zend_Form_Element_Text('racecode');
        $racecode->setAttrib('maxLength', 20);
        $racecode->setRequired(true);
        $racecode->addValidator('NotEmpty', false, array('messages' => 'Please enter race code.'));  
		$racecode->addValidator(new Zend_Validate_Db_NoRecordExists(
                                              array('table'=>'main_racecode',
                                                        'field'=>'racecode',
                                                      'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" and isactive=1',    
                                                 ) )  
                                    );
        $racecode->getValidator('Db_NoRecordExists')->setMessage('Race code already exists.');
		$racecode->addValidators(array(
						 array(
							 'validator'   => 'Regex',
							 'breakChainOnFailure' => true,
							 'options'     => array( 
							 'pattern'=> '/^(?=.*[a-zA-Z])([^ ][a-zA-Z0-9 ]*)$/',
								 'messages' => array(
										 'regexNotMatch'=>'Please enter valid race code.'
								 )
							 )
						 )
					 ));
		
		$racename = new Zend_Form_Element_Text('racename');
        $racename->setAttrib('maxLength', 20);
        $racename->setRequired(true);
        $racename->addValidator('NotEmpty', false, array('messages' => 'Please enter race.'));  
		$racename->addValidator(new Zend_Validate_Db_NoRecordExists(
                                              array('table'=>'main_racecode',
                                                        'field'=>'racename',
                                                      'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" and isactive=1',    
                                                 ) )  
                                    );
        $racename->getValidator('Db_NoRecordExists')->setMessage('Race already exists.');
		
		$racename->addValidators(array(
						 array(
							 'validator'   => 'Regex',
							 'breakChainOnFailure' => true,
							 'options'     => array( 
							 'pattern'=> '/^(?=.*[a-zA-Z])([^ ][a-zA-Z ]*)$/',
								 'messages' => array(
										 'regexNotMatch'=>'Please enter valid race.'
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

		$url = "'gender/saveupdate/format/json'";
		$dialogMsg = "''";
		$toggleDivId = "''";
		$jsFunction = "'redirecttocontroller(\'gender\');'";;

		 $this->addElements(array($id,$racecode,$racename,$description,$submit));
         $this->setElementDecorators(array('ViewHelper')); 
	}
}