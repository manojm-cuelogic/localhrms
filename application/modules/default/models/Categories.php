<?php

class Default_Model_Categories extends Zend_Db_Table_Abstract
{
	protected $_name = 'main_pd_categories';
	private $db;

	public function init()
	{
		$this->db = Zend_Db_Table::getDefaultAdapter();
	}
	
	public function addCategory($data)
	{
		if(!empty($data))
		{
			$this->insert($data);
			$id = $this->getAdapter()->lastInsertId($this->_name);
			return $id;
		}
	}

	/**
	** function to retrieve categories
	** @con = 'add' gets active category names 
	** @con = 'cnt' gets active categories count
	** @con = 'grid' gets active categories data
	** @con = 'menu' gets active categories with document count
	**/
	public function getCategories($con,$sort='', $by='', $pageNo='', $perPage='',$searchQuery='')
	{
		try
		{
			if($con == 'add' || $con == 'menu')
			{
				$columns = 'c.category';
			}
			else if($con == 'cnt')
			{
				$columns = 'COUNT(c.id) as cnt';
			}
			else if($con == 'grid')
			{
				$columns = 'c.*';
			}
			
			$where = "c.isactive = 1";
			if($searchQuery)
				$where .= " AND ".$searchQuery;

			if(empty($by) && empty($sort)) 
			{
				$by = 'c.category';
				$sort = 'ASC';
			}

			$res = $this->select()
				->setIntegrityCheck(false)
				->from(array('c' => $this->_name),array('c.id',$columns))
				->where($where)
				->order("$by $sort");

			if($con == 'grid' && !empty($pageNo) && !empty($perPage))
			{
				$this->select()->limitPage($pageNo, $perPage);
			}

			if($con == 'grid')
			{
				return $res;
			}
			else if($con == 'menu')
			{
				$qry = 'select d.category_id,count(d.id) as doccnt  from main_pd_documents d where d.isactive = 1 group by d.category_id'; 					
				$tmpRes = $this->db->query($qry);
				$documentsObj = $tmpRes->fetchAll();
				$categoryObj = $this->fetchAll($res)->toArray();

				return array('res' => $categoryObj, 'docs' => $documentsObj);
			}
			else
			{
				return $this->fetchAll($res)->toArray();
			}
		}
		catch(Exception $e)
		{
			print_r($e);
		}
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
					$searchQuery .= " c.".$key." like '%".$val."%' AND ";
					$searchArray[$key] = $val;
				}
				$searchQuery = rtrim($searchQuery," AND");					
			}
			
		$objName = 'categories';
		
		$tableFields = array('action'=>'Action','category'=>'Category','description' => 'Description');
		
		$tablecontent = $this->getCategories('grid',$sort, $by, $pageNo, $perPage,$searchQuery);     
		
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

	public function getCategoryById($id)
	{
		try
		{
			$where = 'c.id = '.$id.' AND c.isactive = 1';
			$res = $this->select()
					->setIntegrityCheck(false)
					->from(array('c' => $this->_name),array('c.id','c.isused','c.category','c.description'))
					->where($where);

			$tmp = $this->fetchAll($res)->toArray();
			if(!empty($tmp))
			{
				return $tmp = $tmp[0];
			}
		}
		catch(Exception $e)
		{
			//print_r($e);
		}
	}	
	public function editCategory($data,$where)
	{
		if(!empty($data) && !empty($where))
		{
			$this->update($data,$where);
			return true;
		}
		else
			return false;
	}	
}
?>