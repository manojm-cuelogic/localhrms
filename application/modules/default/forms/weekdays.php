<?php

class Default_Form_weekdays extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('action',BASE_URL.'weekdays/edit');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'weekdays');


        $id = new Zend_Form_Element_Hidden('id');
		
		$dayname = new Zend_Form_Element_Select('day_name');
        $dayname->setAttrib('class', 'selectoption');
        $dayname->setRegisterInArrayValidator(false);
        $dayname->setRequired(true);
		$dayname->addValidator('NotEmpty', false, array('messages' => 'Please select day name.'));	
			
		$dayshortcode = new Zend_Form_Element_Text('dayshortcode');
        $dayshortcode->setAttrib('maxLength', 20);
        $dayshortcode->addFilter(new Zend_Filter_StringTrim());
		$dayshortcode->addValidator(new Zend_Validate_Db_NoRecordExists(
                                              array('table'=>'main_weekdays',
                                                        'field'=>'dayshortcode',
                                                      'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" and isactive=1',    
                                                 ) )  
                                    );
        $dayshortcode->getValidator('Db_NoRecordExists')->setMessage('Day short code already exists.'); 	
		
		$daylongcode = new Zend_Form_Element_Text('daylongcode');
        $daylongcode->setAttrib('maxLength', 20);
        $daylongcode->addFilter(new Zend_Filter_StringTrim());
		
        $daylongcode->addValidator(new Zend_Validate_Db_NoRecordExists(
                                              array('table'=>'main_weekdays',
                                                        'field'=>'daylongcode',
                                                      'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" and isactive=1',    
                                                 ) )  
                                    );
        $daylongcode->getValidator('Db_NoRecordExists')->setMessage('Day long code already exists.'); 		
			
		$description = new Zend_Form_Element_Textarea('description');
        $description->setAttrib('rows', 10);
        $description->setAttrib('cols', 50);
		$description ->setAttrib('maxlength', '200');

        $submit = new Zend_Form_Element_Submit('submit');
		 $submit->setAttrib('id', 'submitbutton');
		 $submit->setLabel('Save');

		$url = "'weekdays/saveupdate/format/json'";
		$dialogMsg = "''";
		$toggleDivId = "''";
		$jsFunction = "'redirecttocontroller(\'weekdays\');'";;

		 $this->addElements(array($id,$dayname,$dayshortcode,$daylongcode,$description,$submit));
         $this->setElementDecorators(array('ViewHelper')); 
	}
}