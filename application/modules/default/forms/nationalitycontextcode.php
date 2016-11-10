<?php

class Default_Form_nationalitycontextcode extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('action',BASE_URL.'nationalitycontextcode/edit');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'gender');


        $id = new Zend_Form_Element_Hidden('id');
		
		$nationalitycontextcode = new Zend_Form_Element_Text('nationalitycontextcode');
        $nationalitycontextcode->setAttrib('maxLength', 20);
        $nationalitycontextcode->setRequired(true);
        $nationalitycontextcode->addValidator('NotEmpty', false, array('messages' => 'Please enter nationality context.'));  
        $nationalitycontextcode->addValidators(array(
						 array(
							 'validator'   => 'Regex',
							 'breakChainOnFailure' => true,
							 'options'     => array( 
							 'pattern'=> '/^(?=.*[a-zA-Z])([^ ][a-zA-Z0-9 ]*)$/',
								 'messages' => array(
										 'regexNotMatch'=>'Please enter valid nationality context.'
								 )
							 )
						 )
					 ));
			
		$nationalitycontextcode->addValidator(new Zend_Validate_Db_NoRecordExists(
                                              array('table'=>'main_nationalitycontextcode',
                                                        'field'=>'nationalitycontextcode',
                                                      'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" and isactive=1',    
                                                 ) )  
                                    );
        $nationalitycontextcode->getValidator('Db_NoRecordExists')->setMessage('Nationality context already exists.');
		
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

		 $this->addElements(array($id,$nationalitycontextcode,$description,$submit));
         $this->setElementDecorators(array('ViewHelper')); 
	}
}