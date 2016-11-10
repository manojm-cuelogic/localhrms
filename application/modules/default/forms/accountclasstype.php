<?php

class Default_Form_accountclasstype extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('action',BASE_URL.'accountclasstype/edit');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'accountclasstype');


        $id = new Zend_Form_Element_Hidden('id');
		
		$accountclasstype = new Zend_Form_Element_Text('accountclasstype');
        $accountclasstype->setAttrib('maxLength', 20);
        
        $accountclasstype->setRequired(true);
        $accountclasstype->addValidator('NotEmpty', false, array('messages' => 'Please enter account class type.'));  
        $accountclasstype->addValidators(array(
						 array(
							 'validator'   => 'Regex',
							 'breakChainOnFailure' => true,
							 'options'     => array( 
							 'pattern'=> '/^(?=.*[a-zA-Z])([^ ][a-zA-Z0-9 ]*)$/',
								 'messages' => array(
										 'regexNotMatch'=>'Please enter valid account class type.'
								 )
							 )
						 )
					 ));
		$accountclasstype->addValidator(new Zend_Validate_Db_NoRecordExists(
                                              array('table'=>'main_accountclasstype',
                                                        'field'=>'accountclasstype',
                                                      'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" and isactive=1',    
                                                 ) )  
                                    );
        $accountclasstype->getValidator('Db_NoRecordExists')->setMessage('Account class type already exists.');
		
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
		 

		 $this->addElements(array($id,$accountclasstype,$description,$submit));
         $this->setElementDecorators(array('ViewHelper')); 
	}
}