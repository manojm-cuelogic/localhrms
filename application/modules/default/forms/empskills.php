<?php

class Default_Form_empskills extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('action',BASE_URL.'employee/add');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'empskills');
		
		$id = new Zend_Form_Element_Hidden('id');
				
		$userid = new Zend_Form_Element_Hidden('user_id');
				
		$skillname = new Zend_Form_Element_Text('skillname');
		$skillname->setRequired(true);
		$skillname->setAttrib('maxLength', 50);
        $skillname->addFilter('StripTags');
        $skillname->addFilter('StringTrim');
        $skillname->addValidator('NotEmpty', false, array('messages' => 'Please enter skill.'));
        
		
		$yearsofexp = new Zend_Form_Element_Text('yearsofexp');
        $yearsofexp->setAttrib('maxLength', 5);
	    $yearsofexp->addFilter(new Zend_Filter_StringTrim());
		$yearsofexp->setRequired(true);
        $yearsofexp->addValidator('NotEmpty', false, array('messages' => 'Please enter years of experience.'));
		
		$yearsofexp->addValidators(array(
						 array(
							 'validator'   => 'Regex',
							 'breakChainOnFailure' => true,
							 'options'     => array( 
							 
							 'pattern'=>'/^[0-9]\d{0,1}(\.\d*)?$/', 
							  'messages' => array('regexNotMatch'=>'Please enter numbers less than 100.'
								 )
							 )
						 )
					 ));	
		
       
		$competencylevelid = new Zend_Form_Element_Select('competencylevelid');
        $competencylevelid->setRequired(true)->addErrorMessage('Please select competency level.');
		
		$competencylevelid->addValidator('NotEmpty', false, array('messages' => 'Please select competency level.')); 
		$competencylevelid->setRegisterInArrayValidator(false);

        $year_skill = new ZendX_JQuery_Form_Element_DatePicker('year_skill_last_used');
		$year_skill->setOptions(array('class' => 'brdr_none'));	
		$year_skill->setAttrib('readonly', 'true');	
		$year_skill->setAttrib('onfocus', 'this.blur()');
		
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');
		
		$this->addElements(array($id,$userid,$skillname,$yearsofexp,$competencylevelid,$year_skill,$submit));
        $this->setElementDecorators(array('ViewHelper'));
         $this->setElementDecorators(array(
                    'UiWidgetElement',
        ),array('year_skill_last_used')); 		
	}
}