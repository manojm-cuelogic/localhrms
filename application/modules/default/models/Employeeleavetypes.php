<?php

class Default_Model_Employeeleavetypes extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_employeeleavetypes';
    protected $_primary = 'id';
	
	public function getEmployeeLeaveData($sort, $by, $pageNo, $perPage,$searchQuery)
	{
		$where = "e.isactive = 1";
		
		if($searchQuery)
			$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$employeeLeaveData = $this->select()
    					   ->setIntegrityCheck(false)	
                           ->from(array('e'=>'main_employeeleavetypes'),array('e.id','e.numberofdays','e.isactive','e.leavetype','e.leavecode','leavepreallocated'=>'if(e.leavepreallocated=1,"Yes","No")','leavepredeductable'=>'if(e.leavepredeductable=1,"Yes","No")','e.description'))						   
						   ->where($where)
    					   ->order("$by $sort") 
    					   ->limitPage($pageNo, $perPage);
		
		return $employeeLeaveData;       		
	}
	public function getsingleEmployeeLeavetypeData($id)
	{
		
		$db = Zend_Db_Table::getDefaultAdapter();
		$leaveData = $db->query("SELECT * FROM main_employeeleavetypes WHERE id = ".$id." AND isactive=1");
		$res = $leaveData->fetchAll();
		if (isset($res) && !empty($res)) 
		{	
			return $res;
		}
		else
			return 'norows';
	}
	
	public function getLeavetypeDataByID($id)
	{
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('e'=>'main_employeeleavetypes'),array('e.*'))
					    ->where('e.isactive = 1 AND e.id='.$id.' ');
		return $this->fetchAll($select)->toArray();
	
	}
	
	public function SaveorUpdateEmployeeLeaveTypeData($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_employeeleavetypes');
			return $id;
		}

	}
	
	public function getactiveleavetype()
	{
	 	$select = $this->select()
    					   ->setIntegrityCheck(false)	
                           ->from(array('e'=>'main_employeeleavetypes'),array('e.id','e.leavetype','e.numberofdays','e.leavepredeductable'))
						   ->where('e.isactive = 1');  		   					   				
		return $this->fetchAll($select)->toArray();   
	
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
		$objName = 'employeeleavetypes';
		
		$tableFields = array('action'=>'Action','leavetype' => 'Leave Type','numberofdays' => 'Number Of Days','leavecode'=>'Leave Code','leavepreallocated'=>'Is Pre Allocated','leavepredeductable'=>'Is Deductible','description' => 'Description');
		
		$bool_arr = array('' => 'All',1 => 'Yes',2 => 'No');	
		$tablecontent = $this->getEmployeeLeaveData($sort, $by, $pageNo, $perPage,$searchQuery);
		
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
                        'call'=>$call,
                        'search_filters' => array(
                           'leavepreallocated' => array(
                               'type' => 'select',
                               'filter_data' => $bool_arr,
                           ), 
                            'leavepredeductable' => array(
                               'type' => 'select',
                               'filter_data' => $bool_arr,
                           ), 
                        ),
			);			    
				
		return $dataTmp;
	}
	
	public function checkDuplicateLeaveType($leaveTypeName)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$qry = "select count(*) as count from main_employeeleavetypes el where el.leavetype='".$leaveTypeName."' AND el.isactive=1 ";
		$res = $db->query($qry)->fetchAll();
		return $res;
	}
}