<?php

class Default_Model_Empreporting extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_emp_reporting';
    protected $_primary = 'id';
    
    
    /**
     * This function is used to get reporting managers for drop down.
     * 
     * @return Array Array of reporting managers id's and names.
     */
    public function getReportingManagersOptions()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $query = " select distinct er.reporting_manager_id id,e.emp_name name 
                   from main_emp_reporting er inner join main_employees e 
                   on e.id = er.reporting_manager_id and e.isactive = 1
                   where er.isactive = 1";
        $result = $db->query($query);
        $emp_options = array();
        while($row = $result->fetch())
        {
            $emp_options[$row['id']] = $row['name'];
        }
        return $emp_options;
    }
    
}//end of class