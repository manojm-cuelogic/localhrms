<?php

class Default_Model_Hrwizard extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_hr_wizard';
    protected $_primary = 'id';
	
    
	
	public function getHrwizardData()
	{
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('w'=>'main_hr_wizard'),array('w.*'));
		return $this->fetchRow($select)->toArray();
	
	}

	public function SaveorUpdateHrWizardData($data, $where)
	{ 
			$this->update($data, $where);
			return 'update';
	
	}
	
	
	
	
	
}