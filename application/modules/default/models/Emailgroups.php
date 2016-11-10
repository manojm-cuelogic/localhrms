<?php

class Default_Model_Emailgroups extends Zend_Db_Table_Abstract
{	
    protected $_name = 'main_emailgroups';
    protected $_primary = 'id';
	            
    public function SaveorUpdateEmailgroupsData($data, $where)
    {
        if($where != '')
        {
            $this->update($data, $where);
            return 'update';
        }
        else
        {
            $this->insert($data);
            $id=$this->getAdapter()->lastInsertId('main_emailcontacts');
            return $id;
        }		
    }
    public function getEgroupsOptions()
    {
        
		$data = $this->fetchAll("isactive = 1",'group_name')->toArray();
        $options = array();
        if(count($data)>0)
        {
            foreach($data as $edata)
            {
                $options[$edata['id']] = $edata['group_name'];
            }
        }
        return $options;
    }
}
?>