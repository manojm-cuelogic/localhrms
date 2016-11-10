<?php

class Default_Model_Geographygroup extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_geographygroup';
    protected $_primary = 'id';
	
	public function getgeographygroupData($sort, $by, $pageNo, $perPage,$searchQuery)
	{
		$where = "g.isactive = 1 AND c.isactive=1";
		
		if($searchQuery)
			$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$geographygroupData = $this->select()
    					   ->setIntegrityCheck(false)	
						   ->from(array('g'=>'main_geographygroup'),array('g.*'))
                           ->joinLeft(array('c'=>'main_currency'), 'g.currency=c.id',array('currency'=>'concat(c.currencyname," ",c.currencycode)'))						   
						   ->where($where)
    					   ->order("$by $sort") 
    					   ->limitPage($pageNo, $perPage);
		
		return $geographygroupData;       		
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
				    if($key == "currency")
					{
					    $combined = 'concat(c.currencyname," ",c.currencycode)';
						$searchQuery .= " ".$combined." like '%".$val."%' AND ";
					}	
				    else		
					   $searchQuery .= " ".$key." like '%".$val."%' AND ";
					$searchArray[$key] = $val;
				}
				$searchQuery = rtrim($searchQuery," AND");					
			}
		$objName = 'geographygroup';
		
		$tableFields = array('action'=>'Action','geographycode' => 'Geography Code','defaultGeographyGroup' => 'Default Geography Group','geographygroupname' => 'Geography Group','geographyregion' => 'Geography Region','currency' => 'Currency','geographycityname' => 'Geography City');
		
		$tablecontent = $this->getgeographygroupData($sort, $by, $pageNo, $perPage,$searchQuery);     
		
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
	
	public function getsingleGeographygroupData($id)
	{
		$row = $this->fetchRow("id = '".$id."'");
		if (!$row) {
			throw new Exception("Could not find row $id");
		}
		return $row->toArray();
	}
	
	public function getGeographyGroupDataByID($id)
	{
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('g'=>'main_geographygroup'),array('g.*'))
					    ->where('g.isactive = 1 AND g.id='.$id.' ');
		return $this->fetchAll($select)->toArray();
	
	}
	
	public function SaveorUpdateGeographyGroupData($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_geographygroup');
			return $id;
		}
		
	
	}
}