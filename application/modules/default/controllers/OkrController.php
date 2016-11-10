<?php
class Default_OkrController extends Zend_Controller_Action {

//    public function init()
//    {
//        $employeeModel = new Default_Model_Employee();
//        $this->_options= $this->getInvokeArg('bootstrap')->getOptions();
//    }

    public function indexAction()
    {
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity()){
            $loginUserId = $auth->getStorage()->read()->id;
        }
        $cycleModel = new Default_Model_Assessmentcycle();
        $currentCycle = $cycleModel->getCurrentAssessmentCycle('current');
        $goalModel = new Default_Model_Goal();
        $myGoals = $goalModel->getNumberOfGoalsForUser($loginUserId);
        $companyGoals = $goalModel->getNumberOfCompanyGoals();
        $teamGoals = $goalModel->getNumberOfGoalsForTeam($loginUserId, $currentCycle[0]['id']);
        $followingGoals = $goalModel->getNumberOfFollowingGoals($loginUserId);
        $assessmentGoals = $goalModel->getNumberOfGoalsForAssesment($loginUserId);

        // print_r($companyGoals); exit;
        $this->view->myGoals = $myGoals[0];
        $this->view->companyGoals = $companyGoals[0];
        $this->view->teamGoals = $teamGoals;
        $this->view->followingGoals = $followingGoals[0];
        $this->view->assessmentGoals = $assessmentGoals;
        $this->view->user_id = $loginUserId;
    }

    public function goalsAction() {

    }

    public function addgoalAction(){

        $auth = Zend_Auth::getInstance();

        if($auth->hasIdentity()){
            $loginUserId = $auth->getStorage()->read()->id;
        }
    }

    public function archivegoalsAction(){

        $auth = Zend_Auth::getInstance();

        if($auth->hasIdentity()){
            $loginUserId = $auth->getStorage()->read()->id;
        }
    }
    public function archivegoalpopupAction(){

        $auth = Zend_Auth::getInstance();

        if($auth->hasIdentity()){
            $loginUserId = $auth->getStorage()->read()->id;
        }
    }


    public function convertmilestonetogoalAction(){

        $auth = Zend_Auth::getInstance();

        if($auth->hasIdentity()){
            $loginUserId = $auth->getStorage()->read()->id;
        }
    }

    public function goalassessmentAction(){

        $auth = Zend_Auth::getInstance();

        if($auth->hasIdentity()){
            $loginUserId = $auth->getStorage()->read()->id;
        }
    }

    public function goaldetailsAction(){

        $auth = Zend_Auth::getInstance();

        if($auth->hasIdentity()){
            $loginUserId = $auth->getStorage()->read()->id;
        }
    }

    public function goalsfollowingAction(){

        $auth = Zend_Auth::getInstance();

        if($auth->hasIdentity()){
            $loginUserId = $auth->getStorage()->read()->id;
        }
    }

    public function addremovepeopleAction(){

        $auth = Zend_Auth::getInstance();

        if($auth->hasIdentity()){
            $loginUserId = $auth->getStorage()->read()->id;
        }
    }
    public function assessmentdetailsAction(){

        $auth = Zend_Auth::getInstance();

        if($auth->hasIdentity()){
            $loginUserId = $auth->getStorage()->read()->id;
        }
    }
    public function assessmentmilestoneAction(){

        $auth = Zend_Auth::getInstance();

        if($auth->hasIdentity()){
            $loginUserId = $auth->getStorage()->read()->id;
        }
    }
    public function movemilestonetoanothergoalAction(){

        $auth = Zend_Auth::getInstance();

        if($auth->hasIdentity()){
            $loginUserId = $auth->getStorage()->read()->id;
        }
    }
    public function searchgoalAction(){

        $auth = Zend_Auth::getInstance();

        if($auth->hasIdentity()){
            $loginUserId = $auth->getStorage()->read()->id;
        }
    }


}