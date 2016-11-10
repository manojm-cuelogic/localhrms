<?php

class Default_Form_agencylistreport extends Zend_Form
{
	public function init()
	{
	    $this->setMethod('post');		
		$this->setAttrib('id', 'agencylistreport');
		$this->setAttrib('name','agencylistreport');
		$this->setAttrib('action',BASE_URL.'reports/agencylistreport');
		
		$agencyname = new Zend_Form_Element_Text('agencynamef');
		$agencyname->setLabel('Agency');
		$agencyname->setAttrib('onblur', 'clearagencyname(this)');
		
        $agencyname->setAttrib('maxLength', 50);
        
			
				
		$primaryphone = new Zend_Form_Element_Text('primaryphonef');
		$primaryphone->setLabel('Primary Phone');
		$primaryphone->setAttrib('onblur', 'blurelement(this)');
        $primaryphone->setAttrib('maxLength', 15);
        $primaryphone->addFilter(new Zend_Filter_StringTrim());
        $primaryphone->addValidators(array(array('StringLength',false,
									  array('min' => 10,
											'max' => 15,
											'messages' => array(
											Zend_Validate_StringLength::TOO_LONG =>
											'Primary phone number must contain at most %max% characters',
											Zend_Validate_StringLength::TOO_SHORT =>
											'Primary phone number must contain at least %min% characters.'
											)))));
		$primaryphone->addValidator("regex",true,array(
                           'pattern'=>'/^(?!0{10})[0-9]+$/', 
                          
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter valid phone number.'
                           )
        ));	
		 
						 
		$checktype = new Zend_Form_Element_Multiselect('bg_checktypef');
		$checktype->setLabel('Screening Type');
		
		$checktypeModal = new Default_Model_Bgscreeningtype();
	    	$typesData = $checktypeModal->fetchAll('isactive=1','type');
			foreach ($typesData->toArray() as $data){
		$checktype->addMultiOption($data['id'],$data['type']);
	    	}
		$checktype->setRegisterInArrayValidator(false);	
		$checktype->setAttrib('onchange', 'changeelement(this)');
						
		$website = new Zend_Form_Element_Text('website_urlf');
		$website->setLabel('Website Url');
		$website->setAttrib('maxLength', 50);
        $website->addFilter(new Zend_Filter_StringTrim());
      	   
      	$website->setAttrib('onblur', 'clearagencyname(this)');
		
		
		
		$this->addElements(array($agencyname,$primaryphone,$checktype,$website));
        $this->setElementDecorators(array('ViewHelper')); 
	}
}
?>