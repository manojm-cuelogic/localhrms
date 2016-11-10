<?php

require_once 'Zend/Db/Table/Abstract.php';

class Timemanagement_Model_States extends Zend_Db_Table_Abstract
{
	protected $_name = 'tbl_states';
	protected $_primary = 'id';

	/**
	 * 
	 * This action is used to get states based on country id.
	 * @param number $country_id
	 */
	public function getStatesByCountryId($country_id){
		$sql = "SELECT * FROM ".$this->_name." WHERE country_id = :param1 AND isactive = :param2";
		$state_data  = $this->_db->fetchAll($sql,array("param1"=>$country_id,"param2"=>1));
		return $state_data;
	}

}