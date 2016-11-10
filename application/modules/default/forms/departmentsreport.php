<?php

class Default_Form_departmentsreport extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');		
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'departmentsreport');      
		$this->setAttrib('action', BASE_URL.'reports/departments');  
		
		$deptname = new Zend_Form_Element_Text('deptname');
		$deptname->setLabel('Department');
		
		$deptname->setAttrib('onblur', 'clearautocompletenames(this)');	
		
		$dcode = new Zend_Form_Element_Text('dcode');
		$dcode->setLabel('Code');		
        $dcode->setAttrib('class', 'selectoption');      
		$dcode->addValidator("regex",true,array(                           
                                   'pattern'=>'/^([a-zA-Z0-9.\-]+ ?)+$/',
                                   'messages'=>array(
                                       'regexNotMatch'=>'Please enter only alpha numeric characters.'
                                   )
                        ));
		$dcode->setAttrib('onblur', 'clearautocompletenames(this)');	
		
		$bname = new Zend_Form_Element_Text('bname');
		$bname->setLabel('Business Unit');
		$bname->addValidator("regex",true,array(                           
                                   'pattern'=>'/^([a-zA-Z.\-]+ ?)+$/',
                                   'messages'=>array(
                                       'regexNotMatch'=>'Please enter only alphabets.'
                                   )
                        ));
		$bname->setAttrib('onblur', 'clearautocompletenames(this)');	
       				
		$startdate = new ZendX_JQuery_Form_Element_DatePicker('startdate');
		$startdate->setLabel('Started On');
		$startdate->setAttrib('readonly', 'true');	        
		$startdate->setOptions(array('class' => 'brdr_none'));	
		
		$country = new Zend_Form_Element_Select('country');
        $country->setLabel('Country');			
		   		       

		$this->addElements(array($deptname,$dcode,$bname,$startdate,$country));
        $this->setElementDecorators(array('ViewHelper')); 
		$this->setElementDecorators(array(
                    'UiWidgetElement',
        ),array('startdate'));		 
	}
}

