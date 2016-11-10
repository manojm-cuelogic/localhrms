<?php
require_once 'Zend/Db/Table/Abstract.php';

class Timemanagement_Model_Countries extends Zend_Db_Table_Abstract
{
    protected $_name = 'tbl_countries';
    protected $_primary = 'id';
	
}