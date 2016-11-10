<?php

class Default_Form_prefix extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('action',BASE_URL.'prefix/edit');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'prefix');


        $id = new Zend_Form_Element_Hidden('id');
		
		$prefix = new Zend_Form_Element_Text('prefix');
        $prefix->setAttrib('maxLength', 20);
        $prefix->setRequired(true);
        $prefix->addValidator('NotEmpty', false, array('messages' => 'Please enter prefix.'));  
        $prefix->addValidators(array(
						 array(
							 'validator'   => 'Regex',
							 'breakChainOnFailure' => true,
							 'options'     => array( 
							 'pattern'=> '/^(?=.*[a-zA-Z])([^ ][a-zA-Z0-9 ]*)$/',
								 'messages' => array(
										 'regexNotMatch'=>'Please enter valid prefix.'
								 )
							 )
						 )
					 ));
			
		$prefix->addValidator(new Zend_Validate_Db_NoRecordExists(
                                              array('table'=>'main_prefix',
                                                        'field'=>'prefix',
                                                      'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" and isactive=1',    
                                                 ) )  
                                    );
        $prefix->getValidator('Db_NoRecordExists')->setMessage('Prefix already exists.'); 			
		
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

		 $this->addElements(array($id,$prefix,$description,$submit));
         $this->setElementDecorators(array('ViewHelper')); 
	}
}