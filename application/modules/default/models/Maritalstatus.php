<?php

class Default_Model_Maritalstatus extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_maritalstatus';
    protected $_primary = 'id';
	
	public function getMaritalStatusData($sort, $by, $pageNo, $perPage,$searchQuery)
	{
		$where = "isactive = 1";
		
		if($searchQuery)
			$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$maritalstatusData = $this->select()
    					   ->setIntegrityCheck(false)	    					
						   ->where($where)
    					   ->order("$by $sort") 
    					   ->limitPage($pageNo, $perPage);
		
		return $maritalstatusData;       		
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
		$objName = 'maritalstatus';
		
		$tableFields = array('action'=>'Action','maritalcode' => 'Marital Code','maritalstatusname' =>'Marital Status','description' => 'Description');
		$tablecontent = $this->getMaritalStatusData($sort, $by, $pageNo, $perPage,$searchQuery);     
		
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
	
	public function getsingleMaritalstatusData($id)
	{
		
		$db = Zend_Db_Table::getDefaultAdapter();
		$maritalstatusData = $db->query("SELECT * FROM main_maritalstatus WHERE id = ".$id." AND isactive=1");
		$res = $maritalstatusData->fetchAll();
		if (isset($res) && !empty($res)) 
		{	
			return $res;
		}
		else
			return 'norows';
	}
	
	public function SaveorUpdateMaritalStatusData($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_maritalstatus');
			return $id;
		}
		
	
	}
	
	public function getMaritalStatusList()
	{
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('m'=>'main_maritalstatus'),array('m.id','m.maritalstatusname'))
					    ->where('m.isactive = 1')
						->order('m.maritalstatusname');
		return $this->fetchAll($select)->toArray();
	
	}
}