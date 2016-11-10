<?php

class Default_Model_Appraisalhistory extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_pa_appraisalhistory';
    protected $_primary = 'id';
	
	public function SaveorUpdateAppraisalHistoryData($data)
	{
		$this->insert($data);
		$id=$this->getAdapter()->lastInsertId('main_pa_appraisalhistory');
		return $id;
	}

	//function to get the appraisal history for a particular employee
	public function getEmpAppraisalHistory($employee_id)
	{
		$result = array();
		if(is_numeric($employee_id))
		{
			$sql_str = "select a.employee_id,group_concat(a.pa_initialization_id) as init_ids,c.from_year,c.to_year,format(COALESCE(avg(a.consolidated_rating),0),2) as consolidated_rating,case appraisal_mode when 'Quarterly' then 'Q1, Q2, Q3, Q4' when 'Half-yearly' then 'H1, H2' when 'Yearly' then 'Yearly' end as app_mode,group_concat(c.appraisal_period) as app_period    
			from main_pa_employee_ratings a 
			inner join main_employees_summary b on a.employee_id = b.user_id 
			inner join main_pa_initialization c on a.pa_initialization_id=c.id 
			where a.employee_id = $employee_id and a.isactive = 1 and a.appraisal_status = 'Completed' and c.status=2
			group by c.from_year,c.to_year
			order by c.from_year desc";
			$db = Zend_Db_Table::getDefaultAdapter();
            $result = $db->query($sql_str)->fetchAll();
		}
		return $result;
	}
}