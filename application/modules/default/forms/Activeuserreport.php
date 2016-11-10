<?php

/**
 * This gives employee report form.
 */
class Default_Form_Activeuserreport extends Zend_Form
{
    public function init()
    {
        $this->setMethod('post');
        $this->setAttrib('id', 'formid');
        $this->setAttrib('name', 'frm_auser_report');

        $createddate = new Zend_Form_Element_Text("createddate");        
        $createddate->setLabel("Created Date");        
        $createddate->setAttrib('readonly', 'readonly');
        
        $logindatetime = new Zend_Form_Element_Text("logindatetime");        
        $logindatetime->setLabel("Last Login Date");
        $logindatetime->setAttrib('readonly', 'readonly');
		                     
        
        $emprole = new Zend_Form_Element_Select("emprole");
        $emprole->setRegisterInArrayValidator(false);
        $emprole->setLabel("Role");        
        $emprole->addMultiOptions(array(''=>'Select Role'));
        
        $isactive = new Zend_Form_Element_Select("isactive");
        $isactive->setLabel("Status");        
        $isactive->addMultiOptions(array(''=>'Select Status',1 => 'Active',0 => 'Inactive'));
        
        $submit = new Zend_Form_Element_Button('submit');        
        $submit->setAttrib('id', 'idsubmitbutton');
        $submit->setLabel('Report'); 
        
        $this->addElements(array($submit,$createddate,$logindatetime,$emprole,$isactive));
        $this->setElementDecorators(array('ViewHelper')); 
    }
}