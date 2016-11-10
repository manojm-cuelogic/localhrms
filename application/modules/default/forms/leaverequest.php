<?php

class Default_Form_leaverequest extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'leaverequest');


        $id = new Zend_Form_Element_Hidden('id');
			
		$availableleaves = new Zend_Form_Element_Text('no_of_days');
        $availableleaves->setAttrib('readonly', 'true');
		$availableleaves->setAttrib('onfocus', 'this.blur()'); 
		
		$appliedleavesdaycount = new Zend_Form_Element_Text('appliedleavesdaycount');
        $appliedleavesdaycount->setAttrib('readonly', 'true');
		$appliedleavesdaycount->setAttrib('onfocus', 'this.blur()');
		
		$unapprovedleavesdaycount = new Zend_Form_Element_Text('unapprovedleavesdaycount');
        $unapprovedleavesdaycount->setAttrib('readonly', 'true');
        $unapprovedleavesdaycount->setAttrib('onfocus', 'this.blur()');

		$repmanagerid = new Zend_Form_Element_Text('rep_mang_id');
        $repmanagerid->setAttrib('readonly', 'true');
		$repmanagerid->setAttrib('onfocus', 'this.blur()');
		
		$issatholiday = new Zend_Form_Element_Hidden('is_sat_holiday');
        		
		$leavetypeid = new Zend_Form_Element_Select('leavetypeid');
        $leavetypeid->setAttrib('class', 'selectoption');
      /** commented on 04-02-2015 **/
	    $leavetypeid->addMultiOption('','Select Leave Type');
        $leavetypeid->setRegisterInArrayValidator(false);
        $leavetypeid->setRequired(true);
		$leavetypeid->addValidator('NotEmpty', false, array('messages' => 'Please select leave type.'));
       
        $leaveday = new Zend_Form_Element_Select('leaveday');
        $leaveday->setRegisterInArrayValidator(false);
		$leaveday->setAttrib('onchange', 'hidetodatecalender(this)');
        $leaveday->setMultiOptions(array(							
							'1'=>'Full Day' ,
							'2'=>'Half Day',
							));
        $leaveday->setRequired(true);
		$leaveday->addValidator('NotEmpty', false, array('messages' => 'Please select date.'));	

        $from_date = new ZendX_JQuery_Form_Element_DatePicker('from_date');
		$from_date->setAttrib('readonly', 'true');
		$from_date->setAttrib('onfocus', 'this.blur()');
		$from_date->setOptions(array('class' => 'brdr_none'));	
		$from_date->setRequired(true);
        $from_date->addValidator('NotEmpty', false, array('messages' => 'Please select date.'));
		
		$to_date = new ZendX_JQuery_Form_Element_DatePicker('to_date');
		$to_date->setAttrib('readonly', 'true');
		$to_date->setAttrib('onfocus', 'this.blur()');
		$to_date->setAttrib('onblur', 'validate_todate()');
		$to_date->setOptions(array('class' => 'brdr_none'));	
		
		$reason = new Zend_Form_Element_Textarea('reason');
        $reason->setAttrib('rows', 10);
        $reason->setAttrib('cols', 50);
		$reason ->setAttrib('maxlength', '400');
		$reason->setRequired(true);
        $reason->addValidator('NotEmpty', false, array('messages' => 'Please enter reason.'));
		
		$leavestatus = new Zend_Form_Element_Text('leavestatus');
        $leavestatus->setAttrib('readonly', 'true');
		$leavestatus->setAttrib('onfocus', 'this.blur()');
		
		$comments = new Zend_Form_Element_Textarea('comments');
        $comments->setAttrib('readonly', 'true');
		$comments->setAttrib('onfocus', 'this.blur()');
        
		$createddate = new Zend_Form_Element_Text('createddate');
        $createddate->setAttrib('readonly', 'true');
		$createddate->setAttrib('onfocus', 'this.blur()');

        $submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Apply');
		
		$url = "'leaverequest/saveleaverequestdetails/format/json'";
		$dialogMsg = "''";
		$toggleDivId = "''";
		$jsFunction = "''";
		 

		 $submit->setOptions(array('onclick' => "saveDetails($url,$dialogMsg,$toggleDivId,$jsFunction);"
		));

		$this->addElements(array($id,$reason,$availableleaves,$repmanagerid,$comments,$leaveday,$from_date,$to_date,$leavetypeid,$issatholiday,$appliedleavesdaycount,$leavestatus,$createddate,$submit,$unapprovedleavesdaycount));
        $this->setElementDecorators(array('ViewHelper'));
        $this->setElementDecorators(array(
                    'UiWidgetElement',
        ),array('from_date','to_date')
        );   		 
	}
}