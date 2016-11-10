<?php

class Default_Form_Appraisalskills extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'appraisalskills');

        $id = new Zend_Form_Element_Hidden('id');
		
		$appraisalskill = new Zend_Form_Element_Text("skill_name");
		$appraisalskill->setLabel('Skill');
		$appraisalskill->setAttrib('maxLength', 30);
		$appraisalskill->addFilter(new Zend_Filter_StringTrim());
		$appraisalskill->setRequired(true);
        $appraisalskill->addValidator('NotEmpty', false, array('messages' => 'Please enter skill.'));
		$appraisalskill->addValidator("regex",true,array(                           
                           'pattern'=>'/^[a-zA-Z0-9.\- ?\',\/#@$&*()!+]+$/',
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter valid skill.'
                           )
        	));
        $appraisalskill->addValidator(new Zend_Validate_Db_NoRecordExists(
	                                            array(  'table'=>'main_pa_skills',
	                                                     'field'=>'skill_name',
	                                                     'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" AND isactive=1',    
	
	                                                      ) ) );
		$appraisalskill->getValidator('Db_NoRecordExists')->setMessage('Skill name already exists.');	

		$description = new Zend_Form_Element_Textarea('description');
		$description->setLabel("Description");
        $description->setAttrib('rows', 10);
        $description->setAttrib('cols', 50);
		$description ->setAttrib('maxlength', '200');

        $submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');

		 $this->addElements(array($id,$appraisalskill,$description,$submit));
         $this->setElementDecorators(array('ViewHelper')); 
	}
}