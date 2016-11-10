<?php

class Default_Model_Militaryservice extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_militaryservice';
    protected $_primary = 'id';
	
	public function getMilitaryServiceData($sort, $by, $pageNo, $perPage,$searchQuery)
	{
		$where = "isactive = 1";
		
		if($searchQuery)
			$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$militaryserviceData = $this->select()
    					   ->setIntegrityCheck(false)	    					
						   ->where($where)
    					   ->order("$by $sort") 
    					   ->limitPage($pageNo, $perPage);
		
		return $militaryserviceData;       		
	}
	
	public function getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$a='',$b='',$c='',$d='')
	{		
        $searchQuery = '';
        $searchArray = array();
        $data = array();
		
		if($searchData != '' && $searchData!='undefined')
			{
				$searchValues = json_decode($searchData);
				foreach($searchValues as $key => $val)
				{
					$searchQuery .= " ".$key." like '%".$val."%' AND ";
					$searchArray[$key] = $val;
				}
				$searchQuery = rtrim($searchQuery," AND");					
			}
			
		$objName = 'militaryservice';
		$tableFields = array('action'=>'Action','militaryservicetype' => 'Military Service Type','description' => 'Description');
		
		$tablecontent = $this->getMilitaryServiceData($sort, $by, $pageNo, $perPage,$searchQuery);     
		
		$dataTmp = array(
			'sort' => $sort,
			'by' => $by,
			'pageNo' => $pageNo,
			'perPage' => $perPage,				
			'tablecontent' => $tablecontent,
			'objectname' => $objName,
			'extra' => array(),
			'tableheader' => $tableFields,
			'jsGridFnName' => 'getAjaxgridData',
			'jsFillFnName' => '',
			'searchArray' => $searchArray,
			'call'=>$call,
			'dashboardcall'=>$dashboardcall
		);	
		return $dataTmp;
	}
	
	public function getsingleMilitaryServiceData($id)
	{
		$row = $this->fetchRow("id = '".$id."'");
		if (!$row) {
			throw new Exception("Could not find row $id");
		}
		return $row->toArray();
	}
	
	public function getMilitaryServiceDataByID($id)
	{
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('m'=>'main_militaryservice'),array('m.*'))
					    ->where('m.isactive = 1 AND m.id='.$id.' ');
		return $this->fetchAll($select)->toArray();
	
	}
	
	public function getTotalMilitaryServiceData()
	{
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('m'=>'main_militaryservice'),array('m.*'))
					    ->where('m.isactive = 1')
						->order('m.militaryservicetype');
		
		return $this->fetchAll($select)->toArray();
	
	}
	
	public function SaveorUpdateMilitaryServiceData($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_militaryservice');
			return $id;
		}
		
	
	}
}