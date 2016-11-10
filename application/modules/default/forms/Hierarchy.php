<?php

class Default_Form_Hierarchy extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('action',BASE_URL.'heirarchy/edit');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'heirarchy');
		
		$id = new Zend_Form_Element_Hidden('id');
			
		$level_1 = new Zend_Form_Element_Multiselect('level_1');
        $level_1->setLabel('Level one');	
		$level_1->setRequired(true);
		$level_1->addValidator('NotEmpty', false, array('messages' => 'Please select level one employees.')); 
		$level_1->setRegisterInArrayValidator(false);

		$level_2 = new Zend_Form_Element_Multiselect('level_2');
        $level_2->setLabel('Level two');
		$level_2->setRequired(true);
		$level_2->addValidator('NotEmpty', false, array('messages' => 'Please select level two employees.')); 		
		$level_2->setRegisterInArrayValidator(false);
		
		$level_3 = new Zend_Form_Element_Multiselect('level_3');
        $level_3->setLabel('Level three');	
		$level_3->setRequired(true);
		$level_3->addValidator('NotEmpty', false, array('messages' => 'Please select level three employees.')); 		
		$level_3->setRegisterInArrayValidator(false);
		
		$level_4 = new Zend_Form_Element_Multiselect('level_4');
        $level_4->setLabel('Level four');
		$level_4->setRegisterInArrayValidator(false);
		
		$level_5 = new Zend_Form_Element_Multiselect('level_5');
        $level_5->setLabel('Level five');
		$level_5->setRegisterInArrayValidator(false);
		
		$level_6 = new Zend_Form_Element_Multiselect('level_6');
        $level_6->setLabel('Level six');
		$level_6->setRegisterInArrayValidator(false);
		
		$level_7 = new Zend_Form_Element_Multiselect('level_7');
        $level_7->setLabel('Level seven');	
		$level_7->setRegisterInArrayValidator(false);
		
		$level_8 = new Zend_Form_Element_Multiselect('level_8');
        $level_8->setLabel('Level eight');
		$level_8->setRegisterInArrayValidator(false);
		
		$level_9 = new Zend_Form_Element_Multiselect('level_9');
        $level_9->setLabel('Level nine');
		$level_9->setRegisterInArrayValidator(false);
		
		$level_10 = new Zend_Form_Element_Multiselect('level_10');
        $level_10->setLabel('Level ten');
		$level_10->setRegisterInArrayValidator(false);
		
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');
		
		$this->addElements(array($id,$level_1,$level_2,$level_3,$level_4,$level_5,$level_6,$level_7,$level_8,$level_9,$level_10,$submit));
        $this->setElementDecorators(array('ViewHelper')); 
		$this->setElementDecorators(array(
                    'UiWidgetElement',
        ),array('start_date'));
	}
}