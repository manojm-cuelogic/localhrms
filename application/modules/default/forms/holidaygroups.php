<?php

class Default_Form_holidaygroups extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'holidaygroups');


        $id = new Zend_Form_Element_Hidden('id');
		
		
		$groupname = new Zend_Form_Element_Text('groupname');
        $groupname->setAttrib('maxLength', 20);
        $groupname->addFilter(new Zend_Filter_StringTrim());
        $groupname->setRequired(true);
        $groupname->addValidator('NotEmpty', false, array('messages' => 'Please enter group name.'));  
        $groupname->addValidator("regex",true,array(                           
                           'pattern'=>'/^[a-zA-Z0-9.\- ?]+$/',
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter valid group name.'
                           )
        	));
		$groupname->addValidator(new Zend_Validate_Db_NoRecordExists(
	                                            array(  'table'=>'main_holidaygroups',
	                                                     'field'=>'groupname',
	                                                     'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" and isactive=1',    
	
	                                                      ) ) );

        $groupname->getValidator('Db_NoRecordExists')->setMessage('Group name already exists.'); 
		
		   	
		$description = new Zend_Form_Element_Textarea('description');
        $description->setAttrib('rows', 10);
        $description->setAttrib('cols', 50);
		$description ->setAttrib('maxlength', '200');

        $submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');

		 $this->addElements(array($id,$groupname,$description,$submit));
         $this->setElementDecorators(array('ViewHelper')); 
	}
}