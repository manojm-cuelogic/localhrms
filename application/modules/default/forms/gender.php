<?php

class Default_Form_gender extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('action',BASE_URL.'gender/edit');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'gender');


        $id = new Zend_Form_Element_Hidden('id');
		
		$gendercode = new Zend_Form_Element_Text('gendercode');
        $gendercode->setAttrib('maxLength', 20);
        $gendercode->setRequired(true);
        $gendercode->addValidator('NotEmpty', false, array('messages' => 'Please enter gender code.'));  
        $gendercode->addValidators(array(
						 array(
							 'validator'   => 'Regex',
							 'breakChainOnFailure' => true,
							 'options'     => array( 
							 'pattern'=> '/^(?=.*[a-zA-Z])([^ ][a-zA-Z0-9 ]*)$/',
								 'messages' => array(
										 'regexNotMatch'=>'Please enter valid gender code.'
								 )
							 )
						 )
					 )); 
		$gendercode->addValidator(new Zend_Validate_Db_NoRecordExists(
                                              array('table'=>'main_gender',
                                                        'field'=>'gendercode',
                                                      'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" and isactive=1',    
                                                 ) )  
                                    );
        $gendercode->getValidator('Db_NoRecordExists')->setMessage('Gender code already exists.'); 	
		
		$gendername = new Zend_Form_Element_Text('gendername');
        $gendername->setAttrib('maxLength', 50);
        $gendername->setRequired(true);
        $gendername->addValidator('NotEmpty', false, array('messages' => 'Please enter gender.'));  
        $gendername->addValidator("regex",true,array(
                           'pattern'=> '/^(?=.*[a-zA-Z])([^ ][a-zA-Z ]*)$/',					   
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter valid gender.'
                           )
        	));
        			
		$gendername->addValidator(new Zend_Validate_Db_NoRecordExists(
                                              array('table'=>'main_gender',
                                                        'field'=>'gendername',
                                                      'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" and isactive=1',    
                                                 ) )  
                                    );
        $gendername->getValidator('Db_NoRecordExists')->setMessage('Gender already exists.'); 	
		
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
		 
		 $this->addElements(array($id,$gendercode,$gendername,$description,$submit));
         $this->setElementDecorators(array('ViewHelper')); 
	}
}