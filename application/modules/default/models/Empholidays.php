<?php

class Default_Model_Empholidays extends Zend_Db_Table_Abstract
{
	protected $_name = 'main_employeeleaves';
    protected $_primary = 'id';
	
	public function getEmpLeavesData($sort, $by, $pageNo, $perPage,$searchQuery,$id)
	{
		$where = " e.user_id = ".$id." AND e.isactive = 1 ";
		if($searchQuery)
			$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$empskillsData = $this->select()
    					   ->setIntegrityCheck(false)	 
						   ->from(array('e' => 'main_employeeleaves'),array('id'=>'e.id','leavelimit'=>'e.emp_leave_limit','used_leaves'=>'e.used_leaves','e.alloted_year'))
						   ->where($where)
    					   ->order("$by $sort") 
    					   ->limitPage($pageNo, $perPage);
	
		return $empskillsData;       		
	}
	
	public function getsingleEmployeeleaveData($id)
	{
		$select = $this->select()
						->setIntegrityCheck(false)
						->from(array('el'=>'main_employeeleaves'),array('el.*'))
						->where('el.user_id='.$id.' AND el.isactive = 1 AND el.alloted_year = year(now())');
		
		return $this->fetchAll($select)->toArray();
	}
	
	public function getsingleEmpleavesrow($id)
	{
		$row = $this->fetchRow("id = '".$id."'");
		if (!$row) {
			throw new Exception("Could not find row $id");
		}
		return $row->toArray();
	}
	
	public function SaveorUpdateEmpLeaves($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_employeeleaves');
			return $id;
		}
		
	}
	
	
}
?>