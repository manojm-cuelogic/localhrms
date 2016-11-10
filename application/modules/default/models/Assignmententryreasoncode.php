<?php

class Default_Model_Assignmententryreasoncode extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_assignmententryreasoncode';
    protected $_primary = 'id';
	
	public function getAssignmentEntryReasonData($sort, $by, $pageNo, $perPage,$searchQuery)
	{
		$where = "isactive = 1";
		
		if($searchQuery)
			$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$assignmentEntryReasonData = $this->select()
    					   ->setIntegrityCheck(false)	    					
						   ->where($where)
    					   ->order("$by $sort") 
    					   ->limitPage($pageNo, $perPage);
		
		return $assignmentEntryReasonData;       		
	}
	public function getsingleAssignmentEntryReasonData($id)
	{
		$row = $this->fetchRow("id = '".$id."'");
		if (!$row) {
			throw new Exception("Could not find row $id");
		}
		return $row->toArray();
	}
	
	public function SaveorUpdateAssignmentEntryData($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_assignmententryreasoncode');
			return $id;
		}
		
	
	}
}