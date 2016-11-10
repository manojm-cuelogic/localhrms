<?php

class Default_Model_Language extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_language';
    protected $_primary = 'id';
	
	public function getLanguageData($sort, $by, $pageNo, $perPage,$searchQuery)
	{
		$where = "isactive = 1";
		
		if($searchQuery)
			$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$languageData = $this->select()
    					   ->setIntegrityCheck(false)	    					
						   ->where($where)
    					   ->order("$by $sort") 
    					   ->limitPage($pageNo, $perPage);
		
		return $languageData;       		
	}
	public function getsingleLanguageData($id)
	{
		$row = $this->fetchRow("id = '".$id."'");
		if (!$row) {
			throw new Exception("Could not find row $id");
		}
		return $row->toArray();
	}
	
	public function getLanguageDataByID($id)
	{
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('l'=>'main_language'),array('l.*'))
					    ->where('l.isactive = 1 AND l.id='.$id.' ');
		return $this->fetchAll($select)->toArray();
	
	}
	
	public function gettotalLanguageData()
	{
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('l'=>'main_language'),array('l.id','l.languagename'))
					    ->where('l.isactive = 1')
						->order('l.languagename');
		return $this->fetchAll($select)->toArray();
	
	}
	
	public function SaveorUpdateLanguageData($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_language');
			return $id;
		}
		
	
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
		$objName = 'language';
		
		$tableFields = array('action'=>'Action','languagename' => 'Language','description' => 'Description');
		
			
		$tablecontent = $this->getLanguageData($sort, $by, $pageNo, $perPage,$searchQuery); 
		
		   
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