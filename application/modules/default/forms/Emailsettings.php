<?php

class Default_Form_Emailsettings extends Zend_Form
{
    public function init()
    {
        $this->setMethod('post');
        $this->setAttrib('id', 'formid');
        $this->setAttrib('name', 'emailsettings');
		        
        $id = new Zend_Form_Element_Hidden("id");
        $id_val = Zend_Controller_Front::getInstance()->getRequest()->getParam('id',null);
		
        $username = new Zend_Form_Element_Text("username");
        $username->setLabel("User name");	
        $username->setAttrib("class", "formDataElement");
       // $username->setRequired("true");
        $username->setAttrib('maxlength', '100');
        //$username->addValidator('NotEmpty', false, array('messages' => 'Please enter username.'));
        
        $tls = new Zend_Form_Element_Text("tls");
        $tls->setLabel("Secure Transport Layer");	
        $tls->setAttrib("class", "formDataElement");
       // $tls->setRequired("true");
        $tls->setAttrib('maxlength', '40');
      //  $tls->addValidator('NotEmpty', false, array('messages' => 'Please enter secure transport layer.'));
        
        $auth = new Zend_Form_Element_Select("auth");
        $auth->setLabel("Authentication Type");	
        $auth->setMultiOptions(array(							
							'true'=>'True' ,
							'false'=>'False'
							));
        $auth->setAttrib("class", "formDataElement");
		$auth->setAttrib("onChange","toggleAuth()");
        $auth->setRequired("true");
        $auth->setAttrib('maxlength', '50');
        $auth->addValidator('NotEmpty', false, array('messages' => 'Please enter authentication type.'));
        
        $port = new Zend_Form_Element_Text("port");
        $port->setLabel("Port");	
        $port->setAttrib("class", "formDataElement");
        $port->setRequired("true");
        $port->setAttrib('maxlength', '50');
        $port->addValidator('NotEmpty', false, array('messages' => 'Please enter port.'));
        
        $password = new Zend_Form_Element_Text("password");
        $password->setLabel("Password");	
        $password->setAttrib("class", "formDataElement");
       // $password->setRequired("true");
        $password->setAttrib('maxlength', '100');
       // $password->addValidator('NotEmpty', false, array('messages' => 'Please enter password.'));
        
        $server_name = new Zend_Form_Element_Text("server_name");
        $server_name->setLabel("SMTP Server");	
        $server_name->setAttrib("class", "formDataElement");
        $server_name->setRequired("true");
        $server_name->setAttrib('maxlength', '100');
        $server_name->addValidator('NotEmpty', false, array('messages' => 'Please enter SMTP Server.'));
                        	                        	       

	$submit = new Zend_Form_Element_Submit("submit");
        $submit->setLabel("Save");  
        $submit->setAttrib('id', 'submitbutton');
        $submit->setAttrib("class", "formSubmitButton");
       

        $this->addElements(array($id,$submit,$username,$tls,$auth,$port,$password,$server_name));
        $this->setElementDecorators(array('ViewHelper')); 
    }//end of init function.
}//end of class