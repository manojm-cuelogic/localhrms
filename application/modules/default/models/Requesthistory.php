<?php

class Default_Model_Requesthistory extends Zend_Db_Table_Abstract 
{
    protected $_name = 'main_request_history';
    protected $_primary = 'id';
    
    /*
     * This function is used to save/update data in database.
     * @parameters
     * @data  =  array of form data.
     * @where =  where condition in case of update.
     *
     * returns  Primary id when new record inserted,'update' string when a record updated.
     */
    public function SaveorUpdateRhistory($data, $where)
    {    
        if($where != '')
        {
            $this->update($data, $where);
            return 'update';
        } 
        else 
        {
            $this->insert($data);
            $id=$this->getAdapter()->lastInsertId($this->_name);
            return $id;
        }
    }//end of SaveorUpdateRhistory
}//end of Default_Model_Requesthistory class
