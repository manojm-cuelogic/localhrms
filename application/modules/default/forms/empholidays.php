<?php

class Default_Form_empholidays extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'empholidays');
		
		$id = new Zend_Form_Element_Hidden('id');
				
		$userid = new Zend_Form_Element_Hidden('user_id');
		
		$holiday_group_name = new Zend_Form_Element_Text('holiday_group_name');
        $holiday_group_name->setAttrib('readonly', 'true');	
		$holiday_group_name->setAttrib('onfocus', 'this.blur()');  
			
		$holiday_group = new Zend_Form_Element_Select('holiday_group');
        $holiday_group->setRegisterInArrayValidator(false);
		$holiday_group->addMultiOption('','Select Holiday Group');
		$holiday_group->setAttrib('onchange', 'displayHolidayDates(this)');
		$holiday_group->setRequired(true);
		$holiday_group->addValidator('NotEmpty', false, array('messages' => 'Please select holiday group.'));
			
						
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');
		
		$this->addElements(array($id,$holiday_group_name,$holiday_group,$submit));
        $this->setElementDecorators(array('ViewHelper'));
     		
	}
}