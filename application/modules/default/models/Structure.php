<?php

class Default_Model_Structure extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_businessunits';
    protected $_primary = 'id';
	
	public function getOrgData()
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$orgData = $db->query("select id,organisationname from main_organisationinfo where isactive = 1;");
		$result= $orgData->fetch();
		return $result;
	}
	
	public function getUnitData()
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$unitData = $db->query("select id,unitname from main_businessunits where isactive = 1 order by unitname asc;");
		$result= $unitData->fetchAll();
		return $result;
	}
	
	public function getDeptData()
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$deptData = $db->query("select id,deptname,unitid from main_departments where isactive = 1 order by deptname asc;");
		$result= $deptData->fetchAll();
		return $result;
	}
	
}