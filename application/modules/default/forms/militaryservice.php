<?php

class Default_Form_militaryservice extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('action',BASE_URL.'militaryservice/edit');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'militaryservice');


        $id = new Zend_Form_Element_Hidden('id');
		
		$militaryservicetype = new Zend_Form_Element_Text('militaryservicetype');
        $militaryservicetype->setAttrib('maxLength', 20);
        $militaryservicetype->addFilter(new Zend_Filter_StringTrim());
        $militaryservicetype->setRequired(true);
        $militaryservicetype->addValidator('NotEmpty', false, array('messages' => 'Please enter military service type.'));  
		$militaryservicetype->addValidators(array(
						 array(
							 'validator'   => 'Regex',
							 'breakChainOnFailure' => true,
							 'options'     => array( 
							 'pattern'=> '/^(?=.*[a-zA-Z])([^ ][a-zA-Z\s]*)$/',
								 'messages' => array(
										 'regexNotMatch'=>'Please enter valid military service type.'
								 )
							 )
						 )
					 )); 	
		$militaryservicetype->addValidator(new Zend_Validate_Db_NoRecordExists(
                                              array('table'=>'main_militaryservice',
                                                        'field'=>'militaryservicetype',
                                                      'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" and isactive=1',    
                                                 ) )  
                                    );
        $militaryservicetype->getValidator('Db_NoRecordExists')->setMessage('Military service type already exists.');
		
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

		 $this->addElements(array($id,$militaryservicetype,$description,$submit));
         $this->setElementDecorators(array('ViewHelper')); 
	}
}