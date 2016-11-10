<?php

class Default_Form_bankaccounttype extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('action',BASE_URL.'bankaccounttype/edit');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'bankaccounttype');


        $id = new Zend_Form_Element_Hidden('id');
		
		$bankaccounttype = new Zend_Form_Element_Text('bankaccounttype');
        $bankaccounttype->setAttrib('maxLength', 50);
        
        $bankaccounttype->setRequired(true);
        $bankaccounttype->addValidator('NotEmpty', false, array('messages' => 'Please enter bank account type.'));  
		$bankaccounttype->addValidators(array(
						 array(
							 'validator'   => 'Regex',
							 'breakChainOnFailure' => true,
							 'options'     => array( 
							 
							 'pattern'=> '/^(?=.*[a-zA-Z])([^ ][a-zA-Z0-9\-\s]*)$/',
								 'messages' => array(
										 'regexNotMatch'=>'Please enter valid bank account type.'
								 )
							 )
						 )
					 )); 
		$bankaccounttype->addValidator(new Zend_Validate_Db_NoRecordExists(
                                              array('table'=>'main_bankaccounttype',
                                                        'field'=>'bankaccounttype',
                                                      'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" and isactive=1',    
                                                 ) )  
                                    );
        $bankaccounttype->getValidator('Db_NoRecordExists')->setMessage('Bank account type already exists.');
		
		$description = new Zend_Form_Element_Textarea('description');
        $description->setAttrib('rows', 10);
        $description->setAttrib('cols', 50);
		$description ->setAttrib('maxlength', '200');

        $submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');

		 $this->addElements(array($id,$bankaccounttype,$description,$submit));
         $this->setElementDecorators(array('ViewHelper')); 
	}
}