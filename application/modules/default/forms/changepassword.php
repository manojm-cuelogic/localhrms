<?php

class Default_Form_changepassword extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAction(BASE_URL.'dashboard/editpassword');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'editpassword');


        $id = new Zend_Form_Element_Hidden('id');
		
		$oldPassword = new Zend_Form_Element_Password('password');
    	$oldPassword->setAttrib('size',20); 
    	$oldPassword->addValidator('NotEmpty', false, array('messages' => 'Please enter current password.'));
    	                   
                          
    	$oldPassword->setAttrib('maxLength', 15);
    	$oldPassword->setRequired(true);
		$oldPassword->addFilters(array('StringTrim'));
    	
    	 
        
    	$newPassword = new Zend_Form_Element_Password('newpassword');
    	$newPassword->setAttrib('size',20);  
        $newPassword->setAttrib('maxLength',15);  		
       	$newPassword->setRequired(true);
       	$newPassword->addValidator('NotEmpty', false, array('messages' => 'Please enter new password.'));  
    	$newPassword->addValidator('stringLength', true, array('min' => 6, 'max' => 15,
    	'messages' => array(Zend_Validate_StringLength::TOO_LONG => 'The password cannot be more than 15 characters.',
                        Zend_Validate_StringLength::TOO_SHORT => 'New password should be atleast 6 characters long.') ));
                         
    	$newPassword->addFilters(array('StringTrim'));
    	
        
    	$newPasswordAgain = new Zend_Form_Element_Password('passwordagain');
    	$newPasswordAgain->setAttrib('size',20);
        $newPasswordAgain->setAttrib('maxLength',15); 		
        $newPasswordAgain->setRequired(true);
    	$newPasswordAgain->addValidator('NotEmpty', false, array('messages' => 'Please&nbsp;enter&nbsp;confirm&nbsp;password.'));
    	$newPasswordAgain->addValidator('stringLength', true, array('min' => 6, 'max' => 15,
    	'messages' => array(Zend_Validate_StringLength::TOO_LONG => 'The password cannot be more than 15 characters.',
                        Zend_Validate_StringLength::TOO_SHORT => 'Confirm password should be atleast 6 characters long.') ));
                        
        
         $newPasswordAgain->addFilters(array('StringTrim'));						   

         $submit = new Zend_Form_Element_Submit('submit');
		
		 $submit->setAttrib('id', 'submitbutton');
		 $submit->setLabel('Save');

		$url = "'dashboard/editpassword/format/json'";
		$dialogMsg = "''";
		$toggleDivId = "''";
		$jsFunction = "''";
		 

		 $submit->setOptions(array('onclick' => "saveDetails($url,$dialogMsg,$toggleDivId,$jsFunction);"
		));

		 $this->addElements(array($id,$oldPassword,$newPassword,$newPasswordAgain,$submit));
         $this->setElementDecorators(array('ViewHelper')); 
	}
}