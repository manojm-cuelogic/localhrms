<?php

class Default_Model_Gender extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_gender';
    protected $_primary = 'id';
	
	public function getGenderData($sort, $by, $pageNo, $perPage,$searchQuery)
	{
		$where = "isactive = 1";
		
		if($searchQuery)
			$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$genderData = $this->select()
    					   ->setIntegrityCheck(false)	    					
						   ->where($where)
    					   ->order("$by $sort") 
    					   ->limitPage($pageNo, $perPage);
		
		return $genderData;       		
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
		$objName = 'gender';
		$tableFields = array('action'=>'Action','gendercode' => 'Gender Code','gendername' =>'Gender','description' => 'Description');
		$tablecontent = $this->getGenderData($sort, $by, $pageNo, $perPage,$searchQuery);     
		
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
	
	public function getsingleGenderData($id)
	{
		$row = $this->fetchRow("id = '".$id."'");
		if (!$row) {
			throw new Exception("Could not find row $id");
		}
		return $row->toArray();
	}
	
	public function getGenderDataByID($id)
	{
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('g'=>'main_gender'),array('g.*'))
					    ->where('g.isactive = 1 AND g.id='.$id.' ');
		return $this->fetchAll($select)->toArray();
	
	}
	
	public function SaveorUpdateGenderData($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_gender');
			return $id;
		}
		
	
	}
	
	public function getGenderList()
	{
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('g'=>'main_gender'),array('g.id','g.gendername'))
					    ->where('g.isactive = 1')
						->order('g.gendername');
		return $this->fetchAll($select)->toArray();
	
	}
}