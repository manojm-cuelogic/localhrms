<?php

class Default_Form_businessunitsreport extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');		
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'businessunitsreport');      
		$this->setAttrib('action', BASE_URL.'reports/businessunits');  
		
		$bunitname = new Zend_Form_Element_Text('bunitname');
		$bunitname->setLabel('Business Unit');
		$bunitname->setAttrib('onblur', 'clearbuname(this)');	
		
       
        		
		$bunitcode = new Zend_Form_Element_Text('bunitcode');
		$bunitcode->setLabel('Code');		
		$bunitcode->setAttrib('onblur', 'clearbuname(this)');	
        $bunitcode->setAttrib('class', 'selectoption');      
		
						
		$startdate = new ZendX_JQuery_Form_Element_DatePicker('startdate');
		$startdate->setLabel('Started On');
		$startdate->setAttrib('readonly', 'true');	        
		$startdate->setOptions(array('class' => 'brdr_none'));	
		
		$start_date = new ZendX_JQuery_Form_Element_DatePicker('start_date');
		
		$start_date->setAttrib('readonly', 'true');	
        $start_date->setAttrib('onfocus', 'this.blur()'); 		
		$start_date->setOptions(array('class' => 'brdr_none'));	
		
		$country = new Zend_Form_Element_Select('country');
        $country->setLabel('Country');			
		   		       

		$this->addElements(array($bunitname,$bunitcode,$startdate,$country));
        $this->setElementDecorators(array('ViewHelper')); 
		$this->setElementDecorators(array(
                    'UiWidgetElement',
        ),array('startdate'));		 
	}
}

