<?php

class Default_Form_activitylogreport extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('action',BASE_URL.'reports/activitylogreport');
		$this->setAttrib('id', 'activitylog');
		$this->setAttrib('name', 'activitylog');

		$username = new Zend_Form_Element_Text('username');
		
		$username->setAttrib('onblur', 'clearautoactivity(this)');
		$username->addFilter(new Zend_Filter_StringTrim());
		$username->setLabel("User Name");

		$menu = new Zend_Form_Element_Select('menu');
		$menu->setLabel('Menu');
		$menu->setAttrib('onchange', 'changeelement(this)');
		$menuModel = new Default_Model_Menu();
		$menuList = $menuModel->getMenuArrayActivityLogReport();
		$menu->addMultiOption('','Select Menu');
		foreach ($menuList as $menuitem){
			$menu->addMultiOption($menuitem['id'],$menuitem['menuName']);
		}

		$useraction = new Zend_Form_Element_Select('useraction');
		$useraction->setLabel('User Action');
		$useraction->setAttrib('onchange', 'changeelement(this)');
		$useraction->addMultiOption('','Select Action');
		$useraction->addMultiOption('1','Add');
		$useraction->addMultiOption('5','Cancel');
		$useraction->addMultiOption('3','Delete');
		$useraction->addMultiOption('2','Edit');	

		$modifieddate = new ZendX_JQuery_Form_Element_DatePicker('modifieddate');
		$modifieddate->setAttrib('onblur', 'blurelement(this)');
		$modifieddate->setLabel("Modified Date");
		$modifieddate->setAttrib('readonly', 'true');
		$modifieddate->setAttrib('onfocus', 'this.blur()');		
		$modifieddate->setOptions(array('class' => 'brdr_none'));
		
		

		$this->addElements(array($username,$menu,$useraction,$modifieddate));
		$this->setElementDecorators(array('ViewHelper'));
		$this->setElementDecorators(array(
                    'UiWidgetElement',
		),array('modifieddate'));
	}
}