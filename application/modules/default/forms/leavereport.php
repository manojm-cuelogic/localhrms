<?php

class Default_Form_leavereport extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'leavereport');


        $id = new Zend_Form_Element_Hidden('id');
		
		$employeename = new Zend_Form_Element_Text('employeename');
		$employeename->setLabel('Leave Applied By');
        $employeename->setAttrib('onblur', 'clearautocompletename(this)');		
        		
		$department = new Zend_Form_Element_Select('department');
		$department->setLabel('Department');
		$department->addMultiOption('','Select Department');
        $department->setAttrib('class', 'selectoption');
        $department->setRegisterInArrayValidator(false);
              
        $leavestatus = new Zend_Form_Element_Select('leavestatus');
		$leavestatus->setLabel('Leave Status');
        $leavestatus->setMultiOptions(array(
                            ''=>'Select Leave Status',
							'1'=>'Pending for approval' ,
							'2'=>'Approved',
							'3'=>'Rejected',
							'4'=>'Cancel',
							));
        $leavestatus->setRegisterInArrayValidator(false);	

        $from_date = new ZendX_JQuery_Form_Element_DatePicker('from_date');
		$from_date->setLabel('From Date');
		$from_date->setAttrib('readonly', 'true');
		$from_date->setAttrib('onfocus', 'this.blur()');
		$from_date->setOptions(array('class' => 'brdr_none'));	

        $to_date = new ZendX_JQuery_Form_Element_DatePicker('to_date');
        $to_date->setLabel('To Date');
        $to_date->setAttrib('readonly', 'true');
        $to_date->setAttrib('onfocus', 'this.blur()');
        $to_date->setOptions(array('class' => 'brdr_none'));  
		
        $submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');

		$this->addElements(array($id,$employeename,$department,$leavestatus,$from_date,$to_date,$submit));
        $this->setElementDecorators(array('ViewHelper'));
        $this->setElementDecorators(array(
                    'UiWidgetElement',
        ),array('from_date','to_date'));   		 
        
	}
}