<?php

class Default_Form_empcommunicationdetails extends Zend_Form
{
    public function init()
    {
        $this->setMethod('post');
		
        $this->setAttrib('id', 'formid');
        $this->setAttrib('name', 'emplcommunicationdetails');
		    $relationArr = array(''=>'Select Relation',
                  'brother'=>"Brother" ,
                  'child'=>"Child",
                  'ex spouse'=>"Ex Spouse",
                  'father'=>"Father",
                  'mother'=>"Mother",
                  'granddaughter'=>"Grand Daughter",
                  'grandfather'=>"Grand Father",
                  'grandmother'=>'Grand Mother',
                  'grandson'=>"Grand Son",
                  'mother-in-law'=>'Mother-in-law',
                  'father-in-law'=>'Father-in-law',
                  'sister'=>"Sister",
                  'spouse'=>'Spouse'                          
            );
        $id = new Zend_Form_Element_Hidden('id');
				
        $userid = new Zend_Form_Element_Hidden('user_id');
			
        $personalemail = new Zend_Form_Element_Text('personalemail');
        $personalemail->setAttrib('maxLength', 50);
        $personalemail->addFilter('StripTags');
        $personalemail->addFilter('StringTrim');
		    $personalemail->addValidator("regex",true,array(
                           
						    'pattern'=>'/^(?!.*\.{2})[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/',                            
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter valid email.'
                           )
        	));
				
        $perm_streetaddress = new Zend_Form_Element_Text('perm_streetaddress');
        $perm_streetaddress->setAttrib('maxLength', 100);
        $perm_streetaddress->addFilter(new Zend_Filter_StringTrim());
			
        $perm_country = new Zend_Form_Element_Select('perm_country');
		    $perm_country->setAttrib('onchange', 'displayParticularState(this,"","perm_state","")');
        $perm_country->setRegisterInArrayValidator(false);
        
        $perm_state = new Zend_Form_Element_Select('perm_state');
		    $perm_state->setAttrib('onchange', 'displayParticularCity(this,"","perm_city","")');
        $perm_state->setRegisterInArrayValidator(false);
        $perm_state->addMultiOption('','Select State');
		
        $perm_city = new Zend_Form_Element_Select('perm_city');
        $perm_city->setRegisterInArrayValidator(false);
        $perm_city->addMultiOption('','Select City');

        $perm_pincode = new Zend_Form_Element_Text('perm_pincode');
        $perm_pincode->setAttrib('maxLength', 10);
        $perm_pincode->addFilter(new Zend_Filter_StringTrim());
        $perm_pincode->addValidators(array(array('StringLength',false,
                                  array('min' => 3,
                                  		'max' => 10,
                                        'messages' => array(
                                        Zend_Validate_StringLength::TOO_LONG =>
                                        'Postal code must contain at most %max% characters.',
                                        Zend_Validate_StringLength::TOO_SHORT =>
                                        'Postal code must contain at least %min% characters.')))));
		
        $perm_pincode->addValidator("regex",true,array(
                           'pattern'=>'/^(?!0{3})[0-9a-zA-Z]+$/', 

                           'messages'=>array(
                               'regexNotMatch'=>'Please enter valid postal code.'
                           )
        	));
        			

        $current_streetaddress = new Zend_Form_Element_Text('current_streetaddress');
        $current_streetaddress->setAttrib('maxLength', 100);
        $current_streetaddress->addFilter(new Zend_Filter_StringTrim());
		
			
        $current_country = new Zend_Form_Element_Select('current_country');
       	
		$current_country->setAttrib('onchange', 'displayParticularState(this,"","current_state","")');
        $current_country->setRegisterInArrayValidator(false);
        $current_country->addMultiOption('','Select Country');

        $current_state = new Zend_Form_Element_Select('current_state');
        
		$current_state->setAttrib('onchange', 'displayParticularCity(this,"","current_city","")');
        $current_state->setRegisterInArrayValidator(false);
        $current_state->addMultiOption('','Select State');
       		
        $current_city = new Zend_Form_Element_Select('current_city');
        
        $current_city->setRegisterInArrayValidator(false);
        $current_city->addMultiOption('','Select City');
		

        $current_pincode = new Zend_Form_Element_Text('current_pincode');
        $current_pincode->setAttrib('maxLength', 10);
        $current_pincode->addFilter(new Zend_Filter_StringTrim());
        $current_pincode->addValidators(array(array('StringLength',false,
                                  array('min' => 3,
                                  		'max' => 10,
                                        'messages' => array(
                                        Zend_Validate_StringLength::TOO_LONG =>
                                        'Postal code must contain at most %max% characters.',
                                        Zend_Validate_StringLength::TOO_SHORT =>
                                        'Postal code must contain at least %min% characters.')))));
		
        $current_pincode->addValidator("regex",true,array(
		                    'pattern'=>'/^(?!0{3})[0-9a-zA-Z]+$/', 
                           
                          
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter valid postal code.'
                           )
        	));	
		
        $address_flag = new Zend_Form_Element_Checkbox('address_flag');
        $address_flag->setAttrib('onclick', 'populateCurrentAddress(this)');

        $emergency_number_1 = new Zend_Form_Element_Text('emergency_number_1');
        $emergency_number_1->setAttrib('maxLength', 10);
        $emergency_number_1->addFilter(new Zend_Filter_StringTrim());
        $emergency_number_1->addValidator("regex",true,array(
                           'pattern'=>'/^(?!0{10})[0-9]+$/', 
                          
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter valid phone number.'
                           )
        	));
		
        $emergency_name_1 = new Zend_Form_Element_Text('emergency_name_1');
        $emergency_name_1->setAttrib('maxLength', 50);
        $emergency_name_1->addFilter(new Zend_Filter_StringTrim());
        $emergency_name_1->addValidator("regex",true,array(                           
                                   'pattern'=>'/^([a-zA-Z.]+ ?)+$/',
                                   'messages'=>array(
                                       'regexNotMatch'=>'Please enter only alphabets.'
                                   )
                        ));

        $relation_emergency_1 = new Zend_Form_Element_Select('relation_emergency_1');
        
        $relation_emergency_1->setRegisterInArrayValidator(false);
        $relation_emergency_1->addMultiOptions($relationArr);



        $emergency_number_2 = new Zend_Form_Element_Text('emergency_number_2');
        $emergency_number_2->setAttrib('maxLength', 10);
        $emergency_number_2->addFilter(new Zend_Filter_StringTrim());
        $emergency_number_2->addValidator("regex",true,array(
                           'pattern'=>'/^(?!0{10})[0-9]+$/', 
                          
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter valid phone number.'
                           )
          ));
    
        $emergency_name_2 = new Zend_Form_Element_Text('emergency_name_2');
        $emergency_name_2->setAttrib('maxLength', 50);
        $emergency_name_2->addFilter(new Zend_Filter_StringTrim());
        $emergency_name_2->addValidator("regex",true,array(                           
                                   'pattern'=>'/^([a-zA-Z.]+ ?)+$/',
                                   'messages'=>array(
                                       'regexNotMatch'=>'Please enter only alphabets.'
                                   )
                        ));
        $relation_emergency_2 = new Zend_Form_Element_Select('relation_emergency_2');
        
        $relation_emergency_2->setRegisterInArrayValidator(false);
        $relation_emergency_2->addMultiOptions($relationArr);

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setAttrib('id', 'submitbutton');
        $submit->setLabel('Save');

        $this->addElements(array($id,$userid,$personalemail,$perm_streetaddress,$perm_country,$perm_state,$perm_city,$perm_pincode,$current_streetaddress,$current_country,$current_state,$current_city,$current_pincode,$address_flag,$emergency_number_1,$emergency_name_1,$relation_emergency_1,$emergency_number_2,$emergency_name_2,$relation_emergency_2,$submit));
        $this->setElementDecorators(array('ViewHelper')); 		
    }
}