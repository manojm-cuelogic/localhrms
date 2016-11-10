<?php

class Default_Model_Timezone extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_timezone';
    protected $_primary = 'id';
	
	public function getTimezoneData($sort, $by, $pageNo, $perPage,$searchQuery)
	{
		$where = "isactive = 1";
		
		if($searchQuery)
			$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$timeZoneData = $this->select()
							->from(array('b' => 'main_timezone'),array('id'=>'id','isactive'=>'isactive','description'=>'description','timezone'=>'concat(timezone," [",timezone_abbr,"]")'))
    					   ->setIntegrityCheck(false)	    					
						   ->where($where)
    					   ->order("$by $sort") 
    					   ->limitPage($pageNo, $perPage);
		
		return $timeZoneData;       		
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
		$objName = 'timezone';
		$tableFields = array('action'=>'Action','timezone' => 'Time Zone','description' => 'Description');
		
		$tablecontent = $this->getTimezoneData($sort, $by, $pageNo, $perPage,$searchQuery);     
		
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
	
	public function getsingleTimezoneData($id)
	{
		$row = $this->fetchRow("id = '".$id."'");
		if (!$row) {
			throw new Exception("Could not find row $id");
		}
		return $row->toArray();
	}
	
	public function SaveorUpdateTimeZoneData($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_timezone');
			return $id;
		}
		
	
	}
	
	public function getTimeZoneDataByID($id)
	{
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('t'=>'main_timezone'),array('t.*'))
					    ->where('t.isactive = 1 AND t.id='.$id.' ');
		return $this->fetchAll($select)->toArray();
	
	}
	
	public function getTimeZoneList()
	{
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('t'=>'main_timezone'),array('t.id','t.timezone'))
					    ->where('t.isactive = 1');
		return $this->fetchAll($select)->toArray();
	
	}
	
	public function getalltimezones()
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select * from tbl_timezones where id not in (select actual_id from main_timezone where isactive = 1);";
		$result = $db->query($query)->fetchAll();
		return $result;
	}
	
	public function savetimezonedetails($str,$desc,$loginid)
	{	
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "insert into main_timezone(actual_id,timezone,timezone_abbr,offet_value,
				description,createdby,modifiedby,createddate,modifieddate,isactive) 
				select id,timezone,timezone_abbr,offset_value,'".$desc."',".$loginid.",".$loginid.",'".gmdate("Y-m-d H:i:s")."','".gmdate("Y-m-d H:i:s")."',1 from tbl_timezones where id in (".$str.");";
		$result = $db->query($query);
		return $id=$this->getAdapter()->lastInsertId('main_timezone');
	}
}