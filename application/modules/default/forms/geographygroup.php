<?php

class Default_Form_geographygroup extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('action',BASE_URL.'geographygroup/edit');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'geographygroup');


        $id = new Zend_Form_Element_Hidden('id');
		
		$geographygroupname = new Zend_Form_Element_Text('geographygroupname');
        $geographygroupname->setAttrib('maxLength', 50);
		$geographygroupname->addValidators(array(
						 array(
							 'validator'   => 'Regex',
							 'breakChainOnFailure' => true,
							 'options'     => array( 
							 'pattern' =>'/^[a-zA-Z\s]+$/i',
								 'messages' => array(
										 'regexNotMatch'=>'Please enter valid geography group.'
								 )
							 )
						 )
					 ));
					 
		$geographygroupname->addValidator(new Zend_Validate_Db_NoRecordExists(
                                              array('table'=>'main_geographygroup',
                                                        'field'=>'geographygroupname',
                                                      'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" and isactive=1',    
                                                 ) )  
                                    );
        $geographygroupname->getValidator('Db_NoRecordExists')->setMessage('Geography group name already exists.');
		
		
		$geographyregion = new Zend_Form_Element_Text('geographyregion');
        $geographyregion->setAttrib('maxLength', 20);
		$geographyregion->addValidator("regex",true,array(
                           'pattern'=>'/^[a-zA-Z][a-zA-Z0-9\s]*$/', 
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter valid geography region.'
                           )
        	));
		
		$geographycityname = new Zend_Form_Element_Text('geographycityname');
        $geographycityname->setAttrib('maxLength', 20);
		$geographycityname->addValidators(array(
						 array(
							 'validator'   => 'Regex',
							 'breakChainOnFailure' => true,
							 'options'     => array( 
							 'pattern' =>'/^[a-zA-Z\s]+$/i',
								 'messages' => array(
										 'regexNotMatch'=>'Please enter valid geography city.'
								 )
							 )
						 )
					 ));
		
		$defaultGeographyGroup = new Zend_Form_Element_Text('defaultGeographyGroup');
        $defaultGeographyGroup->setAttrib('maxLength', 20);
		$defaultGeographyGroup->addValidator("regex",true,array(
                           'pattern'=>'/^[a-zA-Z][a-zA-Z0-9\s]*$/', 
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter valid default geography group.'
                           )
        	));
       
		$geographycode = new Zend_Form_Element_Text('geographycode');
        $geographycode->setAttrib('maxLength', 20);
        $geographycode->setRequired(true);
        $geographycode->addValidator('NotEmpty', false, array('messages' => 'Please enter geography code.'));
        $geographycode->addValidator("regex",true,array(
                           'pattern'=>'/^[a-zA-Z][a-zA-Z0-9\s]*$/', 
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter valid geography code.'
                           )
        	));	
        $geographycode->addValidator(new Zend_Validate_Db_NoRecordExists(
                                              array('table'=>'main_geographygroup',
                                                        'field'=>'geographycode',
                                                      'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" and isactive=1',    
                                                 ) )  
                                    );
        $geographycode->getValidator('Db_NoRecordExists')->setMessage('Geography code already exists.');			

	
        $currency = new Zend_Form_Element_Select('currency');
        $currency->setAttrib('class', 'selectoption');
        $currency->setRegisterInArrayValidator(false);
        $currency->addMultiOption('','Select Currency');
			$currencymodel = new Default_Model_Currency();
			$currencymodeldata = $currencymodel->getCurrencyList();
				foreach ($currencymodeldata as $currencyres){
					$currency->addMultiOption($currencyres['id'],utf8_encode($currencyres['currency']));
				}
        $currency->setRequired(true);
		$currency->addValidator('NotEmpty', false, array('messages' => 'Please select currency.')); 		
       
        $submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');

		 $this->addElements(array($id,$geographygroupname,$geographyregion,$geographycityname,$defaultGeographyGroup,$geographycode,$currency,$submit));
         $this->setElementDecorators(array('ViewHelper')); 
	}
}