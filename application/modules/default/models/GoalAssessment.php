<?php

class Default_Model_GoalAssessment extends Zend_Db_Table_Abstract
{
	protected $_name = 'goalassessments';
	protected $_primary = 'id';

	public function setGoalAssessment($data)
	{
            if($data)
            {
                $this->insert($data);
                $id = $this->getAdapter()->lastInsertId('goalassessments');
                return $id;
            }
	}
        
	 public function getPreviousAssessmentsOfGoal($goalId)
        {
            try
            {
                $db = Zend_Db_Table::getDefaultAdapter();
                $data = $db->query(" 
                            
                                    select * from goalassessments ga 
                    join main_users mu on ga.assessed_by = mu.id
                    join assessmentmeasurementcriteria amc on amc.id = ga.assessment_scheme_id
                    where ga.goal_id  = ".$goalId." order by ga.id desc
                            "
                        );
                $goals = $data->fetchAll();
                
                if(!empty($goals))
                {
                        return $goals;
                }
                return false;
            }
            catch(Exception $e)
            {
                 //no any log method found
                return false;
            }
            
            
        }
        
        public function getAssessmentDetailsForGoal($goalId) 
        {
            return $this->getPreviousAssessmentsOfGoal($goalId);
        }
        
        public function isRightsAvailableForDoingAssessment($ownerId, $goalId, $myUserId = '')
        {
            if($myUserId == '')
            {
                 $auth = Zend_Auth::getInstance();
                if($auth->hasIdentity())
                {
                      $myUserId = $auth->getStorage()->read()->id;
                }
            }
            if(!$myUserId)
            {
                return false;
            }
            if($ownerId == $myUserId)
            { 
                return true;
            }
           
            $users = new Default_Model_Users();
            $defaultUsersRightsForDoingAssessment = $users->getReportingManagers($ownerId);
            $managersIds = [];
            foreach ($defaultUsersRightsForDoingAssessment as $user)
            {
                if($user['manager'])
                {
                    if($user['manager'] == $myUserId)
                    {
                        return true;
                    }
                }
                
            }
            
            $goals = new Default_Model_Goal();
            $goalAssessmentAssignee = $goals->getGoalAssessmentAssignee($goalId);
            foreach ($goalAssessmentAssignee as $assignee)
            {
                if($assignee['userId'])
                {
                    if($assignee['userId'] == $myUserId)
                    {
                        return true;
                    }
                }
                
            }
            
            return false;
        } 

        public function checkRightForAssessmentAddPeople($goalOwnerId, $userId='')
        {
            if(empty($userId))
            {
                $auth = Zend_Auth::getInstance();
                if($auth->hasIdentity())
                {
                    $userId = $auth->getStorage()->read()->id;
                }
            }
            
            $users = new Default_Model_Users();
            $defaultUsersRightsForDoingAssessment = $users->getReportingManagers($goalOwnerId);
            $managersIds = [];
            
            foreach ($defaultUsersRightsForDoingAssessment as $user)
            {
                if($user['manager'])
                {
                    if($user['manager'] == $userId)
                    {
                        return true;
                    }
                }
                
            }
            return false;
        }
        
        public function updateAssessmentRights($goal_id , $data)
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            $qry = "DELETE FROM assessmentrights WHERE goal_id = ".$goal_id;
            $db->query($qry);

            $values = '';
            foreach($data as $saveData)
            {
                $values .= "(".implode(',', $saveData)."),";
            }
            $values = trim($values , ',');
            $qry = "insert into assessmentrights "
                    . "(goal_id, user_id, added_by, added_at) "
                    . "values ".$values;
            $db->query($qry);
           
        }
        
        public function getAssessmentRightsFotGoals($goalIds)
        {
            $goalCount = count($goalIds);
            $goals = implode(',', $goalIds);
            $goals = trim($goals , ',');
            
            $db = Zend_Db_Table::getDefaultAdapter();
            $sql = " 
                            SELECT * , count(user_id) FROM `assessmentrights` WHERE 
                            goal_id in (".$goals.") 
                            group by user_id having count(user_id) >= ".$goalCount."

                        ";
            $data = $db->query($sql);
            $users = $data->fetchAll();

            if(!empty($users))
            {
                    return $users;
            }
            return false;
        }

}