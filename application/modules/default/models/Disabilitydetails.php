<?php

class Default_Model_Disabilitydetails extends Zend_Db_Table_Abstract
{	
    protected $_name = 'main_empdisabilitydetails';
    protected $_primary = 'id';
	
       
    public function getempDisabilitydetails($id=0)
	{  
		$disabilityDetailsArr="";$where = "";
		$db = Zend_Db_Table::getDefaultAdapter();		
		if($id != 0)
		{
			$where = "user_id =".$id;
			$disabilitydetails = $this->select()
									->from(array('d'=>'main_empdisabilitydetails'))
									->where($where);
		
			
			$disabilityDetailsArr = $this->fetchAll($disabilitydetails)->toArray(); 
        }
		return $disabilityDetailsArr;       		
	}
    
    public function SaveorUpdateEmpdisabilityDetails($data, $where)
    {
	    if($where != '')
        {
            $this->update($data, $where);
			return 'update';
        }
        else
        {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_empdisabilitydetails');
			return $id;
		}
		
	
	}
	
	
}