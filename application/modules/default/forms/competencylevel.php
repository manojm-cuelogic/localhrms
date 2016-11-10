<?php

class Default_Form_competencylevel extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('action',BASE_URL.'competencylevel/edit');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'competencylevel');


        $id = new Zend_Form_Element_Hidden('id');
		
		$competencylevel = new Zend_Form_Element_Text('competencylevel');
        $competencylevel->setAttrib('maxLength', 20);
        
        $competencylevel->setRequired(true);
        $competencylevel->addValidator('NotEmpty', false, array('messages' => 'Please enter competency level.')); 
		$competencylevel->addValidator("regex",true,array(
									
									'pattern'=> '/^(?=.*[a-zA-Z])([^ ][a-zA-Z0-9 ]*)$/',
								   
								   'messages'=>array(
									   'regexNotMatch'=>'Please enter valid competency level.'
								   )
					));
		$competencylevel->addValidator(new Zend_Validate_Db_NoRecordExists(
                                              array('table'=>'main_competencylevel',
                                                        'field'=>'competencylevel',
                                                      'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" and isactive=1',    
                                                 ) )  
                                    );
        $competencylevel->getValidator('Db_NoRecordExists')->setMessage('Competency level already exists.');		
		
		
   	
		$description = new Zend_Form_Element_Textarea('description');
        $description->setAttrib('rows', 10);
        $description->setAttrib('cols', 50);
		$description ->setAttrib('maxlength', '200');

        $submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');

		 $this->addElements(array($id,$competencylevel,$description,$submit));
         $this->setElementDecorators(array('ViewHelper')); 
	}
}