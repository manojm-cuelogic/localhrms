<?php

class Default_Form_emppersonaldetails extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'emppersonaldetails');
		
		$id = new Zend_Form_Element_Hidden('id');
				
		$userid = new Zend_Form_Element_Hidden('user_id');
				
		$genderid = new Zend_Form_Element_Select('genderid');
		$genderid->addMultiOption('','Select Gender');
    	$genderid->setRegisterInArrayValidator(false);

        $maritalstatusid = new Zend_Form_Element_Select('maritalstatusid');
		$maritalstatusid->addMultiOption('','Select Marital Status');
        $maritalstatusid->setRegisterInArrayValidator(false);
		
		$ethniccodeid = new Zend_Form_Element_Select('ethniccodeid');
		$ethniccodeid->addMultiOption('','Select Ethnic Code');
		$ethniccodeid->setLabel('Ethnic Code');
        $ethniccodeid->setRegisterInArrayValidator(false);
        
		

        $racecodeid = new Zend_Form_Element_Select('racecodeid');
		$racecodeid->addMultiOption('','Select Race Code');
		$racecodeid->setLabel('Race Code');
        $racecodeid->setRegisterInArrayValidator(false);
        
		
        
        $languageid = new Zend_Form_Element_Select('languageid');
		$languageid->addMultiOption('','Select Language');
		$languageid->setLabel('Language');
        $languageid->setRegisterInArrayValidator(false);
        
	

        $nationalityid = new Zend_Form_Element_Select('nationalityid');
		$nationalityid->addMultiOption('','Select Nationality');
        $nationalityid->setRegisterInArrayValidator(false);

        $dob = new ZendX_JQuery_Form_Element_DatePicker('dob');
		$dob->setOptions(array('class' => 'brdr_none'));	
		$dob->setAttrib('readonly', 'true');
		$dob->setAttrib('onfocus', 'this.blur()');
		//DOB should not be current date....
		
		/*
        $celebrated_dob = new ZendX_JQuery_Form_Element_DatePicker('celebrated_dob');
		$celebrated_dob->setOptions(array('class' => 'brdr_none'));	
		$celebrated_dob->setAttrib('readonly', 'true');
		$celebrated_dob->setAttrib('onfocus', 'this.blur()');
		*/
		
        $bloodgroup = new Zend_Form_Element_Select('bloodgroup');
        $bloodgroup->addMultiOption('','Select Blood Group');
        $bloodgroup->addMultiOption('A+','A+');
        $bloodgroup->addMultiOption('A-','A-');
        $bloodgroup->addMultiOption('B+','B+');
        $bloodgroup->addMultiOption('B-','B-');
        $bloodgroup->addMultiOption('AB+','AB+');
        $bloodgroup->addMultiOption('AB-','AB-');
        $bloodgroup->addMultiOption('O+','O+');
        $bloodgroup->addMultiOption('O-','O-');
    	$bloodgroup->setAttrib('required',''); 
        $bloodgroup->setRegisterInArrayValidator(false);
    	
				
		/*$submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');*/
		
		$submitadd = new Zend_Form_Element_Button('submitbutton');
		$submitadd->setAttrib('id', 'submitbuttons');
		$submitadd->setAttrib('onclick', 'validatedocumentonsubmit(this)');
		$submitadd->setLabel('Save');
		
		$this->addElements(array($id,$userid,$genderid,$maritalstatusid,$nationalityid,$ethniccodeid,$racecodeid,$languageid,$dob,$bloodgroup,$submitadd));
        $this->setElementDecorators(array('ViewHelper')); 
 		 $this->setElementDecorators(array(
                    'UiWidgetElement',
        ),array('dob')); 
	}
}