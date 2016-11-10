<?php

class Default_Model_Milestone extends Zend_Db_Table_Abstract
{
	protected $_name = 'milestones';
	protected $_primary = 'id';

	
    public function getMilestoneMeasurementCriteria(){

        $select = $this->select()
        ->setIntegrityCheck(false)
        ->from(array('m'=>'milestonemeasurementcriteria'), array('id'=>'m.id', 'name'=> 'm.name'));
        return $this->fetchAll($select)->toArray();
    }
    

    public function getMilestoneMetric($milestoneId)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $query = "SELECT mc.id, mc.name FROM milestones m,  milestonemeasurementcriteria mc WHERE m.measurement_criteria_id = mc.id
             AND m.id = '".$milestoneId."'";
        $qry = $db->query($query);
        $res = $qry->fetchAll();
        return $res;
    }
    
	
    public function getMilestoneById($milestoneId) {
        try
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            $data = $db->query(" 
                        
                                SELECT m.*, mc.name unit,
                                (
                                    select count(ma.id) from milestoneassessment ma WHERE ma.milestone_id = m.id
                                   ) assessment_count
                                   FROM `milestones` m
                                    join milestonemeasurementcriteria mc on mc.id = m.measurement_criteria_id
                                    where  m.id = ".$milestoneId."
                        "
                    );
            $goals = $data->fetchAll();
            
            if(!empty($goals))
            {
                    return $goals[0];
            }
            return false;
        }
        catch(Exception $e)
        {
             //no any log method found
            return false;
        }
            
            
    }
        
	 // public function getGoalMilestones($goalId)
  //       {
  //           $db = Zend_Db_Table::getDefaultAdapter();
  //           $data = $db->query(" 

  //                               SELECT m.*, mc.name unit,
  //                               (
  //                                   select count(ma.id) from milestoneassessment ma WHERE ma.milestone_id = m.id
  //                                  ) assessment_count
  //                                  FROM `milestones` m
  //                                   join milestonemeasurementcriteria mc on mc.id = m.measurement_criteria_id
  //                                   where  m.goal_id = ".$goalId." order by id desc
  //                       "
  //                   );
  //           $milestones = $data->fetchAll();

  //           if(!empty($milestones))
  //           {
  //                   return $milestones;
  //           }
  //           return false;
  //       }
        
    public function getGoalMilestones($goalId)
        {
            try
            {
                $db = Zend_Db_Table::getDefaultAdapter();
                $data = $db->query(" 
                            
                                    SELECT m.*, mc.name unit,
                                    (
                                        select count(ma.id) from milestoneassessment ma WHERE ma.milestone_id = m.id
                                       ) assessment_count
                                       FROM `milestones` m
                                        join milestonemeasurementcriteria mc on mc.id = m.measurement_criteria_id
                                        where  m.goal_id = ".$goalId."
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

}