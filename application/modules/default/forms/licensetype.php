<?php

class Default_Form_licensetype extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('action',BASE_URL.'licensetype/edit');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'licensetype');


        $id = new Zend_Form_Element_Hidden('id');
		
		$licensetype = new Zend_Form_Element_Text('licensetype');
        $licensetype->setAttrib('maxLength', 20);
        $licensetype->setRequired(true);
        $licensetype->addValidator('NotEmpty', false, array('messages' => 'Please enter license type.'));  
        $licensetype->addValidators(array(
						 array(
							 'validator'   => 'Regex',
							 'breakChainOnFailure' => true,
							 'options'     => array( 
							 'pattern'=> '/^(?=.*[a-zA-Z])([^ ][a-zA-Z0-9 ]*)$/',
								 'messages' => array(
										 'regexNotMatch'=>'Please enter valid licence type.'
								 )
							 )
						 )
					 ));
		$licensetype->addValidator(new Zend_Validate_Db_NoRecordExists(
                                              array('table'=>'main_licensetype',
                                                        'field'=>'licensetype',
                                                      'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" and isactive=1',    
                                                 ) )  
                                    );
        $licensetype->getValidator('Db_NoRecordExists')->setMessage('License type already exists.');
		
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
		 
		 $this->addElements(array($id,$licensetype,$description,$submit));
         $this->setElementDecorators(array('ViewHelper')); 
	}
}