<?php

class Default_Model_Cronstatus extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_cronstatus';
    protected $_primary = 'id';
	
	
    public function SaveorUpdateCronStatusData($data, $where)
    {
        if($where != ''){
                    $this->update($data, $where);
                    return 'update';
            } else {
                    $this->insert($data);
                    $id=$this->getAdapter()->lastInsertId($this->_name);
                    return $id;
            }


    }
    
    public function getActiveCron($cron_type)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $query = "select count(*) cnt from ".$this->_name." where cron_status = 1 and cron_type = '".$cron_type."'";
        $result = $db->query($query);
        $row = $result->fetch();
        if($row['cnt'] >0)
            $status = "no";
        else 
            $status = "yes";
        return $status;
    }        
}
?>