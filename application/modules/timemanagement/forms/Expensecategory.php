<?php
class Timemanagement_Form_Expensecategory extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('action',BASE_URL.'timemanagement/expensecategory/edit');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'expensecategory');


        $id = new Zend_Form_Element_Hidden('id');
		
		$category = new Zend_Form_Element_Text('expense_category');
        $category->setAttrib('maxLength', 200);
        $category->setLabel("Category");
        
        $category->setRequired(true);
        $category->addValidator('NotEmpty', false, array('messages' => 'Please enter Category.'));
		$category->addValidator("regex",true,array(
									'pattern'=> '/^(?=.*[a-zA-Z])([^ ][a-zA-Z0-9 ]*)$/',
								    'messages'=>array(
									     'regexNotMatch'=>'Please enter valid Category.'
								     )
					       ));	
        $category->addValidator(new Zend_Validate_Db_NoRecordExists(
                                              array('table'=>'tm_expense_categories',
                                                     'field'=>'expense_category',
                                                     'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" and is_active=1',    
                                                 ) )  
                                    );
        $category->getValidator('Db_NoRecordExists')->setMessage('Category already exists.');	
        	
       //http://stackoverflow.com/questions/9299012/using-for-decimals-in-zend-validator-float
        $unitPrice = new Zend_Form_Element_Text('unit_price');
        $unitPrice->setAttrib('maxLength', 7);
        $unitPrice->setLabel("Unit Price");
		$unitPrice->addValidator("regex",true,array(
									'pattern'=> '/^[1-9]+(\.\d{1,2})?$/',
								    'messages'=>array(
									     'regexNotMatch'=>'Please enter valid price.'
								     )
					       ));        
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');

		 $this->addElements(array($id,$category,$unitPrice,$submit));
         $this->setElementDecorators(array('ViewHelper')); 
	}
}