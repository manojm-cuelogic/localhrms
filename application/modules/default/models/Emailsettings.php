<?php

class Default_Model_Emailsettings extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_mail_settings';
    protected $_primary = 'id';
	
	public function getAccountClassTypeData($sort, $by, $pageNo, $perPage,$searchQuery)
	{
		$where = "isactive = 1";
		
		if($searchQuery)
			$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$accountclasstypeData = $this->select()
    					   ->setIntegrityCheck(false)	    					
						   ->where($where)
    					   ->order("$by $sort") 
    					   ->limitPage($pageNo, $perPage);
		
		return $accountclasstypeData;       		
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
		$objName = 'accountclasstype';
		
		$tableFields = array('action'=>'Action','accountclasstype' => 'Account Class Type','description' => 'Description');
		$tablecontent = $this->getAccountClassTypeData($sort, $by, $pageNo, $perPage,$searchQuery);     
		
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
	
	
	
	public function getsingleAccountClassTypeData($id)
	{
	  $accountclassTypeData = $this->select()
    					   ->setIntegrityCheck(false)	
						   ->from(array('ac'=>'main_accountclasstype'),array('ac.id','ac.accountclasstype'))
                           ->where('ac.id= '.$id.' AND ac.isactive = 1');
      return $this->fetchAll($accountclassTypeData)->toArray();
	}
	
	public function getAccountClassTypeList()
	{
	  $accountclassTypeData = $this->select()
    					   ->setIntegrityCheck(false)	
						   ->from(array('ac'=>'main_accountclasstype'),array('ac.id','ac.accountclasstype'))
                           ->where('ac.isactive = 1')
						   ->order('ac.accountclasstype');
						   
      return $this->fetchAll($accountclassTypeData)->toArray();
	}
	
	public function SaveorUpdateAccountClassTypeData($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_accountclasstype');
			return $id;
		}
		
	
	}
}