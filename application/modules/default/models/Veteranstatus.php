<?php

class Default_Model_Veteranstatus extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_veteranstatus';
    protected $_primary = 'id';
	
	public function getVeteranStatusData($sort, $by, $pageNo, $perPage,$searchQuery)
	{
		$where = "isactive = 1";
		/*if($columnkey != '' && $columntext != '')
			$where = " ".$columnkey." like '%".$columntext."%' "; */
		if($searchQuery)
			$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$veteranstatusData = $this->select()
    					   ->setIntegrityCheck(false)	    					
						   ->where($where)
    					   ->order("$by $sort") 
    					   ->limitPage($pageNo, $perPage);
		//echo $dateFormatData->__toString(); 
		return $veteranstatusData;       		
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
			
		$objName = 'veteranstatus';
		$tableFields = array('action'=>'Action','veteranstatus' => 'Veteran Status','description' => 'Description');
		
		$tablecontent = $this->getVeteranStatusData($sort, $by, $pageNo, $perPage,$searchQuery);     
		
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
			'searchArray' => $searchArray,'call'=>$call
		);	
		return $dataTmp;
	}
	
	public function getsingleVeteranStatusData($id)
	{
		$row = $this->fetchRow("id = '".$id."'");
		if (!$row) {
			throw new Exception("Could not find row $id");
		}
		return $row->toArray();
	}
	
	public function getVeteranStatusDataByID($id)
	{
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('v'=>'main_veteranstatus'),array('v.*'))
					    ->where('v.isactive = 1 AND v.id='.$id.' ');
		return $this->fetchAll($select)->toArray();
	
	}
	
	public function getTotalVeteranStatusData()
	{
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('v'=>'main_veteranstatus'),array('v.*'))
					    ->where('v.isactive = 1')
						->order('v.veteranstatus');
		return $this->fetchAll($select)->toArray();
	
	}
	
	public function SaveorUpdateVeteranStatusData($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_veteranstatus');
			return $id;
		}
		
	
	}
}