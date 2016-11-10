<?php

class Default_Model_Bankaccounttype extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_bankaccounttype';
    protected $_primary = 'id';
	
	public function getBankaccounttypeData($sort, $by, $pageNo, $perPage,$searchQuery)
	{
		$where = "isactive = 1";
		
		if($searchQuery)
			$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$bankaccounttypeData = $this->select()
    					   ->setIntegrityCheck(false)	    					
						   ->where($where)
    					   ->order("$by $sort") 
    					   ->limitPage($pageNo, $perPage);
		
		return $bankaccounttypeData;       		
	}
	public function getsingleBankAccountData($id)
	{
		
		$db = Zend_Db_Table::getDefaultAdapter();
		$bankAccntData = $db->query("SELECT * FROM main_bankaccounttype WHERE id = ".$id." AND isactive=1	");
		$res = $bankAccntData->fetchAll();
		if (isset($res) && !empty($res)) 
		{	
			return $res;
		}
		else
			return 'norows';
	}
	
	public function getBankAccountList()
	{
	  $accountclassTypeData = $this->select()
    					   ->setIntegrityCheck(false)	
						   ->from(array('b'=>'main_bankaccounttype'),array('b.id','b.bankaccounttype'))
                           ->where('b.isactive = 1')
						   ->order('b.bankaccounttype');
      return $this->fetchAll($accountclassTypeData)->toArray();
	}
	
	public function SaveorUpdateBankAccountData($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_bankaccounttype');
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
		$objName = 'bankaccounttype';
		
		$tableFields = array('action'=>'Action','bankaccounttype' => 'Bank Account Type','description' => 'Description');
		
			
		$tablecontent = $this->getBankaccounttypeData($sort, $by, $pageNo, $perPage,$searchQuery);     
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