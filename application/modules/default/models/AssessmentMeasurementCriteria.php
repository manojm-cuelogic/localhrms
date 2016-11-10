<?php

class Default_Model_AssessmentMeasurementCriteria extends Zend_Db_Table_Abstract
{
	protected $_name = 'assessmentmeasurementcriteria';
	protected $_primary = 'id';

	public function getAssessmentScoreRangeDetails($goalId = '')
	{
            $db = Zend_Db_Table::getDefaultAdapter();
            $qryStr = 'SELECT amc.min_score, amc.max_score , amc.id FROM  assessmentmeasurementcriteria amc '
                    . 'where id = 1';
            $qry = $db->query($qryStr);
            $res = $qry->fetchAll();
            if(!empty($res))
            {
                    return $res[0];
            }
            return false;
		
	}

}