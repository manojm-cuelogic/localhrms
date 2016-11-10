<?php

class Default_Model_Timeformat extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_timeformat';
    protected $_primary = 'id';
	
	public function getTimeFormatData($sort, $by, $pageNo, $perPage,$searchQuery)
	{
		$where = "isactive = 1";
		
		if($searchQuery)
			$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$timeFormatData = $this->select()
    					   ->setIntegrityCheck(false)	    					
						   ->where($where)
    					   ->order("$by $sort") 
    					   ->limitPage($pageNo, $perPage);
		
		return $timeFormatData;       		
	}
	public function getsingleTimeformatData($id)
	{
		$row = $this->fetchRow("id = '".$id."'");
		if (!$row) {
			throw new Exception("Could not find row $id");
		}
		return $row->toArray();
	}
	
	public function SaveorUpdateTimeFormatData($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_timeformat');
			return $id;
		}
	}
	
	public function getTimeFormatDataByID($id)
	{
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('t'=>'main_timeformat'),array('t.*'))
					    ->where('t.isactive = 1 AND t.id='.$id.' ');
		return $this->fetchAll($select)->toArray();
	
	}
	
	public function getTimeFormatList()
	{
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('t'=>'main_timeformat'),array('t.id','t.timeformat'))
					    ->where('t.isactive = 1');
		return $this->fetchAll($select)->toArray();
	
	}
}