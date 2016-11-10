<?php

class Default_Form_Categories extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('id','pdcategories');
		$this->setAttrib('name','pdcategories');

		$categoryName = new Zend_Form_Element_Text('category');
		$categoryName->setAttrib('id','category');
		$categoryName->setAttrib('name','category');
		$categoryName->setAttrib('maxlength','30');
		$categoryName->setAttrib('onblur','chkCategory()');
		$categoryName->setAttrib('onkeypress','chkCategory()');
		$categoryName->addFilter(new Zend_Filter_StringTrim());
		$categoryName->setRequired(true);
		$categoryName->addValidator('NotEmpty',false,array("messages"=>'Please enter category'));
		$categoryName->addValidator('regex',true,array(
			'pattern'=>'/^[a-zA-Z0-9][\s+[a-zA-Z0-9]+]*$/', //'/^[a-zA-Z][a-zA-Z0-9\s]*$/', 	/^[a-z0-9 ]+$/i						
			'messages'=>array('regexNotMatch'=>'Please enter valid category')
		));
		$categoryName->addValidator(new Zend_Validate_Db_NoRecordExists(
			array(
				'table' => 'main_pd_categories',
				'field' => 'category',
				'exclude' => 'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" and isactive = 1'
			)	
		));
		$categoryName->getValidator('Db_NoRecordExists')->setMessage('Category already exists');

		$categoryDesc = new Zend_Form_Element_Textarea('description');
		$categoryDesc->setAttrib('id','description');
		$categoryDesc->setAttrib('name','description');
		$categoryDesc->setAttrib('rows',10);
		$categoryDesc->setAttrib('cols',50);
		$categoryDesc->setAttrib('maxlength',250);

		$submitBtn = new Zend_Form_Element_Submit('submit');
		$submitBtn->setAttrib('id','submitBtn');
		$submitBtn->setLabel('Add');

		$this->addElements(array($categoryName,$categoryDesc,$submitBtn));
		$this->setElementDecorators(array('ViewHelper'));
	}
}
?>