<?php

class Default_Model_Comments extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_bgcheckcomments';
    protected $_primary = 'id';
	
	public function SaveorUpdateComments($data, $where)
	{
		
		if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_bgcheckcomments');
			return $id;
		}
	}
	
	public function getComments($id,$limitVar='')
	{	
		if($limitVar == '2')
		{
			$commentsData = $this->select()
    					   ->setIntegrityCheck(false)	 
						   ->from(array('b' => 'main_bgcheckcomments'),array('id'=>'b.id','detail_id'=>'bgdet_id','comment'=>'b.comment','from_id'=>'b.from_id','to_id'=>'b.to_id','createddate'=>'b.createddate'))
						   
						   
						   ->where(' b.isactive = 1 AND b.bgdet_id = '.$id)
						   ->order("b.createddate DESC") 
						   ->limitPage(0,2);
		}
		else if($limitVar == 'all')
		{
			$commentsData = $this->select()
    					   ->setIntegrityCheck(false)	 
						   ->from(array('b' => 'main_bgcheckcomments'),array('id'=>'b.id','detail_id'=>'bgdet_id','comment'=>'b.comment','from_id'=>'b.from_id','to_id'=>'b.to_id','createddate'=>'b.createddate'))
						   
						   
						   ->where(' b.isactive = 1 AND b.bgdet_id = '.$id)
						   ->order("b.createddate DESC");						   
		}
		else{
			$commentsData = $this->select()
    					   ->setIntegrityCheck(false)	 
						   ->from(array('b' => 'main_bgcheckcomments'),array('id'=>'b.id','detail_id'=>'bgdet_id','comment'=>'b.comment','from_id'=>'b.from_id','to_id'=>'b.to_id','createddate'=>'b.createddate'))
						   
						   
						   ->where(' b.isactive = 1 AND b.bgdet_id = '.$id)
						   ->order("b.createddate DESC") 
						   ->limitPage(0,100);
		}
		
		return $this->fetchAll($commentsData)->toArray();

	}
	public function getuserNames($userids)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$userData = $db->query("SELECT id,userfullname FROM main_users WHERE id IN (".$userids.")");									
		$result= $userData->fetchAll();
		return $result; 
	}
	
	public function getGMTData($query)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		return $db->query($query)->fetch();
	}
	
}