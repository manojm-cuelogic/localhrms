<?php

class Default_Model_Bgscreeningtype extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_bgchecktype';
    protected $_primary = 'id';
	
	public function getScreeningtypeData($sort, $by, $pageNo, $perPage,$searchQuery)
	{
		$where = "b.isactive = 1";
		if($searchQuery)
			$where .= " AND ".$searchQuery;		
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$screeningtypdata = $this->select()
    					   ->setIntegrityCheck(false)	
						   ->from(array('b' => 'main_bgchecktype'))
						   ->where($where)
    					   ->order("$by $sort") 
    					   ->limitPage($pageNo, $perPage);
		
		return $screeningtypdata;       		
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
		$objName = 'bgscreeningtype';
		
		$tableFields = array('action'=>'Action','type' => 'Screening Type','description' => 'Description');
		
		$tablecontent = $this->getScreeningtypeData($sort, $by, $pageNo, $perPage,$searchQuery);     
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
			'dashboardcall'=>$dashboardcall,
			'searchArray' => $searchArray,
			'add' =>'add',
			'call' => $call
		);	
		return $dataTmp;
	}
	
	public function getSingleScreeningtypeData($id)
	{
		
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select * from main_bgchecktype where id = ".$id." and isactive = 1;";
		 return $result = $db->query($query)->fetch();	    
	}
	
	public function SaveorUpdateScreeningtype($data, $where)
	{
		if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_bgchecktype');
			return $id;
		}
	}
	
	public function checktypeduplicates($type,$id)
	{
		if($id) $row = $this->fetchRow("type = '".$type."' AND isactive = 1 AND id <> ".$id);
		else	$row = $this->fetchRow("type = '".$type."' AND isactive = 1 ");
		if(!$row){
			return false;
		}else{
		return true;
		}
	}
	
	public function checkagencyfortype($id)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select count(*) as count from main_bgagencylist where FIND_IN_SET(".$id.",bg_checktype) and isactive = 1;";
		$result = $db->query($query)->fetch();
	    return $result['count'];
	}
}