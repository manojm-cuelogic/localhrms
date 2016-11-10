<?php

class Default_Form_states extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('action',BASE_URL.'states/edit');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'states');


        $id = new Zend_Form_Element_Hidden('id');
		
		$country = new Zend_Form_Element_Select('countryid');
        $country->setAttrib('class', 'selectoption');
        $country->setAttrib('onchange', 'displayParticularState(this,"otheroption","state","")');
        $country->setRegisterInArrayValidator(false);
        $country->addMultiOption('','Select Country');
        $country->setRequired(true);
		$country->addValidator('NotEmpty', false, array('messages' => 'Please select country.')); 
		
		$state = new Zend_Form_Element_Multiselect('state');
		$state->setAttrib('onchange', 'displayStateCode(this)');
        $state->setRegisterInArrayValidator(false);
        $state->setRequired(true);
		$state->addValidator('NotEmpty', false, array('messages' => 'Please select state.'));
		$state->addValidator(new Zend_Validate_Db_NoRecordExists(
	                                            array(  'table'=>'main_states',
	                                                     'field'=>'state_id_org',
	                                                     'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" and isactive=1',    
	
	                                                      ) ) );
		$state->getValidator('Db_NoRecordExists')->setMessage('State already exists.');
		
		$otherstatename = new Zend_Form_Element_Text('otherstatename');
        $otherstatename->setAttrib('maxLength', 20);
       	$otherstatename->addValidators(array(
						 array(
							 'validator'   => 'Regex',
							 'breakChainOnFailure' => true,
							 'options'     => array( 
							 'pattern' =>'/^[^ ][a-zA-Z\s]*$/i',
								 'messages' => array(
										 'regexNotMatch'=>'Please enter valid state name.'
								 )
							 )
						 )
					 ));
		
        $submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');


		 $this->addElements(array($id,$country,$state,$otherstatename,$submit));
         $this->setElementDecorators(array('ViewHelper')); 
	}
}