<?php

class Default_Model_Emppersonaldetails extends Zend_Db_Table_Abstract
{
	protected $_name = 'main_emppersonaldetails';
    protected $_primary = 'id';
	
	public function getsingleEmpPerDetailsData($id)
	{
		$select = $this->select()
						->setIntegrityCheck(false)
						->from(array('ep'=>'main_emppersonaldetails'),array('ep.*'))
						->where('ep.user_id='.$id.' AND ep.isactive = 1');
					
		return $this->fetchAll($select)->toArray();
	}
	
	public function SaveorUpdateEmpPersonalData($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_emppersonaldetails');
			return $id;
		}
		
	}	
	public function getAllProjects() {
		$select = $this->select()
						->setIntegrityCheck(false)
						->from(array('mp' => 'main_projects'))
						->where('mp.isactive = 1');
					
		return $this->fetchAll($select)->toArray();
	}

	public function getMyProjects($id) {
		$select = $this->select()
						->setIntegrityCheck(false)
						->from(array('mep' => 'main_employee_projects'))
						->where("mep.user_id = {$id} and mep.isactive = 1");
					
		return $this->fetchAll($select)->toArray();
	}

	public function addOtherDetails($skype_id, $prev_hr_email) {
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$data = array('skype_id'=>$skype_id,'prev_org_hr_email'=>$prev_hr_email);
		$where = " user_id = $loginUserId";
		$this->update($data, $where);
	}
}