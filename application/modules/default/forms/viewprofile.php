<?php

class Default_Form_viewprofile extends Zend_Form
{
    public function init()
    {
        $this->setMethod('post');
		$this->setAttrib('action',BASE_URL.'dashboard/viewprofile');
        $this->setAttrib('id', 'formid');
        $this->setAttrib('name', 'profileview');
		
        $id = new Zend_Form_Element_Hidden('id');
				
      	/*$userfullname = new Zend_Form_Element_Text("userfullname");
        $userfullname->setLabel("User Name");	
        $userfullname->setAttrib("class", "formDataElement");
        $userfullname->setAttrib('length', 70);
        $userfullname->setRequired(true);
        $userfullname->addValidator('NotEmpty', false, array('messages' => 'Please enter user name.'));
        $userfullname->addValidator("regex",true,array(                           
                                   'pattern'=>'/^([a-zA-Z.]+ ?)+$/',
                                   'messages'=>array(
                                      // 'regexNotMatch'=>'Please enter only alphabetic characters.'
                                    'regexNotMatch'=>'Please enter only alphabets.'
                                   )
                        ));*/
                        
        $firstname = new Zend_Form_Element_Text("firstname");
        $firstname->setLabel("First Name");	
        $firstname->setAttrib("class", "formDataElement");
        $firstname->setAttrib('length', 70);
        $firstname->setRequired(true);
        $firstname->addValidator('NotEmpty', false, array('messages' => 'Please enter first name.'));
        $firstname->addValidator("regex",true,array(                           
                                   'pattern'=>'/^([a-zA-Z.]+ ?)+$/',
                                   'messages'=>array(
                                      // 'regexNotMatch'=>'Please enter only alphabetic characters.'
                                    'regexNotMatch'=>'Please enter only alphabets.'
                                   )
                        ));

        $lastname = new Zend_Form_Element_Text("lastname");
        $lastname->setLabel("Last Name");	
        $lastname->setAttrib("class", "formDataElement");
        $lastname->setAttrib('length', 70);
        $lastname->setRequired(true);
        $lastname->addValidator('NotEmpty', false, array('messages' => 'Please enter last name.'));
        $lastname->addValidator("regex",true,array(                           
                                   'pattern'=>'/^([a-zA-Z.]+ ?)+$/',
                                   'messages'=>array(
                                      // 'regexNotMatch'=>'Please enter only alphabetic characters.'
                                    'regexNotMatch'=>'Please enter only alphabets.'
                                   )
                        ));                
                        
			
        $emailaddress = new Zend_Form_Element_Text('emailaddress');
        $emailaddress->setRequired(true);
        $emailaddress->setAttrib('maxLength', 50);
        $emailaddress->setLabel("Email");
        $emailaddress->addFilter('StripTags');
        $emailaddress->addValidator('NotEmpty', false, array('messages' => 'Please enter email.'));
		$emailaddress->addValidator("regex",true,array(
						    'pattern'=>'/^(?!.*\.{2})[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/',                            
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter valid email.'
                           )
        	));
        $emailaddress->addValidator(new Zend_Validate_Db_NoRecordExists(
                                                                array('table' => 'main_users',
                                                                'field' => 'emailaddress',
                                                                'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'"',                                                                        					        						
                                                                )));
        $emailaddress->getValidator('Db_NoRecordExists')->setMessage('Email already exists.');
				
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setAttrib('id', 'submitbutton');
        $submit->setLabel('Save');
		
        $this->addElements(array($id,$firstname,$lastname,$emailaddress,$submit));
        $this->setElementDecorators(array('ViewHelper')); 		
    }
}