<?php

class Default_Form_Forgotpassword extends Zend_Form
{
    private $_timeout;
	
	public function __construct($options=null) {
		if (is_array($options)) {
			if (!empty($options['custom'])) {
				if (!empty($options['custom']['timeout'])) {
					$this->_timeout= $options['custom']['timeout'];
				}
				unset($options['custom']);
			}
		}	
		parent::__construct($options);
	}
	
    public function init ()
    {
	
        $this->setMethod('post');
		$this->setAction(BASE_URL.'index/editforgotpassword');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'forgotpassword');
        
		$id = new Zend_Form_Element_Hidden('id');
        $username = new Zend_Form_Element_Text('emailaddress');
        $username->setAttrib('class', 'email-status');
        
		
		
        $username->setLabel('Email Address:');
        $username->setRequired(true);
        $username->addFilter('StripTags');
        $username->addFilter('StringTrim');
        $username->addValidator('NotEmpty', false, array('messages' => 'Please enter email.'));
        $username->addValidator('EmailAddress');		
			   
		$submit = new Zend_Form_Element_Submit('submit');
		
		 $submit->setAttrib('id', 'submitbutton');
		 $submit->setLabel('SEND');	   
		
        $url = "'default/index/editforgotpassword/format/json'";
		$dialogMsg = "''";
		$toggleDivId = "''";
		$jsFunction = "''";
		 

		 $submit->setOptions(array('onclick' => "saveDetails($url,$dialogMsg,$toggleDivId,$jsFunction);"
		));

		 $this->addElements(array($id,$username,$submit));
         $this->setElementDecorators(array('ViewHelper')); 
    }
}


