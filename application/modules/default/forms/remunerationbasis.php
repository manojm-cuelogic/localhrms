<?php

class Default_Form_remunerationbasis extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('action',BASE_URL.'remunerationbasis/edit');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'remunerationbasis');


        $id = new Zend_Form_Element_Hidden('id');
		
		$remtype = new Zend_Form_Element_Text('remtype');
        $remtype->setAttrib('maxLength', 20);
        $remtype->setRequired(true);
        $remtype->addValidator('NotEmpty', false, array('messages' => 'Please enter remuneration type.'));  
		$remtype->addValidator("regex",true,array(
									'pattern'=> '/^(?=.*[a-zA-Z])([^ ][a-zA-Z0-9 ]*)$/',
								   'messages'=>array(
									   'regexNotMatch'=>'Please enter valid remuneration type.'
								   )
					));
		$remtype->addValidator(new Zend_Validate_Db_NoRecordExists(
                                              array('table'=>'main_remunerationbasis',
                                                        'field'=>'remtype',
                                                      'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" and isactive=1',    
                                                 ) )  
                                    );
        $remtype->getValidator('Db_NoRecordExists')->setMessage('Remuneration type already exists.');
		
		
	
	
		$description = new Zend_Form_Element_Textarea('remdesc');
        $description->setAttrib('rows', 10);
        $description->setAttrib('cols', 50);
		$description ->setAttrib('maxlength', '200');

        $submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');

		 $this->addElements(array($id,$remtype,$description,$submit));
         $this->setElementDecorators(array('ViewHelper')); 
	}
}