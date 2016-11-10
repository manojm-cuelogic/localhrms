<?php

class Default_Model_Wizard extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_wizard';
    protected $_primary = 'id';
	
    
	
	public function getWizardData()
	{
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('w'=>'main_wizard'),array('w.*'));
		return $this->fetchRow($select)->toArray();
	
	}

	public function SaveorUpdateWizardData($data, $where)
	{ 
			$this->update($data, $where);
			return 'update';
	
	}
	
	
	
	
	
}