<?php

class Default_Model_MilestoneAssessment extends Zend_Db_Table_Abstract
{
	protected $_name = 'milestoneassessment';
	protected $_primary = 'id';

	
	public function setMilestoneAssessment($data)
	{
            if($data)
            {
                $this->insert($data);
                $id = $this->getAdapter()->lastInsertId('milestoneassessment');
                return $id;
            }
	}
        
        public function getPreviousAssessmentsOfMilestone($milestoneId)
        {
            try
            {
                $db = Zend_Db_Table::getDefaultAdapter();
                $data = $db->query(" 
                            
                                    select * from milestoneassessment ma 
                                    join main_users mu on ma.assessed_by = mu.id 
                                    join assessmentmeasurementcriteria amc on amc.id = ma.assessment_scheme_id 
                                    where ma.milestone_id = ".$milestoneId." order by ma.id desc
                            "
                        );
                $milestones = $data->fetchAll();
                
                if(!empty($milestones))
                {
                        return $milestones;
                }
                return false;
            }
            catch(Exception $e)
            {
                 //no any log method found
                return false;
            }
            
            
        }
        
        public function getAssessmentDetailsForMilestone($milestoneId)
        {
            return $this->getPreviousAssessmentsOfMilestone($milestoneId);
        }
        
       

}