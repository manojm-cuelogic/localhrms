<?php

class Default_Form_Feedforwardquestions extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'feedforwardquestions');


        $id = new Zend_Form_Element_Hidden('id');
		
		/*$category = new Zend_Form_Element_Select('pa_category_id');
		$category->setLabel("Category");
        $category->setAttrib('class', 'selectoption');
        $category->addMultiOption('','Select category');
        $category->setRegisterInArrayValidator(false);
        $category->setRequired(true);
		$category->addValidator('NotEmpty', false, array('messages' => 'Please select category.')); */
		
		$postid = Zend_Controller_Front::getInstance()->getRequest()->getParam('id');
		if($postid !='')
		{
			$question = new Zend_Form_Element_Text("question");
			$question->setLabel("Question");
			$question->setAttrib('maxLength', 100);
			$question->addFilter(new Zend_Filter_StringTrim());
			$question->setRequired(true);
	        $question->addValidator('NotEmpty', false, array('messages' => 'Please enter question.'));
			$question->addValidator("regex",true,array(                           
	                           'pattern'=>"/^[a-zA-Z0-9\- ?'.,\/#@$&*()!]+$/",
	                           'messages'=>array(
	                               'regexNotMatch'=>'Please enter valid question.'
	                           )
	        	));
	        $question->addValidator(new Zend_Validate_Db_NoRecordExists(
		                                            array(  'table'=>'main_pa_questions',
		                                                     'field'=>'question',
		                                                     'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" AND pa_category_id="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('pa_category_id').'" AND isactive=1',    
		
		                                                      ) ) );
			$question->getValidator('Db_NoRecordExists')->setMessage('Question already exists for the category.');	
	   	
			$description = new Zend_Form_Element_Textarea('description');
			$description->setLabel("Description");
	        $description->setAttrib('rows', 10);
	        $description->setAttrib('cols', 50);
			$description ->setAttrib('maxlength', '200');
		}

        $submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');
		if($postid !='')
			 $this->addElements(array($id,$question,$description,$submit));
	    else		 
		 	$this->addElements(array($id,$submit));
         $this->setElementDecorators(array('ViewHelper')); 
	}
}