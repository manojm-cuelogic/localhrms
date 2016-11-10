<?php

class Default_Form_numberformats extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'numberformats');


        $id = new Zend_Form_Element_Hidden('id');
		
		$numberformattype = new Zend_Form_Element_Text('numberformattype');
        $numberformattype->setAttrib('maxLength', 30);
        $numberformattype->setRequired(true);
        $numberformattype->addValidator('NotEmpty', false, array('messages' => 'Please enter number format type.'));  
		$numberformattype->addValidators(array(
						 array(
							 'validator'   => 'Regex',
							 'breakChainOnFailure' => true,
							 'options'     => array( 
							 'pattern'=>'/^(\d?\d?\d(,\d\d\d)*|\d+)(\.\d\d)?$/',
							 'messages' => array(
							 'regexNotMatch'=>'Please enter valid number format type.'
								 )
							 )
						 )
					 )); 
		
		$numberformattype->addValidator(new Zend_Validate_Db_NoRecordExists(
                                              array('table'=>'main_numberformats',
                                                        'field'=>'numberformattype',
                                                      'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" and isactive=1',    
                                                 ) )  
                                    );
        $numberformattype->getValidator('Db_NoRecordExists')->setMessage('Number format type already exists.');
		
		$description = new Zend_Form_Element_Textarea('description');
        $description->setAttrib('rows', 10);
        $description->setAttrib('cols', 50);
		$description ->setAttrib('maxlength', '200');

        $submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');

		 $this->addElements(array($id,$numberformattype,$description,$submit));
         $this->setElementDecorators(array('ViewHelper')); 
	}
}