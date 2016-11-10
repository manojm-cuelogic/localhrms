<?php

class Default_Model_Ethniccode extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_ethniccode';
    protected $_primary = 'id';
	
	public function getEthnicCodeData($sort, $by, $pageNo, $perPage,$searchQuery)
	{
		$where = "isactive = 1";
		
		if($searchQuery)
			$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$ethniccodeData = $this->select()
    					   ->setIntegrityCheck(false)	    					
						   ->where($where)
    					   ->order("$by $sort") 
    					   ->limitPage($pageNo, $perPage);
		
		return $ethniccodeData;       		
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
		$objName = 'ethniccode';
		$tableFields = array('action'=>'Action','ethnicname' =>'Ethnic Code','ethniccode' => 'Ethnic Short Code','description' => 'Description');
		$tablecontent = $this->getEthnicCodeData($sort, $by, $pageNo, $perPage,$searchQuery);     
		
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
                        'dashboardcall' => $dashboardcall,
		);	
		return $dataTmp;
	}
	
	public function getsingleEthnicCodeData($id)
	{
		
		$db = Zend_Db_Table::getDefaultAdapter();
		$ethniccodeData = $db->query("SELECT * FROM main_ethniccode WHERE id = ".$id." AND isactive=1");
		$res = $ethniccodeData->fetchAll();
		if (isset($res) && !empty($res)) 
		{	
			return $res;
		}
		else
			return 'norows';
	}
	
	public function gettotalEthnicCodeData()
	{
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('e'=>'main_ethniccode'),array('e.id','e.ethniccode','e.ethnicname'))
					    ->where('e.isactive = 1')
						->order('e.ethnicname');
		return $this->fetchAll($select)->toArray();
	
	}
	
	public function SaveorUpdateEthnicCodeData($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_ethniccode');
			return $id;
		}
		
	
	}
}