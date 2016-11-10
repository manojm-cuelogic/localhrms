<?php

class Default_Form_bgscreeningtype extends Zend_Form
{
	public function init()
	{
	    $this->setMethod('post');		
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name','bgscreeningtype');		
		
		$id = new Zend_Form_Element_Hidden('id');
		
		$type = new Zend_Form_Element_Text('type');
        $type->setAttrib('maxLength', 50);
        $type->addFilter(new Zend_Filter_StringTrim());
        $type->setRequired(true);
        $type->addValidator('NotEmpty', false, array('messages' => 'Please enter screening type.'));  
		$type->addValidator("regex",true,array(                           
                           'pattern'=>'/^[a-zA-Z.\- ?]+$/',
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter valid screening type.'
                           )
        	));
			
		$description = new Zend_Form_Element_Textarea('description');
        $description->setAttrib('rows', 10);
        $description->setAttrib('cols', 50);		
		
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');
		
		$this->addElements(array($id,$type,$description,$submit));
        $this->setElementDecorators(array('ViewHelper')); 
     }
}