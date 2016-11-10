<?php

class Default_Model_Servicedeskdepartment extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_sd_depts';
    protected $_primary = 'id';
	
	public function getServiceDeskDepartmentData($sort, $by, $pageNo, $perPage,$searchQuery)
	{
		$where = "sd.isactive = 1";
		if($searchQuery)
			$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$servicedeskDepartmentData = $this->select()
    					   ->setIntegrityCheck(false)	
                           ->from(array('sd'=>'main_sd_depts'),array('sd.*'))
                           ->where($where)
    					   ->order("$by $sort") 
    					   ->limitPage($pageNo, $perPage);
		
        
		return $servicedeskDepartmentData;       		
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
			
		$objName = 'servicedeskdepartment';
		
		$tableFields = array('action'=>'Action','service_desk_name' => 'Category','description' => 'Description');
		
		$tablecontent = $this->getServiceDeskDepartmentData($sort, $by, $pageNo, $perPage,$searchQuery);     
		
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
			'add' =>'add',
			'call'=>$call,
			'dashboardcall'=>$dashboardcall,
		);
		return $dataTmp;
	}
	
	public function getServiceDeskDepartmentDatabyID($id)
	{
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('sd'=>'main_sd_depts'),array('sd.*'))
					    ->where('sd.isactive = 1 AND sd.id='.$id.' ');
		return $this->fetchAll($select)->toArray();
	
	}
	
	public function getSDDepartmentData()
	{
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('sd'=>'main_sd_depts'),array('sd.*'))
					    ->where('sd.isactive = 1');
		return $this->fetchAll($select)->toArray();
	
	}
	
	public function SaveorUpdateServiceDeskDepartmentData($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_sd_depts');
			return $id;
		}
		
	
	}
    
    /**
     * This function gives request types based on service_desk id.
     * @param integer $service_desk_id  = id of service desk.
     * @return array Array of service request types and their ids.
     */
    public function getRequestsById($service_desk_id)
    {
        $options = array();
        if($service_desk_id != '')
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            $query = "select id,service_request_name from main_sd_reqtypes "
                    . "where isactive = 1 and service_desk_id = ".$service_desk_id." order by service_request_name asc";
            $result = $db->query($query);
            $options = $result->fetchAll();
        }
        return $options;
    }
    
	public function checkDuplicateCategoryName($categoryName)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$qry = "select count(*) as count from main_sd_depts sd where sd.service_desk_name='".$categoryName."' AND sd.isactive=1 ";
		$res = $db->query($qry)->fetchAll();
		return $res;
	}
}