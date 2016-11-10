<?php

class Default_Model_Educationlevelcode extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_educationlevelcode';
    protected $_primary = 'id';
	
	public function getEducationLevelCodeData($sort, $by, $pageNo, $perPage,$searchQuery)
	{
		$where = "isactive = 1";
		
		if($searchQuery)
			$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$educationLevelCodeData = $this->select()
    					   ->setIntegrityCheck(false)	    					
						   ->where($where)
    					   ->order("$by $sort") 
    					   ->limitPage($pageNo, $perPage);
		
		return $educationLevelCodeData;       		
	}
	public function getsingleEducationLevelCodeData($id)
	{
		
		if($id == "") {
			return 'norows';
		}
		$db = Zend_Db_Table::getDefaultAdapter();
		$eduLevelData = $db->query("SELECT * FROM main_educationlevelcode WHERE id = ".$id." AND isactive=1");
		$res = $eduLevelData->fetchAll();
		if (isset($res) && !empty($res)) 
		{	
			return $res;
		}
		else
			return 'norows';
		
	}
	
	public function SaveorUpdateEducationlevelData($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_educationlevelcode');
			return $id;
		}
		
	
	}
	
	public function getEducationlevelData()
	{
	  $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('ed'=>'main_educationlevelcode'),array('ed.id','ed.educationlevelcode'))
					    ->where('ed.isactive = 1')
						->order('ed.educationlevelcode');
		return $this->fetchAll($select)->toArray();
	
	}
	public function getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$exParam1='',$exParam2='',$exParam3='',$exParam4='')
	{		
        $searchQuery = '';$tablecontent = '';  $searchArray = array();$data = array();$id='';
        $dataTmp = array();
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

		/** search from grid - END **/
		$objName = 'educationlevelcode';
		
		$tableFields = array('action'=>'Action','educationlevelcode' => 'Education Level','description' => 'Description');
				
		$tablecontent = $this->getEducationLevelCodeData($sort, $by, $pageNo, $perPage,$searchQuery);      
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
			'call'=>$call,'dashboardcall'=>$dashboardcall
		);		
			
		return $dataTmp;
	}
}