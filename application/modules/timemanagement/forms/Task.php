<?php

class Timemanagement_Form_Task extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('action',BASE_URL.'timemanagement/defaulttasks/edit');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'defaulttasks');


        $id = new Zend_Form_Element_Hidden('id');
		
		$task = new Zend_Form_Element_Text('task');
        $task->setAttrib('maxLength', 100);
        
        $task->setRequired(true);
        $task->addValidator('NotEmpty', false, array('messages' => 'Please enter default task.'));
		$task->addValidator("regex",true,array(
									'pattern'=> '/^(?=.*[a-zA-Z])([a-zA-Z0-9& ]*)$/',
								    'messages'=>array(
									     'regexNotMatch'=>'Please enter a valid default task.'
								     )
					       ));	
        $task->addValidator(new Zend_Validate_Db_NoRecordExists(
                                              array('table'=>'tm_tasks',
                                                     'field'=>'task',
                                                     'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" and is_active=1',    
                                                 ) )  
                                    );
        $task->getValidator('Db_NoRecordExists')->setMessage('Default task already exists.');	
        	
      
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');

		 $this->addElements(array($id,$task,$submit));
         $this->setElementDecorators(array('ViewHelper')); 
	}
}