<?php
require_once 'Zend/Db/Table/Abstract.php';
class Timemanagement_Model_Mailslist extends Zend_Db_Table_Abstract
{
	/**
	 * The default table name
	 */
	protected $_name = 'tm_mailing_list';
		
	public function addOrUpdateMailsList($data,$where){
		if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId($this->_name);
			return $id;
		}
	}

	public function getPendingMailsData(){
		$emailData = $this->select()
						->setIntegrityCheck(false)
						->from(array('ml' => $this->_name),array('ml.*'))
						->where("ml.is_mail_sent = 0")
						->order("ml.id");
		return $this->fetchAll($emailData)->toArray();
	}

}
