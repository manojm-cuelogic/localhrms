<?php

class Default_Form_timezone extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('action',BASE_URL.'timezone/edit');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'timezone');


        $id = new Zend_Form_Element_Hidden('id');
		
		
		$timezone = new Zend_Form_Element_Multiselect('timezone');
        $timezone->setRegisterInArrayValidator(false);
        $timezone->setRequired(true);
		$timezone->addValidator('NotEmpty', false, array('messages' => 'Please select time zone.'));
		$timezoneModal = new Default_Model_Timezone();
	    	$timezoneData = $timezoneModal->getalltimezones();
			foreach ($timezoneData as $data){
		$timezone->addMultiOption($data['id'],$data['timezone'].' ['.$data['timezone_abbr'].']');
	    	}
		
		
		$description = new Zend_Form_Element_Textarea('description');
        $description->setAttrib('rows', 10);
        $description->setAttrib('cols', 50);
		$description ->setAttrib('maxlength', '200');

        $submit = new Zend_Form_Element_Submit('submit');
		 $submit->setAttrib('id', 'submitbutton');
		 $submit->setLabel('Save');

		$url = "'timezone/saveupdate/format/json'";
		$dialogMsg = "''";
		$toggleDivId = "''";
		$jsFunction = "'redirecttocontroller(\'timezone\');'";;
		 

		 $this->addElements(array($id,$timezone,$description,$submit));
         $this->setElementDecorators(array('ViewHelper')); 
	}
}