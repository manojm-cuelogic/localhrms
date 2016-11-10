<?php

class Default_Form_cities extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('action',BASE_URL.'cities/edit');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'cities');


        $id = new Zend_Form_Element_Hidden('id');
		
		$country = new Zend_Form_Element_Select('countryid');
        $country->setAttrib('class', 'selectoption');
        $country->setAttrib('onchange', 'displayParticularState(this,"state","state","")');
        $country->setRegisterInArrayValidator(false);
        $country->addMultiOption('','Select Country');
	    $country->setRequired(true);
		$country->addValidator('NotEmpty', false, array('messages' => 'Please select country.'));
		
		$state = new Zend_Form_Element_Select('state');
        $state->setAttrib('class', 'selectoption');
        $state->setAttrib('onchange', 'displayParticularCity(this,"otheroption","city","")');
        $state->setRegisterInArrayValidator(false);
        $state->addMultiOption('','Select State');
			
        $state->setRequired(true);
		$state->addValidator('NotEmpty', false, array('messages' => 'Please select state.')); 
		
		$city = new Zend_Form_Element_Multiselect('city');
        $city->setAttrib('class', 'selectoption');
		$city->setAttrib('onchange', 'displayCityCode(this)');
        $city->setRegisterInArrayValidator(false);
       
        $city->setRequired(true);
		$city->addValidator('NotEmpty', false, array('messages' => 'Please select city.'));
		$city->addValidator(new Zend_Validate_Db_NoRecordExists(
	                                            array(  'table'=>'main_cities',
	                                                     'field'=>'city_org_id',
	                                                     'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" and isactive=1',    
	
	                                                      ) ) );
		$city->getValidator('Db_NoRecordExists')->setMessage('City already exists.');
		
		$othercityname = new Zend_Form_Element_Text('othercityname');
        $othercityname->setAttrib('maxLength', 20);
        
       	$othercityname->addValidators(array(
						 array(
							 'validator'   => 'Regex',
							 'breakChainOnFailure' => true,
							 'options'     => array( 
							 'pattern' =>'/^[^ ][a-zA-Z\s]*$/i',
								 'messages' => array(
										 'regexNotMatch'=>'Please enter valid city name.'
								 )
							 )
						 )
					 ));
		
        $submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');


		 $this->addElements(array($id,$country,$state,$othercityname,$city,$submit));
         $this->setElementDecorators(array('ViewHelper')); 
	}
}