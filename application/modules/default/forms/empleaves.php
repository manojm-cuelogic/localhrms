<?php

class Default_Form_empleaves extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'empleaves');
		
		$id = new Zend_Form_Element_Hidden('id');
				
		$userid = new Zend_Form_Element_Hidden('user_id');
			
		
		$emp_leave_limit = new Zend_Form_Element_Text('leave_limit');
        $emp_leave_limit->setAttrib('maxLength', 5);
        $emp_leave_limit->setAttrib('required', '');
        $emp_leave_limit->addFilter(new Zend_Filter_StringTrim());
		$emp_leave_limit->setRequired(true);
        $emp_leave_limit->addValidator('NotEmpty', false, array('messages' => 'Please enter leave limit for current year.'));
		/* Richa Comment */
		$emp_leave_limit->addValidator("regex",true,array(
                
						  // 'pattern'=>'/^(\-?[1-9]|\-?[1-9][0-9])$/',
						   'pattern'=>'/^([1-9]{0,1})([0-9]{1})(\.[0-9]{1,2})?$/',
						   
                
                           'messages'=>array(
                               'regexNotMatch'=>'Leave limit must be in numbers.'
                           )
        	));	
			
/*Richa code start*/
		$leavetypeid = new Zend_Form_Element_Select('leavetypeid');
        $leavetypeid->setAttrib('class', 'selectoption');
	    $leavetypeid->addMultiOption('','Select Leave Type');
	    $leavetypeid->addMultiOption('','Select Leave Type');
        $leavetypeid->setRegisterInArrayValidator(false);
        $leavetypeid->setAttrib('required', '');
		$leavetypeid->addValidator('NotEmpty', false, array('messages' => 'Please select leave type.'));
     	$employeeleavetypemodel = new Default_Model_Employeeleavetypes();  
        $leavetype = $employeeleavetypemodel->getactiveleavetype();
   		if(!empty($leavetype))
		    {
				if(sizeof($leavetype) > 0)
				{
					foreach ($leavetype as $leavetyperes){
						$leavetypeid->addMultiOption($leavetyperes['id'].'!@#'.$leavetyperes['numberofdays'].'!@#'.utf8_encode($leavetyperes['leavetype']),utf8_encode($leavetyperes['leavetype']));
					}
				}
			}
		else
			{
				$msgarray['leavetypeid'] = ' Leave types are not configured yet.';
			}
			///$this->view->leavetype = $leavetype;

/*Richa code end*/
		$used_leaves = new Zend_Form_Element_Text('used_leaves');
        $used_leaves->setAttrib('maxLength', 3);
		$used_leaves->setAttrib('readonly', 'true');
        $used_leaves->setAttrib('onfocus', 'this.blur()'); 		
        
		$alloted_year = new Zend_Form_Element_Text('alloted_year');
        $alloted_year->setAttrib('maxLength', 4);
		$alloted_year->setAttrib('readonly', 'true');
		$alloted_year->setAttrib('onfocus', 'this.blur()');
				
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbuttjon');
		$submit->setLabel('Save');
		
		$submitbutton = new Zend_Form_Element_Button('submitbutton');
		
		 $submitbutton->setAttrib('id', 'submitbuttonsnnn');
		$submitbutton->setLabel('Save');
		
		$this->addElements(array($id,$userid,$emp_leave_limit,$leavetypeid,$used_leaves,$alloted_year,$submit,$submitbutton));
        $this->setElementDecorators(array('ViewHelper'));
     		
	}
}