<?php

class Default_Form_veteranstatus extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('action',BASE_URL.'veteranstatus/edit');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'veteranstatus');


        $id = new Zend_Form_Element_Hidden('id');
		
		$veteranstatus = new Zend_Form_Element_Text('veteranstatus');
        $veteranstatus->setAttrib('maxLength', 20);
        $veteranstatus->setRequired(true);
        $veteranstatus->addValidator('NotEmpty', false, array('messages' => 'Please enter veteran status.'));  
		$veteranstatus->addValidators(array(
						 array(
							 'validator'   => 'Regex',
							 'breakChainOnFailure' => true,
							 'options'     => array( 
							 'pattern'=> '/^(?=.*[a-zA-Z])([^ ][a-zA-Z\s]*)$/',
								 'messages' => array(
										 'regexNotMatch'=>'Please enter valid veteran status.'
								 )
							 )
						 )
					 )); 	
		$veteranstatus->addValidator(new Zend_Validate_Db_NoRecordExists(
                                              array('table'=>'main_veteranstatus',
                                                        'field'=>'veteranstatus',
                                                      'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" and isactive=1',    
                                                 ) )  
                                    );
        $veteranstatus->getValidator('Db_NoRecordExists')->setMessage('Veteran status already exists.');
		
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

		 $this->addElements(array($id,$veteranstatus,$description,$submit));
         $this->setElementDecorators(array('ViewHelper')); 
	}
}