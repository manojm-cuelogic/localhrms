<?php
/**
 *
 * @model Projecttasks Model
 *
 */
class Timemanagement_Model_Projecttaskresources extends Zend_Db_Table_Abstract
{
	/**
	 * The default table name
	 */
	protected $_name = 'tm_project_task_employees';
	protected $_primary = 'id';

	public function SaveorUpdateProjectTaskResourceData($data, $where)
	{
		if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId($this->_name);
			return $id;
		}
	}

	//function to insert tasks
	public function assignTasks($task_id,$projectId,$employeeId,$ProjectTaskId,$for_update=0)
	{
		$auth = Zend_Auth::getInstance();
		$loginUserId=0;
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$dataArray = array( 'project_id'=>trim($projectId),
				               'task_id'=>trim($task_id), 
							   'project_task_id'=> $ProjectTaskId,
							   'emp_id'=>$employeeId, 
							   'created_by'=>$loginUserId, 
			     	           'is_active' => 1,
				   			   'created'=>gmdate("Y-m-d H:i:s")
		);
		$where='';
		if($for_update>0)
		{
			$dataArray = array('is_active'=>0,'modified'=>gmdate("Y-m-d H:i:s"),'modified_by'=>$loginUserId);
			$where = array('id=?'=>$ProjectTaskId);//here $ProjectTaskId is primary key in tm_project_task_employees table.
		}
		$id = $this->SaveorUpdateProjectTaskResourceData($dataArray,$where);
		return $id;
	}
	//function to check is task is assigned to resource previously
	public function isTaskAssigned($projectId,$taskId,$employeeId)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select count(*) as count from tm_project_task_employees where project_id = ".$projectId." AND task_id=".$taskId." AND emp_id=".$employeeId." AND is_active = 1";
		$result = $db->query($query)->fetch();
		return $result['count'];
	}
	//function to get count of project tasks
	public function getAssignedTaskCount($taskId,$projectId)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$is_default = "select is_default from tm_tasks where id = ".$taskId;
		$is_default_task = $db->query($is_default)->fetch();
		if($is_default_task['is_default']==0){
			$query = "select count(*) as count from tm_project_tasks where task_id = ".$taskId." AND task_id=".$taskId." AND project_id!=".$projectId." AND is_active = 1";
			$result = $db->query($query)->fetch();
			return $result['count'];
		}else
		{
			return 1;
		}
	}

}