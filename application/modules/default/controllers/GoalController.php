<?php

class Default_GoalController extends Zend_Controller_Action
{
    public function init(){

            parent::init(); 
            $this->flashMessenger = $this->getHelper('FlashMessenger'); 
            
            $ajaxContext = $this->_helper->getHelper('AjaxContext'); 
            $ajaxContext->addActionContext('allcomments', 'html') 
                ->initContext(); 
        $ajaxContext->addActionContext('allteamandfollowedcomments', 'html') 
                ->initContext();
            
            $this->users = new Default_Model_Users();
            $this->goals = new Default_Model_Goal();
            $auth = Zend_Auth::getInstance();
            if($auth->hasIdentity())
            {
                 $this->user_id = $auth->getStorage()->read()->id;
                 $this->userProfileImage = $auth->getStorage()->read()->profileimg;
                 $this->userfullname = $auth->getStorage()->read()->userfullname;
            }
    } 

    public function addcompanygoalAction(){
        $auth = Zend_Auth::getInstance();

        if($auth->hasIdentity()){
            $loginUserId = $auth->getStorage()->read()->id;
        }

        if($auth->getStorage()->read()->emprole == MANAGEMENTROLE) {
            $goalModel = new Default_Model_Goal();
            $allKeywords = $goalModel->getAllKeywords();
            $keywordsArr = array();
            foreach ($allKeywords as $key => $value) {
                $keywordsArr[] = $value['name'];
            }
            $cycleModel = new Default_Model_Assessmentcycle();
            if($_POST['goal_form']) {
                $form = $_POST['goal_form'];
                $data = array(
                            'name'                  => $form['name'],
                            'descrption'           => $form['description'],
                            'owner_id'              => $loginUserId,
                            'goal_type_id'          => 1,
                            'goal_align_id'         => 0,
                            'start_date'            => date("Y-m-d", strtotime($form['align_start_date'])),
                            'end_date'              => date("Y-m-d", strtotime($form['align_end_date'])),
                            'assessment_cycle_id'   => $form['align_period_id'],
                            'created_by'            => $loginUserId,
                            'updated_by'            => $loginUserId,
                            'tags'                  => $form['align_keyword_id']
                        );
                if(!$g_id = $goalModel->saveData($data)) {
                    throw new Exception("Error occurred");
                }
                //$result = sapp_Helper::sendGoalUpdateNotification($g_id, "goaladded");
                $this->_redirect('goal/mygoals/company/1');
            }
            $this->view->cycles = $cycleModel->getCurrentAssessmentCycle();
            $this->view->allKeywords = $keywordsArr;
            // cycles
        } else {
            $this->view->rowexist = "norows";
        }
    }

    public function editcompanygoalAction(){
        $auth = Zend_Auth::getInstance();
        $goal_id =  $this->getRequest()->getParam('id');
        if($auth->hasIdentity()){
            $loginUserId = $auth->getStorage()->read()->id;
        }
        if($auth->getStorage()->read()->emprole == MANAGEMENTROLE) {
            $goalModel = new Default_Model_Goal();
            $goal = $goalModel->getSingleGoalById($goal_id, false);
            $allKeywords = $goalModel->getAllKeywords();
            $keywordsArr = array();
            foreach ($allKeywords as $key => $value) {
                $keywordsArr[] = $value['name'];
            }
            $cycleModel = new Default_Model_Assessmentcycle();
            $keywords = $goalModel->getAllKeywordsForGoal($goal_id);
            if($_POST['goal_form']) {
                $form = $_POST['goal_form'];
                $data = array(
                            'id'                    => $goal_id,
                            'name'                  => $form['name'],
                            'descrption'            => $form['description'],
                            'owner_id'              => $loginUserId,
                            'goal_type_id'          => 1,
                            'goal_align_id'         => 0,
                            'start_date'            => date("Y-m-d", strtotime($form['align_start_date'])),
                            'end_date'              => date("Y-m-d", strtotime($form['align_end_date'])),
                            'assessment_cycle_id'   => $form['align_period_id'],
                            'updated_by'            => $loginUserId,
                            'tags'                  => $form['align_keyword_id']
                        );
                //echo "<pre>";print_r($form); exit;
                if(!$goalModel->editData($data)) {
                    throw new Exception("Error occurred");
                }
                //$result = sapp_Helper::sendGoalUpdateNotification($goal_id);
                $this->_redirect('goal/mygoals/company/1');
            }
            $this->view->cycles = $cycleModel->getCurrentAssessmentCycle();
            $this->view->allKeywords = $keywordsArr;
            $this->view->keywords = $keywords;
            $this->view->goal = $goal[0];
            // cycles
        } else {
            $this->view->rowexist = "norows";
        }
    }

    public function mygoalsAction(){
        $auth = Zend_Auth::getInstance();

        if($auth->hasIdentity()){
            $loginUserId = $auth->getStorage()->read()->id;
        }
        $isTeam = $this->_getParam('team',0);
        $isCompany = $this->_getParam('company',0);
        $cycleModel = new Default_Model_Assessmentcycle();
        $currentCycle = $cycleModel->getCurrentAssessmentCycle('current');
        $goalModel = new Default_Model_Goal();
        if($isCompany)
            $data = $goalModel->search(array("goal_type_id"=>1));
        else if($isTeam)
            $data = $goalModel->search(array("reporting_manager"=>array($loginUserId)));
        else
            $data = $goalModel->search(array("owner_id"=>array($loginUserId)));

        //echo "<pre>"; print_r($data); exit;
        $this->view->data = $data;
        $this->view->isTeam = $isTeam;
        $this->view->isCompany = $isCompany;
    }

    public function assessgoalslistAction(){
        $auth = Zend_Auth::getInstance();

        if($auth->hasIdentity()){
            $loginUserId = $auth->getStorage()->read()->id;
        }
        $cycleModel = new Default_Model_Assessmentcycle();
        $currentCycle = $cycleModel->getCurrentAssessmentCycle('current');
        $goalModel = new Default_Model_Goal();
        $data = $goalModel->search(array("can_assesst_by"=>$loginUserId));

        //echo "<pre>"; print_r($data); exit;
        $this->view->data = $data;
    }

    public function getgoaldetailsAction() {
        $this->_helper->layout->disableLayout();
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity()){
            $loginUserId = $auth->getStorage()->read()->id;
        }
        $goal_id = $this->_getParam('goal_id',null);
        $goalModel = new Default_Model_Goal();
        $goalData = $goalModel->getGoalNMilestoneDetailsForGoal($goal_id);
        if($goalData && is_array($goalData['goal']) && count($goalData['goal']) > 0)
        {
            $this->view->goalProgress = $goalModel->getGoalProgress($goal_id);
            $this->view->goal = $goalData['goal'];
            $this->view->milestones = $goalData['milestone'];
        }
    }

    public function addAction(){

        $auth = Zend_Auth::getInstance();

        if($auth->hasIdentity()){
            $loginUserId = $auth->getStorage()->read()->id;
        }

        $usersModel = new Default_Model_Users;
        $users = $usersModel->getUsersWithPicture();

        $employeeModel = new Default_Model_Employee();
        $emp_data = $employeeModel->getsingleEmployeeData($loginUserId);
        $team_members = $employeeModel->getEmployeesUnderRM($loginUserId);

        $managers = $usersModel->getUsersNotInIdsArray(array());
        $goalModel = new Default_Model_Goal();
        if(count($managers) > 0) {
            $align_goals = $goalModel->getGoalNamesForOwner($emp_data[0]['reporting_manager']);
        } else {
            $align_goals = $goalModel->getCompanyGoalNames();
        }
        $allKeywords = $goalModel->getAllKeywords();
        $keywordsArr = array();
        foreach ($allKeywords as $key => $value) {
            $keywordsArr[] = $value['name'];
        }
        $cycleModel = new Default_Model_Assessmentcycle();
        $cycles = $cycleModel->getCurrentAssessmentCycle();
        
        
        if($_POST['goal_form']) {
                $form = $_POST['goal_form'];
                $data = array(
                            'name'                  => $form['name'],
                            'descrption'           => $form['description'],
                            'owner_id'              => $form['goal_owner_id'],
                            'goal_type_id'          => 2,
                            'goal_align_id'         => $form['align_goal_id'],
                            'start_date'            => date("Y-m-d", strtotime($form['align_start_date'])),
                            'end_date'              => date("Y-m-d", strtotime($form['align_end_date'])),
                            'assessment_cycle_id'   => $form['align_period_id'],
                            'created_by'            => $loginUserId,
                            'updated_by'            => $loginUserId,
                            'tags'                  => $form['align_keyword_id']
                        );
                $goal = new Default_Model_Goal();
                if(!$g_id = $goal->saveData($data)) {
                    throw new Exception("Error occurred");
                }
                $result = sapp_Helper::sendGoalUpdateNotification($g_id, "goaladded");
                $this->_redirect('goal/view/id/'.$g_id);
        }

        $this->view->align_owners = $users;
        $this->view->cycles = $cycles;
        $this->view->team_members = $team_members;
        $this->view->user = $emp_data[0];
        $this->view->managers = $managers;
        $this->view->default_align_goals = $align_goals;
        $this->view->allKeywords = $keywordsArr;
        // echo"<pre>"; print_r($this->view->user); exit;
    }

    public function editAction(){

        $auth = Zend_Auth::getInstance();
        $goal_id =  $this->getRequest()->getParam('id');
        
        if($auth->hasIdentity()){
            $loginUserId = $auth->getStorage()->read()->id;
        }

        
        
        

        $goalModel = new Default_Model_Goal();
        $goal = $goalModel->getSingleGoalById($goal_id); 
        if($goal) {
            $usersModel = new Default_Model_Users;
            $users = $usersModel->getUsersWithPicture();
            $this->view->isEditable = false;
            if($loginUserId == $goal[0]['owner_id'])
            $this->view->isEditable = true;

            $managers = $usersModel->getUsersNotInIdsArray(array('1'));//$usersModel->getAllParents($loginUserId);
            
            $aligned_goal = $goalModel->getSingleGoalById($goal[0]['goal_align_id']);
            $allKeywords = $goalModel->getAllKeywords();
            $keywordsArr = array();
            foreach ($allKeywords as $key => $value) {
                $keywordsArr[] = $value['name'];
            }
            if($aligned_goal[0]['goal_type_id'] == 2) {
                $align_goals = $goalModel->getGoalNamesForOwner($aligned_goal[0]['owner_id']);
            } else {
                $align_goals = $goalModel->getCompanyGoalNames();
            }
            $keywords = $goalModel->getAllKeywordsForGoal($goal_id);
            $cycleModel = new Default_Model_Assessmentcycle();
            $cycles = $cycleModel->getCurrentAssessmentCycle();
            
            $employeeModel = new Default_Model_Employee();
            $emp_data = $employeeModel->getsingleEmployeeData($loginUserId);
            $team_members = $employeeModel->getEmployeesUnderRM($loginUserId);
            if($_POST['goal_form']) {
                $form = $_POST['goal_form'];
                $data = array(
                            'id'                    => $goal_id,
                            'name'                  => $form['name'],
                            'descrption'            => $form['description'],
                            'owner_id'              => $goal[0]['owner_id'],
                            'goal_type_id'          => 2,
                            'goal_align_id'         => $form['align_goal_id'],
                            'start_date'            => date("Y-m-d", strtotime($form['align_start_date'])),
                            'end_date'              => date("Y-m-d", strtotime($form['align_end_date'])),
                            'assessment_cycle_id'   => $form['align_period_id'],
                            'created_by'            => $loginUserId,
                            'updated_by'            => $loginUserId,
                            'tags'                  => $form['align_keyword_id']
                        );
                //echo "<pre>";print_r($form); exit;
                $goal = new Default_Model_Goal();
                if(!$goal->editData($data)) {
                    throw new Exception("Error occurred");
                }
                $result = sapp_Helper::sendGoalUpdateNotification($goal_id);
                $this->_redirect('goal/view/id/'.$goal_id);
            }
            $this->view->align_owners = $users;
            $this->view->cycles = $cycles;
            $this->view->team_members = $team_members;
            $this->view->user = $emp_data[0];
            $this->view->managers = $managers;
            $this->view->default_align_goals = $align_goals;
            $this->view->goal = $goal[0];
            $this->view->aligned_goal = $aligned_goal[0];
            $this->view->keywords = $keywords;
            //echo "<pre>"; print_r($managers); exit;
            $this->view->allKeywords = $keywordsArr;
        } else {
            $this->view->rowexist = "norows";
        }
        

    }

    public function addgoalcommentAction(){

        $auth = Zend_Auth::getInstance();
        $goals = array();
        if($auth->hasIdentity()){
            $loginUserId = $auth->getStorage()->read()->id;
        }
        $goal_id = $this->_getParam('goal_id',null);
        $comment = $this->_getParam('comment',null);
        $goalModel = new Default_Model_Goal();
        $goalModel->saveGoalComment($goal_id, $comment, $loginUserId);
        $this->_helper->json(array('status' => 1));
    }
    public function addmilestoneAction() {

        $auth = Zend_Auth::getInstance();
        $goals = array();
        if($auth->hasIdentity()){
            $loginUserId = $auth->getStorage()->read()->id;
        }
        $goal_id = $this->_getParam('goal_id',null);
        $name = $this->_getParam('name',null);
        $target = $this->_getParam('target',null);
        $criteria_unit = $this->_getParam('criteria_unit',null);
        $goalModel = new Default_Model_Goal();
        $milestone_id = $goalModel->saveMiletsone($goal_id, $name, $target, $criteria_unit, $loginUserId);
        $result = sapp_Helper::sendGoalUpdateNotification($goal_id, "milestoneadded");
        $this->_helper->json(array('status' => 1));
    }

    public function editmilestoneAction() {

        $auth = Zend_Auth::getInstance();
        $goals = array();
        if($auth->hasIdentity()){
            $loginUserId = $auth->getStorage()->read()->id;
        }
        $milestone_id = $this->_getParam('milestone_id',null);
        $goal_id = $this->_getParam('goal_id',null);
        $name = $this->_getParam('name',null);
        $target = $this->_getParam('target',null);
        $criteria_unit = $this->_getParam('criteria_unit',null);
        $goalModel = new Default_Model_Goal();
        $goalModel->editMiletsone($milestone_id, $goal_id, $name, $target, $criteria_unit, $loginUserId);
        $result = sapp_Helper::sendGoalUpdateNotification($goal_id, "milestoneupdated");
        $this->_helper->json(array('status' => 1));
    }
    
    public function addmilestoneprogressAction() {
        $auth = Zend_Auth::getInstance();
        $goals = array();
        if($auth->hasIdentity()){
            $loginUserId = $auth->getStorage()->read()->id;
        }
        $milestone_id = $this->_getParam('milestone_id',null);
        $progress = $this->_getParam('progress',null);
        $goalModel = new Default_Model_Goal();
        $goalModel->setMiletsoneProgress($milestone_id, $progress, $loginUserId);
        $this->_helper->json(array('status' => 1));
    }

    public function getallgoalsAction(){

        $auth = Zend_Auth::getInstance();
        $goals = array();
        if($auth->hasIdentity()){
            $loginUserId = $auth->getStorage()->read()->id;
        }
        $owner = $this->_getParam('owner_id',null);
        if($owner == -1) {
            $goalModel = new Default_Model_Goal();        
            $goals = $goalModel->getCompanyGoalNames();
        } else {
            $goalModel = new Default_Model_Goal();        
            $goals = $goalModel->getGoalNamesForOwner($owner);
        }
        

        $this->_helper->json(array('result' => $goals));
    }

    public function viewAction(){
        $auth = Zend_Auth::getInstance();
        $goal_id =  $this->getRequest()->getParam('id');
        if($auth->hasIdentity()){
            $loginUserId = $auth->getStorage()->read()->id;
        }
        $goalModel = new Default_Model_Goal();
        $users = new Default_Model_Users();
        $goalData = $goalModel->getGoalNMilestoneDetailsForGoal($goal_id);
        if($goalData && is_array($goalData['goal']) && count($goalData['goal']) > 0 && $goalData['goal']['goal_type_id'] == 2)
        {
            //echo"<pre>"; print_r($goalData); exit;
            $m_cri = $goalModel->getMilestoneMeasurementCriteria();
            $this->view->goal = $goalData['goal'];
            $this->view->goalId = $goal_id;
            $this->view->milestones = $goalData['milestone'];
            $this->view->meausurementCriteria = $m_cri;
            $this->view->isEditable = false;
            $this->view->goalProgress = $goalModel->getGoalProgress($goal_id);
            $this->view->employeeOtherGoals = $goalModel->getGoalNamesForOwner($goalData['goal']['owner_id'],array($goalData['goal']['id']));
            
            if($loginUserId == $goalData['goal']['owner_id'])
                $this->view->isEditable = true;
            
            $this->view->canAssess = $goalModel->canAssessGoal($goal_id);

            $this->view->isArchiveable = $goalModel->isArchiveable($goal_id);


            $employeeModel = new Default_Model_Employee();
            $emp_data = $employeeModel->getsingleEmployeeData($loginUserId);
            $team_members = $employeeModel->getEmployeesUnderRM($loginUserId);

            $activeUsersExcludingIds[] = $loginUserId;
            $activeUsers = $users->getUsersNotInIdsArray($activeUsersExcludingIds);
            $this->view->activeUsers = $activeUsers;
            
            $goalAssesmentModel = new Default_Model_GoalAssessment();
            $goalOwnerId = $goalData['goal']['owner_id'];

            $isAddPeopleRightAvailable = $goalAssesmentModel->checkRightForAssessmentAddPeople($goalOwnerId);

            $this->view->canChangeOwner = false;
            if($isAddPeopleRightAvailable)
            {
                $this->view->canChangeOwner = true;
            }

            $this->view->team_members = $team_members;
             $this->view->user = $emp_data[0];
        } else {
            $this->view->rowexist = "norows";
        }
        
    }
    
    public function allteamandfollowedcommentsAction() {
        $this->_helper->layout->disableLayout();
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity()){
            $loginUserId = $auth->getStorage()->read()->id;
        } 
        $goal_id =  $this->getRequest()->getParam('goal_id');
        $goal_id =  empty($goal_id) ? 0 : $goal_id ;
        $user_id =  $this->getRequest()->getParam('user_id');
        $user_id =  empty($user_id) ? 0 : $user_id ;
        $type =  $this->getRequest()->getParam('type');
        $goalModel = new Default_Model_Goal();
        switch($type) {
            case "goal" :   $result = $goalModel->getTeamAndFollowedGoalComments($goal_id, $user_id);
                            break;
            case "milestone" :   $result = $goalModel->getTeamAndFollowedMilestoneComments($goal_id, $user_id);
                            break;
            case "audit" :   $result = $goalModel->getTeamAndFollowedAuditComments($goal_id, $user_id);
                            break;
            default     :   $goal_arr = $goalModel->getTeamAndFollowedGoalComments($goal_id, $user_id);
                            $milestone_arr = $goalModel->getTeamAndFollowedMilestoneComments($goal_id, $user_id);
                            $audit_arr = $goalModel->getTeamAndFollowedAuditComments($goal_id, $user_id);
                            $result = array_merge($goal_arr, $milestone_arr, $audit_arr);
        }
        usort($result, function ($item1, $item2) {
            return strtotime($item1['updated_at']) < strtotime($item2['updated_at']);
        });
        $this->view->result = $result;
        $this->render('allcomments', array('result'=>$result));
    }

    public function allcommentsAction() {
        $this->_helper->layout->disableLayout();
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity()){
            $loginUserId = $auth->getStorage()->read()->id;
        } 
        $goal_id =  $this->getRequest()->getParam('goal_id');
        $goal_id =  empty($goal_id) ? 0 : $goal_id ;
        $user_id =  $this->getRequest()->getParam('user_id');
        $user_id =  empty($user_id) ? 0 : $user_id ;
        $type =  $this->getRequest()->getParam('type');
        $goalModel = new Default_Model_Goal();
        switch($type) {
            case "goal" :   $result = $goalModel->getGoalComments($goal_id, $user_id);
                            break;
            case "milestone" :   $result = $goalModel->getMilestoneComments($goal_id, $user_id);
                            break;
            case "audit" :   $result = $goalModel->getAuditComments($goal_id, $user_id);
                            break;
            default     :   $goal_arr = $goalModel->getGoalComments($goal_id, $user_id);
                            $milestone_arr = $goalModel->getMilestoneComments($goal_id, $user_id);
                            if($goalModel->canAssessGoal($goal_id))
                                $audit_arr = $goalModel->getAuditComments($goal_id, $user_id);
                            else
                                $audit_arr = array();
                            $result = array_merge($goal_arr, $milestone_arr, $audit_arr);
        }
        //echo "<pre>"; print_r($result); exit;
        usort($result, function ($item1, $item2) {
            return strtotime($item1['updated_at']) < strtotime($item2['updated_at']);
        });
        $this->view->result = $result;
    }
    public function goalsfollowingAction() {
        $auth = Zend_Auth::getInstance();
        $goal_id =  $this->getRequest()->getParam('id');
        if($auth->hasIdentity()){
            $loginUserId = $auth->getStorage()->read()->id;
        }
        $goalModel = new Default_Model_Goal();
        $goalIdArray = explode(",",$goalModel->getFollowingGoalsIdAsString($loginUserId));
        $goalsData = $goalModel->getGoalsByID($goalIdArray);
        $this->view->goals = $goalsData;
    }
    public function convertmilestonetogoalAction(){
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity()){
            $loginUserId = $auth->getStorage()->read()->id;
        }
        $milestone_id =  $this->getRequest()->getParam('id');
        $goalModel = new Default_Model_Goal();
        $milestoneDetails = $goalModel->milestoneDetails($milestone_id); 
        if($milestoneDetails) { 
            $cycleModel = new Default_Model_Assessmentcycle();
            $cycles = $cycleModel->getCurrentAssessmentCycle();
            $allKeywords = $goalModel->getAllKeywords(); 
            $keywordsArr = array();
            foreach ($allKeywords as $key => $value) {
                $keywordsArr[] = $value['name'];
            }
            $aligned_goal = $goalModel->getSingleGoalById($milestoneDetails['goal_align_id']);
            if($aligned_goal[0]['goal_type_id'] == 2) {
                $align_goals = $goalModel->getGoalNamesForOwner($aligned_goal[0]['owner_id']);
            } else {
                $align_goals = $goalModel->getCompanyGoalNames();
            }
            $usersModel = new Default_Model_Users;
            $managers = $usersModel->getUsersNotInIdsArray(array('1'));
            $keywords = $goalModel->getAllKeywordsForGoal($milestoneDetails['goal_id']);
            if($_POST['goal_form']) {
                $form = $_POST['goal_form'];
                $data = array(
                            'name'                  => $form['name'],
                            'descrption'           => $form['description'],
                            'owner_id'              => $milestoneDetails['owner_id'],
                            'goal_type_id'          => 2,
                            'goal_align_id'         => $form['align_goal_id'],
                            'start_date'            => date("Y-m-d", strtotime($form['align_start_date'])),
                            'end_date'              => date("Y-m-d", strtotime($form['align_end_date'])),
                            'assessment_cycle_id'   => $form['align_period_id'],
                            'created_by'            => $loginUserId,
                            'updated_by'            => $loginUserId,
                            'tags'                  => $form['align_keyword_id']
                        ); 
                if(!$g_id = $goalModel->saveData($data, $milestone_id)) {
                    throw new Exception("Error occurred");
                }
                $this->_redirect('goal/view/id/'.$g_id);
        } 
            $this->view->aligned_goal = $aligned_goal[0];
            $this->view->keywords = $keywords;
            $this->view->allKeywords = $keywordsArr;
            $this->view->managers = $managers;
            $this->view->cycles = $cycles;
            $this->view->default_align_goals = $align_goals;
            $this->view->milestoneDetails = $milestoneDetails;
        } else {
            $this->view->rowexist = "norows";
        }
    }

    public function followunfollowgoalAction () {
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity()){
            $loginUserId = $auth->getStorage()->read()->id;
        }
        $goal_id =  $this->getRequest()->getParam('goal_id');
        $follow = $this->getRequest()->getParam('follow');
        $goalModel = new Default_Model_Goal();
        $goalModel->followUnfollowGoal($goal_id, $follow, $loginUserId);
        $this->_helper->json(array('status' => 1));
    }
    
    public function archivegoalAction () {
        $auth = Zend_Auth::getInstance();
        $goal_id =  $this->getRequest()->getParam('id');
        if($auth->hasIdentity()){
            $loginUserId = $auth->getStorage()->read()->id;
        }
        $users = new Default_Model_Users();
        $managerHierarchy = $users->getReportingManagers($loginUserId);
        $managers = array();
        if(is_array($managerHierarchy) && count($managerHierarchy) > 0) {
            foreach ($managerHierarchy as $key => $value) {
                if($value['manager'] != "")
                    $managers[] = $value['manager'];
            }
        }
        // print_r($emp_data);
        

        $goalModel = new Default_Model_Goal();
        $goalData = $goalModel->getGoalNMilestoneDetailsForGoal($goal_id);
        if($goalData && is_array($goalData['goal']) && count($goalData['goal']) > 0)
        {
            //$myManager = 
            $is_cxo = ($auth->getStorage()->read()->emprole == MANAGEMENTROLE) ? true : false;
            $this->view->is_cxo = $is_cxo;
            $canArchive = in_array($loginUserId, $managers) ? true : false; 
            if(!$is_cxo && !$canArchive ) {
                $this->view->accessRight = "denied";
            }
            else if($goalData['goal']['goal_type_id'] != 1 || $auth->getStorage()->read()->emprole == MANAGEMENTROLE) {

                $employeeModel = new Default_Model_Employees();
                $emp_data = $employeeModel->getEmployees(array($goalData['goal']['owner_id']));

                //echo MANAGEMENTROLE;
                $is_cxo_goal = ($emp_data[0]['emprole'] == MANAGEMENTROLE);

                $this->view->is_cxo_goal= $is_cxo_goal;

                $this->view->goalProgress = $goalModel->getGoalProgress($goal_id);
                $this->view->goal = $goalData['goal'];
                $this->view->milestones = $goalData['milestone'];
                $this->view->dep_goals = $goalModel->getDependentGoals($goal_id);
                $this->view->isChildGoalsArchived = $goalModel->isChildGoalsArchived($goal_id);
                if(isset($_POST['archive_goal'])) {
                    if($goalModel->setArchived($goal_id, $loginUserId)) {
                        $this->_redirect('okr');
                    }
                }
            } else {
                $this->view->rowexist = "norows";
            }
        } else { 
            $this->view->rowexist = "norows";
        }
    }

    public function searchAction()
    {
        
        $activeUsersExcludingIds = array();
        $activeUsers = $this->users->getUsersNotInIdsArray($activeUsersExcludingIds);
        $cycleModel = new Default_Model_Assessmentcycle();
        $cycles = $cycleModel->getCurrentAssessmentCycle();
        
        $this->view->activeUsers = $activeUsers;
        $this->view->cycles = $cycles;
    }
    public function searchresultsAction()
    {
        $search = $this->getRequest()->getParam('search');
        if($search)
        {
            $searchData = array();
            ($search['user'] == 'owner') ? $searchData['owner_id'] = $search['selected_users'] : $searchData['reporting_manager'] = $search['selected_users'];
            ($search['align_goal_id']) ? $searchData['align_goal_id'] = $search['align_goal_id'] : '';
            ($search['assessment_cycle_id']) ? $searchData['assessment_cycle_id'] = $search['assessment_cycle_id'] : '';
            ($search['start_date']) ? $searchData['start_date'] = date('Y-m-d' , strtotime($search['start_date'])) : '';
            ($search['end_date']) ? $searchData['end_date'] = date('Y-m-d' , strtotime($search['end_date'])) : '';
            ($search['status']) ? $searchData['status'] =  $search['status'] : '';
            ($search['assessment']) ? $searchData['assessment'] =  $search['assessment'] : '';
            ($search['name']) ? $searchData['name'] =  $search['name'] : '';
            ($search['limit']) ? $searchData['limit'] =  $search['limit'] : '2';
            ($search['page']) ? $searchData['page'] =  $search['page'] : '1';

//            print_r($search);exit;
           
            $result = $this->goals->search($searchData);
            if($result)
            {
                $this->view->data = $result;
            }
            else 
            {
                setcookie('search_result_zero', 'Zero result found', time() + (300), "/"); // 86400 = 1 
                $this->_redirect('goal/search');
            }
        }
    }
    
    public function ajaxchangegoalownerAction()
    {
        $userId = $this->_request->getParam('user_id');
        $goalId = $this->_request->getParam('goal_id');
        if($userId && $goalId)
        {
            $this->goals->changeGoalOwner($goalId , $userId);
            $this->_helper->json(array(
                'status'=>'success',
            ));
        }
//         $this->_helper->json(array(
//                'status'=>'error',
//            ));
    }
    
    public function ajaxmovemilestonetoanothergoalAction()
    {
        $milestoneId = $this->_request->getParam('milestone_id');
        $goalId = $this->_request->getParam('goal_id');
        if($milestoneId && $goalId)
        {
            $this->goals->moveMilestoneToAnotherGoal($goalId , $milestoneId);
            $this->_helper->json(array(
                'status'=>'success',
            ));
        }
    }
    
    public function ajaxaddmilestonecommentAction()
    {
        $milestoneId = $this->_request->getParam('milestone_id');
        $comment = $this->_request->getParam('comment');
        $goal_id = $this->_request->getParam('goal_id');
        if($milestoneId && $comment && $goal_id)
        {
            $this->goals->addMilestoneComment($goal_id, $milestoneId , $comment , $this->user_id);
            $this->_helper->json(array(
                'status'=>'success',
            ));
        }
    }
    
   
    
}

// http://localhost/cuelogic-hrms-php/index.php/goal/convertgoaltomilestone/id/9
// http://localhost/cuelogic-hrms-php/index.php/goal/view/id/11
