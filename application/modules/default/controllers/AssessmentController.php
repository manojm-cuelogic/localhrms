<?php
class Default_AssessmentController extends Zend_Controller_Action {

    public function init()
    {
        $this->_options= $this->getInvokeArg('bootstrap')->getOptions();
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
             $this->user_id = $auth->getStorage()->read()->id;
             $this->userProfileImage = $auth->getStorage()->read()->profileimg;
             $this->userfullname = $auth->getStorage()->read()->userfullname;
        }
        $this->goal_assesment = new Default_Model_GoalAssessment();
        $this->milestone_assesment = new Default_Model_MilestoneAssessment();
        $this->assessment_measurement_criteria = new Default_Model_AssessmentMeasurementCriteria();
        $this->goals = new Default_Model_Goal();
        $this->milestone = new Default_Model_Milestone();
        $this->assessment_cycle = new Default_Model_Assessmentcycle();
    }

    public function indexAction()
    {
        //$auth = Zend_Auth::getInstance();
        //$this->view->messages = $this->_helper->flashMessenger->getMessages();
    }
    
    public function getperiodsAction()
    {
        
        $frequencyId = $this->_request->getParam('frequency_id');
        $startDate = $this->_request->getParam('start_date');
        $endDate = $this->_request->getParam('end_date');
        if($frequencyId)
        {
            $data = $this->assessment_cycle->getAssessmentFrequencyById($frequencyId);
           $yrangeStartDate = date(y , strtotime($startDate));
           $yrangeEndDate = date(y , strtotime($endDate));
            $yearRange = $yrangeStartDate . '-' . $yrangeEndDate;
            
            $returnData = '';
            if($data)
            {
                $returnData = '<ul>';
                foreach ($data as $rec)
                {
                    for($i = 1 ; $i <= $rec['frequency'] ; $i++)
                    {
                        $label = str_replace('{n}', $i, $rec['frequency_label']);
                        $label = str_replace('{y}', $yearRange, $label);
                        $returnData .= '<li> <span class="frequency-info-lbl">' . $label  . ' </span>   </li>';
                    }
                }
                $returnData .= '</ul>';
            }
            
            $this->_helper->json(array(
                'status'=>'success',
                'data' => $returnData
            ));
        }
        
    }
    
    public function listassessmentcycleAction()
    { 
        try
        {
           
            if($this->user_id)
            {
                
                $accessmentPeriod = $this->assessment_cycle->getAssessmentPeriod();
                $this->view->accessmentPeriod = $accessmentPeriod;
                
            }
        }
        catch(Exception $e)
        {
            //no any log method found
        }
    }
    public function editassessmentcycleAction()
    { 
        try
        {
           $id = $this->_request->getParam('id');
            if($this->user_id)
            {
                
                $accessmentPeriod = $this->assessment_cycle->getAssessmentPeriodById($id);
                $accessmentCycles = $this->assessment_cycle->getAssessmentCyclesByPeriodId($id);
                $accessmentFrequency = $this->assessment_cycle->getAssessmentFrequency();
                if(isset($_POST['cycle'])) {
                    //echo "<pre>"; print_r($_POST['cycle']); exit;
                    foreach($_POST['cycle'] as $k=>$v) {
                        $this->assessment_cycle->updateAssessmentCycleLabel($k, $v);
                    }
                    $this->_redirect('assessment/listassessmentcycle');
                }
                $this->view->accessmentPeriod = $accessmentPeriod;
                $this->view->accessmentFrequency = $accessmentFrequency;
                $this->view->accessmentCycles = $accessmentCycles;
                //echo "<pre>"; print_r($accessmentCycles); exit;
                
                
            }
        }
        catch(Exception $e)
        {
            //no any log method found
        }
    }
    public function addassessmentcycleAction()
    { 
        try
        {
           
            if($this->user_id)
            {
                
                if($this->getRequest()->getPost())
                {
                    $assessment = $this->_request->getParam('assessment',null);
                    if($assessment && $assessment['frequency'] && $assessment['start_date'] && $assessment['end_date'])
                    {
                        
                        
                        $assessmenrPeriod = $this->assessment_cycle->saveAssessmentPeriod($assessment['frequency'], $assessment['start_date'],   
                            $assessment['end_date'],   
                             $this->user_id   
                        );
                        $assessmentStartDate = $assessment['start_date'];
                        $frequencyData = $this->assessment_cycle->getAssessmentFrequencyById($assessment['frequency']);
                        
                        foreach ($frequencyData as $rec)
                        {
                            for($i = 1 ; $i <= $rec['frequency'] ; $i++)
                            {
                                $assessmentCycle = array();
                                $label = str_replace('{n}', $i, $rec['frequency_label']);
                                $assessmentCycle['assessment_period_id'] = $assessmenrPeriod;
                                $yrangeStartDate = date(y , strtotime($assessmentStartDate));
                                $yrangeEndDate = date(y , strtotime($assessment['end_date']));
                                 $yearRange = $yrangeStartDate . '-' . $yrangeEndDate;
                                $assessmentCycle['display_name'] = str_replace('{y}', $yearRange, $label);
                                $assessmentCycle['start_date'] = date('Y-m-d', strtotime($assessmentStartDate));
                                if($rec['frequency'] == 4)
                                {
                                    $assessmentEndDate = date('Y-m-d', strtotime($assessmentStartDate . ' + 3 months'));
                                }
                                else if($rec['frequency'] == 12)
                                {
                                    $assessmentEndDate = date('Y-m-d', strtotime($assessmentStartDate . ' + 1 month'));
                                }
                                else if($rec['frequency'] == 2)
                                {
                                    $assessmentEndDate = date('Y-m-d', strtotime($assessmentStartDate . ' + 6 months'));
                                }
                                else if($rec['frequency'] == 1)
                                {
                                    $assessmentEndDate = date('Y-m-d', strtotime($assessmentStartDate . ' + 1 year'));
                                }
                                $assessmentCycle['end_date'] = date('Y-m-d', strtotime($assessmentEndDate . ' - 1 day'));
                                
                                $assessmentCycle['creator_user_id'] = $this->user_id;
                                $assessmentCycle['created_at'] = gmdate("Y-m-d H:i:s");
                                $assessmentCycle['modified_at'] = gmdate("Y-m-d H:i:s");
                                $this->assessment_cycle->saveAssessmentCycle($assessmentCycle);
                                
                                //print_r($assessmentCycle);
                                
                                
                                $assessmentStartDate = $assessmentEndDate;
                                
                            }

                        }
                
                        $this->_redirect('assessment/listassessmentcycle/');
                    }
                    
                }
                
                $accessmentPeriod = $this->assessment_cycle->getAssessmentPeriod();
                $accessmentFrequency = $this->assessment_cycle->getAssessmentFrequency();
                $this->view->accessmentPeriod = $accessmentPeriod;
                $this->view->accessmentFrequency = $accessmentFrequency;
                
            }
        }
        catch(Exception $e)
        {
            //no any log method found
        }
    }
    public function goalassessmentAction()
    { 
        try
        {
           
            $goalId = $this->getRequest()->getParam('goal');
            $goal = $this->goals->getGoalByID($goalId);
            
            if($goalId && $this->user_id)
            {
                if($goal)
                {
                    if($this->getRequest()->getPost())
                    {
                        $score = $this->_request->getParam('score',null);
                        $comment = $this->_request->getParam('comment',null);
                        $schemeId = $this->_request->getParam('scheme_id',null);
                        if($comment && $score)
                        {
                            $this->goal_assesment->setGoalAssessment(array(
                                'comment' => $comment,
                                'goal_id' => $goalId,
                                'score' => $score,
                                'assessment_scheme_id' => $schemeId,
                                'created_at' => gmdate("Y-m-d H:i:s"),
                                'updated_at' => gmdate("Y-m-d H:i:s"),
                                'assessed_by' => $this->user_id,
                            ));
                            if($goal['owner_id'] == $this->user_id && $goal['is_self_assessment_done'] == 0)
                            {
                                $this->goals->updateGoalIsAssessment(array(
                                     'id' => $goalId,
                                     'is_self_assessment_done' => 1,
                                ));
                            }
                            else if($goal['is_manager_assessment_done'] == 0)
                            {
                                $this->goals->updateGoalIsAssessment(array(
                                     'id' => $goalId,
                                     'is_manager_assessment_done' => 1,
                                ));
                            }
                            
                            $this->_redirect('assessment/assessmentdetail/goal/'.$goalId );
                        }

                    }
                    $scoreRange = $this->assessment_measurement_criteria->getAssessmentScoreRangeDetails($goalId);
                    $previousAssessments = $this->goal_assesment->getPreviousAssessmentsOfGoal($goalId);
                    $this->view->goal = $goal;
                    $this->view->score_range = $scoreRange;
                    $this->view->previous_assessments = $previousAssessments;
                    $this->view->goalProgress = $this->goals->getGoalProgress($goalId);
                }
            }
        }
        catch(Exception $e)
        {
            //no any log method found
        }
    }
    public function milestoneassessmentAction()
    {
        try
        {
            $goalId = $this->getRequest()->getParam('goal');
            $milestoneId = $this->getRequest()->getParam('milestone');
            $goal = $this->goals->getGoalByID($goalId);
            if($goalId &&  $milestoneId && $this->user_id)
            {

                $goalData = $this->goals->getGoalNMilestoneDetailsForGoal($goalId);
                $isRightsAvailableForDoingAssessment = $this->goal_assesment->isRightsAvailableForDoingAssessment($goalData['goal']['owner_id'] , $goalId , $this->user_id);
                if($goalData)
                {
                    $this->view->isRightsAvailableForDoingAssessment = $isRightsAvailableForDoingAssessment;
                }
                
               
                $milestone = $this->milestone->getMilestoneById($milestoneId);
                if($goal && $milestone)
                {
                    if($this->getRequest()->getPost())
                    {
                        $score = $this->_request->getParam('score',null);
                        $comment = $this->_request->getParam('comment',null);
                        $schemeId = $this->_request->getParam('scheme_id',null);
                        if($comment && $score)
                        {
                            $now = new \DateTime();
                            $currentDate = $now->format("Y-m-d h:i:s"); 

                            $this->milestone_assesment->setMilestoneAssessment(array(
                                'comment' => $comment,
                                'goal_id' => $goalId,
                                'milestone_id' => $milestoneId,
                                'score' => $score,
                                'assessment_scheme_id' => $schemeId,
                                'created_at' => $currentDate,
                                'updated_at' => $currentDate,
                                'assessed_by' => $this->user_id,
                            ));

                            if($goal['owner_id'] == $this->user_id && $goal['is_self_assessment_done'] == 0)
                            {
                                $this->goals->updateGoalIsAssessment(array(
                                     'id' => $goalId,
                                     'is_self_assessment_done' => 1,
                                ));
                            }
                            else if($goal['is_manager_assessment_done'] == 0)
                            {
                                $this->goals->updateGoalIsAssessment(array(
                                     'id' => $goalId,
                                     'is_manager_assessment_done' => 1,
                                ));
                            }
                             $this->_redirect('assessment/assessmentdetail/goal/'.$goalId );
                        }
                    }
                
                    $scoreRange = $this->assessment_measurement_criteria->getAssessmentScoreRangeDetails($goalId);
                    $previousAssessments = $this->milestone_assesment->getPreviousAssessmentsOfMilestone($milestoneId);
                    $this->view->goal = $goal;
                    $this->view->milestone = $milestone;
                    $this->view->score_range = $scoreRange;
                    $this->view->previous_assessments = $previousAssessments;
                }
            }
        }
        catch(Exception $e)
        {
             //no any log method found
        }
        
    }
    
    public function assessmentdetailAction()
    {
        $goalId = $this->getRequest()->getParam('goal');
        if($goalId  && $this->user_id)
        {
            $goalData = $this->goals->getGoalNMilestoneDetailsForGoal($goalId);
            $isRightsAvailableForDoingAssessment = $this->goal_assesment->isRightsAvailableForDoingAssessment($goalData['goal']['owner_id'] , $goalId , $this->user_id);
            if($goalData)
            {
                $this->view->goal = $goalData['goal'];
                $this->view->milestones = $goalData['milestone'];
                $this->view->isRightsAvailableForDoingAssessment = $isRightsAvailableForDoingAssessment;
            }
        }
    }
    
    public function getassessmentdetaildataAction()
    {
        
        $id = $this->_request->getParam('id');
        $type = $this->_request->getParam('type');
        $data = array();
        if($id && $type  && $this->user_id)
        {
            if($type == 'milestone')
            {
                $data = $this->milestone_assesment->getAssessmentDetailsForMilestone($id);
            }
            else if($type == 'goal')
            {
                $data = $this->goal_assesment->getAssessmentDetailsForGoal($id);
            }
            $returnData = [];
            if($data)
            {
                foreach ($data as $rec)
                {
                    $rec['updated_at'] = sapp_Helper::time_diff_string($rec['updated_at']);
                    $returnData[] = $rec;
                }
            }
            $this->_helper->json(array(
                'status'=>'success',
                'data' => $returnData,
                'type'=> $type,
            ));

        }
        
    }
    public function getselectedpeopleAction()
    {
        $this->_helper->layout->disableLayout();
        $users = new Default_Model_Users();
        $goalIds = $this->_request->getParam('goal_ids');
        $data = array();
        if($goalIds)
        {
            $defaultUsersRightsForDoingAssessment = $users->getReportingManagers($this->user_id);
            $managersIds = [];
            foreach ($defaultUsersRightsForDoingAssessment as $user)
            {
                if($user['manager'])
                $managersIds[] = $user['manager'];
            }

            $activeUsersExcludingIds = $managersIds;
            $activeUsersExcludingIds[] = $this->user_id;
            $activeUsers = $users->getUsersNotInIdsArray($activeUsersExcludingIds);
        
            $selectedUsersData = $this->goal_assesment->getAssessmentRightsFotGoals($goalIds);
            
            $selectedUsers = [];
            foreach ($selectedUsersData as $user)
            {
                $selectedUsers[] = $user['user_id'];
            }
            $this->view->activeUsers = $activeUsers;
            $this->view->selectedUsers = $selectedUsers;
        }
    }
    
    public function addpeopleAction()
    {
        $users = new Default_Model_Users();
        $goalId = $this->getRequest()->getParam('goal');
        
        $goal = $this->goals->getGoalByID($goalId);
        $goalOwnerId = $goal['owner_id'];

        $isAddPeopleRightAvailable = $this->goal_assesment->checkRightForAssessmentAddPeople($goalOwnerId);
        if($this->getRequest()->getPost())
        {
            $assessmentPeoples = $this->_request->getParam('peoples',null);
            $assessmentGoals = $this->_request->getParam('goals',null);
            
            if($assessmentPeoples && $assessmentGoals)
            {
                
                foreach ($assessmentGoals as $goal)
                {
                    $saveData = array();
                    foreach($assessmentPeoples as $people)
                    {
                        $saveData[] = array( $goal , $people , $this->user_id ,'now()');
                    }
                    
                    $this->goal_assesment->updateAssessmentRights($goal , $saveData);
                }
                
            }
        }
                
        $defaultUsersRightsForDoingAssessment = $users->getReportingManagers($this->user_id);
        $managersIds = [];
        foreach ($defaultUsersRightsForDoingAssessment as $user)
        {
            if($user['manager'])
            $managersIds[] = $user['manager'];
        }
        $defaultUsersRightsForDoingAssessmentData = $users->getUserNameImageByIds($managersIds);
        $activeUsersExcludingIds = $managersIds;
        $activeUsersExcludingIds[] = $this->user_id;
        $activeUsers = $users->getUsersNotInIdsArray($activeUsersExcludingIds);
        $this->view->defaultUsersRightsForDoingAssessment = $defaultUsersRightsForDoingAssessmentData;
        $loginProfileImgSrc = MEDIA_PATH.'images/default-profile-pic.jpg';
        if (!empty($this->userProfileImage) && file_exists("public/uploads/profile/".$this->userProfileImage)) {
            $loginProfileImgSrc = DOMAIN."public/uploads/profile/".$this->userProfileImage;
        }
        
        //$userGoals = $this->goals->getGoalsForUser($this->user_id);
        $userGoals = $this->goals->getGoalsForUser($goalOwnerId);
    
        $this->view->isAddPeopleRightAvailable = $isAddPeopleRightAvailable;
        $this->view->userGoals = $userGoals;
        $this->view->userProfileImage = $loginProfileImgSrc;
        $this->view->activeUsers = $activeUsers;
        $this->view->userfullname = $this->userfullname;
       
    }

    
}