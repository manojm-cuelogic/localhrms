<?php

class Default_Model_Payfrequency extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_payfrequency';
    protected $_primary = 'id';
	
	public function getPayfrequencyData($sort, $by, $pageNo, $perPage,$searchQuery)
	{
		$where = "isactive = 1";
		
		if($searchQuery)
			$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$PayfrequencyData = $this->select()
    					   ->setIntegrityCheck(false)	    					
						   ->where($where)
    					   ->order("$by $sort") 
    					   ->limitPage($pageNo, $perPage);
		
		return $PayfrequencyData;       		
	}
	public function getsinglePayfrequencyData($id)
	{
		
		$db = Zend_Db_Table::getDefaultAdapter();
		$payFreqData = $db->query("SELECT * FROM main_payfrequency WHERE id = ".$id." AND isactive=1");
		$res = $payFreqData->fetchAll();
		if (isset($res) && !empty($res)) 
		{	
			return $res;
		}
		else
			return 'norows';
	}
	
	public function getActivePayFreqData()
	{
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('p'=>'main_payfrequency'),array('p.id','p.freqtype'))
					    ->where('p.isactive = 1 ')
						->order('p.freqtype');
		return $this->fetchAll($select)->toArray();
	
	}
	
	public function SaveorUpdatePayFrequencyData($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_payfrequency');
			return $id;
		}
		
	
	}
	public function getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$exParam1='',$exParam2='',$exParam3='',$exParam4='')
	{		
        $searchQuery = '';$tablecontent = '';  $searchArray = array();$data = array(); $dataTmp = array();
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
		$objName = 'payfrequency';
		
		$tableFields = array('action'=>'Action','freqtype' => 'Pay Frequency ','freqcode' => 'Short Code ','freqdescription' =>'Description');
		 
		$tablecontent = $this->getPayfrequencyData($sort, $by,$pageNo,$perPage,$searchQuery);     
		
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
       
}