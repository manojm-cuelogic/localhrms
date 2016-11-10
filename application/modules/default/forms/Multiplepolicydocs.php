<?php

class Default_Form_Multiplepolicydocs extends Zend_Form
{

	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('id','policydocs');
		$this->setAttrib('name','policydocs');

		$documentName = new Zend_Form_Element_Text('document_name');
		$documentName->setAttrib('id','document_name');
		$documentName->setAttrib('name','document_name[]');
		$documentName->setAttrib('maxlength','250');
		$documentName->addFilter(new Zend_Filter_StringTrim());
		$documentName->setRequired(true);
		$documentName->addValidator('NotEmpty',false,array("messages"=>'Please enter document'));
		$documentName->addValidator('regex',true,array(
			'pattern'=> '/^[a-zA-Z\-0-9\s]*$/',					
			'messages'=>array('regexNotMatch'=>'Please enter valid category')
		));


		$category_id = new Zend_Form_Element_Select('category_id');
		$category_id->setAttrib('id','category_id');
		$category_id->setAttrib('name','category_id');
		$category_id->setLabel('Category');	
		$category_id->setRequired(true);
		$category_id->addValidator('NotEmpty',false,array('messages'=>'Please select category'));
		$category_id->setRegisterInArrayValidator(false);			

		$documentDesc = new Zend_Form_Element_Textarea('description');
		$documentDesc->setAttrib('id','description');
		$documentDesc->setAttrib('name','description[]');
		$documentDesc->setAttrib('rows',10);
		$documentDesc->setAttrib('cols',50);
		$documentDesc->setAttrib('maxlength',250);

		$documentVersion = new Zend_Form_Element_Text('document_version');
		$documentVersion->setAttrib('id','document_version');
		$documentVersion->setAttrib('name','document_version[]');
		$documentVersion->setAttrib('maxlength','7');
		$documentVersion->addValidator('regex',true,array(
			'pattern'=>'/^[0-9]{1,7}(?:\.[0-9]{1,2})?$/',					
			'messages'=>array('regexNotMatch'=>'Please enter valid version')
		));
		$documentVersion->addFilter(new Zend_Filter_StringTrim());

		$submitBtn = new Zend_Form_Element_Submit('submit');
		$submitBtn->setAttrib('id','submitBtn');
		$submitBtn->setLabel('Add');
		//$submitBtn->setAttrib('onclick','validateUploadDoc()');

		$this->addElements(array($documentName, $category_id, $documentDesc, $documentVersion, $submitBtn));
		$this->setElementDecorators(array('ViewHelper'));
	}
}
?>