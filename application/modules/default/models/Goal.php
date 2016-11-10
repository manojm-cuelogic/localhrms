<?php
class Default_Model_Goal extends Zend_Db_Table_Abstract
{
	protected $_name = 'goals';
	protected $_primary = 'id';		



    public function DeleteGoal($where) {
        
        return $this->delete($where);
    }

    public function SaveorUpdateGoals($data, $where='')
    {
        
        //$where = array('user_id=?'=>$user_id,'isactive'=>1);

         if($where != ''){
            $this->update($data, $where);
            return 'update';
        } else {
            $this->insert($data);
            $id=$this->getAdapter()->lastInsertId('goals');
            return $id;
        }
    }

   	public function getNumberOfGoalsForUser($userId, $forCycle = false)
    {
        $select = $this->select()
        ->setIntegrityCheck(false)
        ->from(array('g'=>'goals'), array('goalCount'=>'count(g.id)'))
        ->where('g.is_archieved= 0')
        ->where('g.owner_id="'.$userId.'"');
        if($forCycle !== false)
            $select->where('g.assessment_cycle_id = '.$forCycle.'');
        return $this->fetchAll($select)->toArray();
    }

    public function getNumberOfGoalsForAssesment($userId, $forCycle = false)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $extraWhere = "";
        if($forCycle !== false) 
            $extraWhere = " g.assessment_cycle_id = $forCycle and ";
        $qry = "select  count(ar.goal_id) as goalCount from assessmentrights ar
                 left join goals g on g.id = ar.goal_id 
                 where $extraWhere ar.user_id = $userId and g.is_archieved= 0";
//        exit;
        $res = $db->fetchRow($qry);
        return $res;
    }

    public function getNumberOfGoalsForTeam($userId, $forCycle = false)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $extraWhere = "";
        if($forCycle !== false) 
            $extraWhere = " g.assessment_cycle_id = $forCycle and ";
        $qry = "select count(g.id) as goalCount from goals g left join main_employees_summary mes on g.owner_id = mes.user_id
                    where $extraWhere mes.reporting_manager = $userId and g.is_archieved= 0";
        $res = $db->fetchRow($qry);
        return $res;
    }

	public function getGoalsForUser($userId, $forCycle = false)
    {
        $select = $this->select()
        ->setIntegrityCheck(false)
        ->from(array('g'=>'goals'))
        ->where('g.is_archieved= 0')
        ->where('g.owner_id="'.$userId.'"');
        if($forCycle !== false)
            $select->where('g.assessment_cycle_id = '.$forCycle.'');
        
        return $this->fetchAll($select)->toArray();
    }
	
	public function getNumberOfCompanyGoals()
    {
        $select = $this->select()
        ->setIntegrityCheck(false)
        ->from(array('g'=>'goals'), array('goalCount'=>'count(g.id)'))
        ->where('g.is_archieved= 0')
        ->where('g.goal_type_id= 1');
        
        return $this->fetchAll($select)->toArray();
    }

    public function getNumberOfFollowingGoals($userId)
    {
        $select = $this->select()
        ->setIntegrityCheck(false)
        ->from(array('gf'=>'goalfollowers'), array('goalCount'=>'count(gf.id)'))
        ->where('gf.user_id= "'.$userId.'"');
        
        return $this->fetchAll($select)->toArray();
    }

    public function getFollowingGoals($userId)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $qryStr = "select g.* from goalfollowers gf left join goals g on g.id = gf.goal_id where gf.user_id = $userId";
        $qry = $db->query($qryStr);
        $res = $qry->fetchAll();
        return $res;
    }

    public function getFollowingGoalsIdAsString($userId)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $query = "select GROUP_CONCAT(g.id) from goalfollowers gf left join goals g on g.id = gf.goal_id where gf.user_id = $userId";
        $res = $db->fetchOne($query);
        return $res;
    }
    
    public function getCompanyGoalNames()
    {
        $select = $this->select()
        ->setIntegrityCheck(false)
        ->from(array('g'=>'goals'), array('id'=>'g.id', 'name'=> 'g.name', 'isArchieved'=> 'g.is_archieved'))
        ->where('g.goal_type_id = 1');
        //->where('g.is_archieved = 0');
        
        return $this->fetchAll($select)->toArray();
    }
    
    public function getGoalNamesForOwner($userId, $notGoalIds = array())
    {
        $select = $this->select()
        ->setIntegrityCheck(false)
        ->from(array('g'=>'goals'), array('id'=>'g.id', 'name'=> 'g.name', 'isArchieved'=> 'g.is_archieved'))
        ->where('g.owner_id = "'.$userId.'"')
        ->where('g.goal_type_id = 2');
        //->where('g.is_archieved = 0');
        if(!empty($notGoalIds))
            $select->where('g.id not in ('.implode(",", $notGoalIds).')');
        return $this->fetchAll($select)->toArray();
    }

    public function getGoalFollowers($goalId)
    {
        $select = $this->select()
        ->setIntegrityCheck(false)
        ->from(array('gf'=>'goalfollowers'), array('userId'=>'gf.user_id'))
        ->where("gf.goal_id = $goalId");
                
        return $this->fetchAll($select)->toArray();
    }

    public function getGoalOwnerName($goalId)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $qryStr = "select userfullname from main_employees_summary where user_id IN (select owner_id from goals where id=$goalId)";
        $qry = $db->query($qryStr);
        $res = $qry->fetchAll();
        return $res;
    }
    public function getSingleGoalById($goalId, $userGoal = true)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $condition = " and goal_type_id = 2";
        if(!$userGoal)
            $condition = " and goal_type_id = 1";
        $qryStr = 'SELECT g.id, 
                            g.name, 
                            g.descrption, 
                            g.owner_id, 
                            g.goal_type_id, 
                            g.goal_align_id, 
                            g.start_date, 
                            g.end_date, 
                            g.is_archieved, 
                            g.assessment_cycle_id
                        FROM  goals g 
                    WHERE g.id = '. $goalId.$condition;
                    //AND g.is_archieved = 0;
        $qry = $db->query($qryStr);
        $res = $qry->fetchAll();
        return $res;
    } 
    public function getGoalDetailsById1($goalId)
    {
        $db = Zend_Db_Table::getDefaultAdapter();

        $qryStr = 'SELECT g.id as goal_id, 
                            g.name, 
                            g.descrption, 
                            g.owner_id, 
                            g.goal_type_id, 
                            g.goal_align_id, 
                            g.start_date, 
                            g.end_date, 
                            g.is_archieved, 
                            g.assessment_cycle_id, 
                            m.id as milestone_id, 
                            m.title as milestone_title, 
                            m.goal_id as milestone_goal_id, 
                            m.measurement_criteria_id as measurement_criteria_id 
                        FROM  goals g, milestones m 
                    WHERE g.id = "'. $goalId.'"
                    AND g.id = m.goal_id';

                    //AND g.is_archieved = 0;
        $qry = $db->query($qryStr);
        $res = $qry->fetchAll();
        return $res;
    }

    public function milestoneDetails($milestone_id) {
        $query = "select m.id, m.title as name, m.goal_id, g.owner_id, g.goal_align_id, g.goal_type_id, g.assessment_cycle_id, g.start_date, g.end_date, g.assessment_cycle_id, u.userfullname from milestones m left join goals g on g.id = m.goal_id left join main_users u on u.id=g.owner_id where m.id = $milestone_id";
        $db = Zend_Db_Table::getDefaultAdapter();
        $res = $db->fetchRow($query);
        //echo "<pre>"; print_r($res); exit;
        return $res;
    }

    public function getAllKeywordsForGoal($goal_id) {
        $query = "SELECT GROUP_CONCAT(t.name) as keywords FROM goaltags gt left join tags t on t.id = gt.tag_id where gt.goal_id = $goal_id";
        $db = Zend_Db_Table::getDefaultAdapter();
        $res = $db->fetchOne($query);
        return $res;
    }
    public function getAllKeywords() {
        $query = "SELECT name FROM tags";
        $db = Zend_Db_Table::getDefaultAdapter();
        $res = $db->fetchAll($query);
        return $res;
    }
    public function saveData($data, $fromMilestone = false) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $qry = "INSERT INTO " . $this->_name . "
                            (
                                name,
                                descrption,
                                owner_id,
                                goal_type_id,
                                goal_align_id,
                                start_date,
                                end_date,
                                assessment_cycle_id,
                                created_by,
                                updated_by,
                                created_at,
                                updated_at
                            ) values(
                                " .$db->quote($data['name']). ",
                                " . $db->quote($data['descrption']) . ",
                                " . $data['owner_id'] . ",
                                " . $data['goal_type_id'] . ",
                                " . $data['goal_align_id'] . ",
                                '" . $data['start_date'] . "',
                                '" . $data['end_date'] . "',
                                " . $data['assessment_cycle_id'] . ",
                                " . $data['created_by'] . ",
                                " . $data['updated_by'] . ",
                                now(),
                                now()
                            ) ";
//echo $qry; exit;
        $db->query($qry);
        $goal_id =  $db->lastInsertId();
        if(isset($data['tags']) && strlen(trim($data['tags'])) > 0) {
            $keywords = explode(",", $data['tags']);
            $this->saveKeywords($keywords, $goal_id);
        }
        if($fromMilestone) {
            $qry = "delete from milestones where id = " . $fromMilestone;
            $db->query($qry);
            $this->convertToGoalComment($fromMilestone,$goal_id);
        }
        
        return $goal_id;
    }

    public function convertToGoalComment($milestone_id,$goal_id) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $qry = "select * from milestonecomments where milestone_id = $milestone_id";
        $res = $db->fetchAll($qry);
        $values = "";
        $goal_qry = "insert into goalcomments (comment, goal_id, user_id, created_at, updated_at) values ";
        foreach($res as $k=>$result) {
            if($k>0) $values .= ",";
            $values .= " (".$db->quote($result['comment']).", ".$goal_id.", ".$result['user_id'].", now(), now())";
        }

        $goal_qry .= $values;
        if($values != "")
            $db->query($goal_qry);
        $qry = "delete from milestonecomments where milestone_id = $milestone_id";
        $db->query($qry);
    }

    public function editData($data) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $qry = "update " . $this->_name . " SET
                                name = " . $db->quote($data['name']) . ",
                                descrption = " . $db->quote($data['descrption']) . ",
                                owner_id = " . $data['owner_id'] . ",
                                goal_type_id = " . $data['goal_type_id'] . ",
                                goal_align_id = " . $data['goal_align_id'] . ",
                                start_date = '" . $data['start_date'] . "',
                                end_date = '" . $data['end_date'] . "',
                                assessment_cycle_id = " . $data['assessment_cycle_id'] . ",
                                updated_by = " . $data['updated_by'] . ",
                                updated_at = NOW()
                            where id = " . $data['id'];
        $db->query($qry);
        $goal_id = $data['id'];
        if(isset($data['tags']) && strlen(trim($data['tags'])) > 0) {
            $keywords = explode(",", $data['tags']);
            $this->saveKeywords($keywords, $goal_id, true);
        } else {
            $this->saveKeywords(array(), $goal_id, true);
        }
        
        return 'update';
    }
    public function saveKeywords($keywords, $goal_id, $edit = false) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $k_arr = array();
        foreach($keywords as $key) {
            $k_arr[] = strtolower(trim($key));
        } 
        $tmp = implode("\",\"", $k_arr);
        $tmp = '"' . $tmp . '"';
        $res = array();
        if(count($keywords) > 0) {
            $qry = "select id, name from tags where name in ($tmp)";
            $qry = $db->query($qry);
            $res = $qry->fetchAll();
        }
        if($edit) {
            $delete_qry = "delete from goaltags where goal_id = ". $goal_id;
            $db->query($delete_qry);
        }
        foreach($res as $r) {
            if (($key = array_search($r['name'], $k_arr)) !== false) unset($k_arr[$key]);
            $this->saveGoalKeyword($r['id'], $goal_id);
        }
        //Saving new keywords
        foreach($k_arr as $k) {
            $qry = "insert into tags (name) values (" . $db->quote($k) . ")";
            $db->query($qry);
            $id = $db->lastInsertId();
            $this->saveGoalKeyword($id, $goal_id);
        }
    }

    public function saveGoalKeyword($keyword_id, $goal_id) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $qry = "insert into goaltags (tag_id, goal_id) values (" . $keyword_id . ", " . $goal_id . ")";
        $db->query($qry);
    }

    public function getCurrentFinancialYear()
    {
       
        $now = new \DateTime();
        $now->setTime(00, 00, 00);
        $currentDate = $now->format("Y-m-d h:i:s"); 
        $select = $this->select()
        ->setIntegrityCheck(false)
        ->from(array('ap'=>'assessmentperiod'), array('id'=>'ap.id'))
        ->where('ap.start_date_financial_year <= "'.$currentDate.'" AND  ap.end_date_financial_year >= "'.$currentDate.'"');
        return $this->fetchAll($select)->toArray();
    }

    public function getCurrentAssessmentCycle($assessmentperiod)
    {
       
        $now = new \DateTime();
        $now->setTime(00, 00, 00);
        $currentDate = $now->format("Y-m-d h:i:s"); 
        $select = $this->select()
        ->setIntegrityCheck(false)
        ->from(array('ap'=>'assessmentcycle'), array('id'=>'ap.id', 'displayName'=>'ap.display_name', 'startDate'=>'ap.start_date', 'endDate'=>'ap.end_date'))
        ->where('ap.assessment_period_id = "'.$assessmentperiod.'"');
        
        return $this->fetchAll($select)->toArray();
    }


    public function getNumberOfGoalsForDirectReporters($empId){


        $db = Zend_Db_Table::getDefaultAdapter();
        $qryStr = 'SELECT count(g.id) as count 
                    FROM  goals g
                    WHERE g.is_archieved = 0 
                    AND g.owner_id IN (select e.user_id  from main_employees_summary e where e.reporting_manager="'.$empId.'")';
        $qry = $db->query($qryStr);
        $res = $qry->fetchAll();
        return $res;
    }
    
    public function isArchivePossibleForGoal($goalId){

        $select = $this->select()
        ->setIntegrityCheck(false)
        ->from(array('ga'=>'goalassessments'), array('assessmentCount'=>'count(ga.id)'))
        ->where('ga.goal_id = "'.$goalId.'"');
        
        $result = $this->fetchAll($select)->toArray();

        if(is_array($result) && count($result)){
            if($result['assessmentCount'] >= 1) {
                return true;
            }
            else 
                return false;


        } 

        return false;
    }
    

    public function getGoalAssessmentAssignee($goalId){

        $select = $this->select()
        ->setIntegrityCheck(false)
        ->from(array('ar'=>'assessmentrights'), array('userId'=>'ar.user_id'))
        ->where('ar.goal_id = "'.$goalId.'"');
        
        return $result = $this->fetchAll($select)->toArray();
    }

     public function canAssessGoal($goalId){
        $goal =$this->getSingleGoalById($goalId);
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity()){
            $user_id = $auth->getStorage()->read()->id;
        }
        if($goal[0]['owner_id']==$user_id) return true;
        
        $query = "select count(*) from goals g LEFT JOIN main_employees_summary e 
        on e.user_id=g.owner_id WHERE g.id=$goalId AND e.reporting_manager = $user_id"; //exit;
        $db = Zend_Db_Table::getDefaultAdapter();

        $res = $db->fetchOne($query);
        if($res >0 ) return true ;
 
        $query = "select count(*) from assessmentrights where user_id = $user_id AND goal_id = $goalId";
        $res = $db->fetchOne($query);
        if($res >0 ) return true ;
        
        return false;
    }

    public function getGoalByID($goalId)
        {
            try
            {
                $auth = Zend_Auth::getInstance();
                if($auth->hasIdentity()){
                    $user_id = $auth->getStorage()->read()->id;
                }
                $db = Zend_Db_Table::getDefaultAdapter();
                $qry = "select g.*, ac.display_name quarter, mu.userfullname owner_name,
                            ( 
                                select GROUP_CONCAT(t.name SEPARATOR ', ') from tags t , goaltags gt WHERE gt.tag_id = t.id and gt.goal_id = g.id
                            ) keywords,
                            (
                                    select count(ga.id) from goalassessments ga WHERE ga.goal_id = g.id
                            ) assessment_count,
                            (
                                select sg.name from goals sg where sg.id = g.goal_align_id 
                            ) goal_align,
                            (
                                select count(gf.id) from goalfollowers gf where gf.goal_id = g.id
                            ) followers,
                            (
                                select IF(count(gfu.id) > 0, 1, 0) from goalfollowers gfu where gfu.goal_id = g.id and gfu.user_id = $user_id
                            ) following
                            from goals g 
                            join assessmentcycle ac on g.assessment_cycle_id = ac.id
                            join main_users mu on g.owner_id = mu.id
                            where g.id = ".$goalId;
                $data = $db->query($qry);
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

    public function getGoalsByID($goalIdArray)
        {
            try
            {
                if(!$goalIdArray)
                    return false;
                $auth = Zend_Auth::getInstance();
                if($auth->hasIdentity()){
                    $user_id = $auth->getStorage()->read()->id;
                }
                $db = Zend_Db_Table::getDefaultAdapter();
                $qry = "select g.*, ac.display_name quarter, mu.userfullname owner_name,
                            ( 
                                select GROUP_CONCAT(t.name SEPARATOR ', ') from tags t , goaltags gt WHERE gt.tag_id = t.id and gt.goal_id = g.id
                            ) keywords,
                            (
                                    select count(ga.id) from goalassessments ga WHERE ga.goal_id = g.id
                            ) assessment_count,
                            (
                                select sg.name from goals sg where sg.id = g.goal_align_id 
                            ) goal_align,
                            (
                                select count(gf.id) from goalfollowers gf where gf.goal_id = g.id
                            ) followers,
                            (
                                select IF(count(gfu.id) > 0, 1, 0) from goalfollowers gfu where gfu.goal_id = g.id and gfu.user_id = $user_id
                            ) following
                            from goals g 
                            join assessmentcycle ac on g.assessment_cycle_id = ac.id
                            join main_users mu on g.owner_id = mu.id
                            where g.id in (".implode(",",$goalIdArray).")";

                $data = $db->query($qry);
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
    public function getGoalNMilestoneDetailsForGoal($goalId)
    {
       
        $result = array(); 
        $result['goal'] = $this->getGoalByID($goalId);

        $milestone = new Default_Model_Milestone();
        $result['milestone'] = $milestone->getGoalMilestones($goalId);

        return $result;
    }
    public function getGoalComments($goal_id, $user_id) {
        $db = Zend_Db_Table::getDefaultAdapter();
        if($goal_id != 0)
            $qryStr = "select g.name as title, gc.comment as comment, gc.updated_at as updated_at, u.userfullname as userfullname, u.profileimg  as profileimg, 'goal' as type from goalcomments gc left join goals g on g.id = gc.goal_id left join main_users u on u.id = gc.user_id where gc.goal_id = " . $goal_id;
        else
            $qryStr = "select g.name as title, gc.comment as comment, gc.updated_at as updated_at, u.userfullname as userfullname, u.profileimg  as profileimg, 'goal' as type from goalcomments gc left join goals g on g.id = gc.goal_id left join main_users u on u.id = gc.user_id where g.owner_id = " . $user_id;
        // echo $qryStr; exit;
        $qry = $db->query($qryStr);
        $res = $qry->fetchAll();
        return $res;
    }
    public function getMilestoneComments($goal_id, $user_id) {
        $db = Zend_Db_Table::getDefaultAdapter();
        if($goal_id != 0)
            $qryStr = "select m.title as title, mc.comment as comment, mc.updated_at as updated_at, u.userfullname as userfullname, u.profileimg  as profileimg, 'milestone' as type from milestonecomments mc left join milestones m on m.id = mc.milestone_id left join main_users u on u.id = mc.user_id where mc.goal_id = $goal_id";
        else
            $qryStr = "select m.title as title, mc.comment as comment, mc.updated_at as updated_at, u.userfullname as userfullname, u.profileimg  as profileimg, 'milestone' as type from milestonecomments mc left join milestones m on m.id = mc.milestone_id left join main_users u on u.id = mc.user_id left join goals g on g.id = mc.goal_id where g.owner_id = $user_id";
        $qry = $db->query($qryStr);
        $res = $qry->fetchAll();
        return $res;
    }
    public function getAuditComments($goal_id, $user_id) {
        $db = Zend_Db_Table::getDefaultAdapter();
        if($goal_id != 0)
            $qryStr = "select g.name as title, ga.comment as comment, ga.updated_at as updated_at, u.userfullname as userfullname, u.profileimg  as profileimg, 'audit' as type from goalassessments ga left join goals g on g.id = ga.goal_id left join main_users u on u.id = ga.assessed_by where ga.goal_id = $goal_id";
        else
            $qryStr = "select g.name as title, ga.comment as comment, ga.updated_at as updated_at, u.userfullname as userfullname, u.profileimg  as profileimg, 'audit' as type from goalassessments ga left join goals g on g.id = ga.goal_id left join main_users u on u.id = ga.assessed_by where g.owner_id = $user_id";
        $qry = $db->query($qryStr);
        $res = $qry->fetchAll();
        return $res;
    }

    public function getTeamAndFollowedGoalComments($goal_id, $user_id) {
        $db = Zend_Db_Table::getDefaultAdapter();
        if($goal_id != 0)
            $qryStr = "select g.name as title, gc.comment as comment, gc.updated_at as updated_at, u.userfullname as userfullname, u.profileimg  as profileimg, 'goal' as type from goalcomments gc left join goals g on g.id = gc.goal_id left join main_employees_summary u on u.user_id = gc.user_id where u.reporting_manager = $user_id and gc.goal_id = " . $goal_id;
        else
            $qryStr = "select g.name as title, gc.comment as comment, gc.updated_at as updated_at, u.userfullname as userfullname, u.profileimg as profileimg, 'goal' as type from goalcomments gc left join goals g on g.id = gc.goal_id left join main_employees_summary u on u.user_id = gc.user_id where u.reporting_manager = $user_id OR gc.goal_id in (select gf.goal_id from goalfollowers gf where gf.user_id = $user_id)";
        // echo $qryStr; exit;
        $qry = $db->query($qryStr);
        $res = $qry->fetchAll();
        return $res;
    }
    public function getTeamAndFollowedMilestoneComments($goal_id, $user_id) {
        $db = Zend_Db_Table::getDefaultAdapter();
        if($goal_id != 0)
            $qryStr = "select m.title as title, mc.comment as comment, mc.updated_at as updated_at, u.userfullname as userfullname, u.profileimg  as profileimg, 'milestone' as type from milestonecomments mc left join milestones m on m.id = mc.milestone_id left join main_employees_summary u on u.user_id = mc.user_id where  u.reporting_manager = $user_id andmc.goal_id = $goal_id";
        else
            $qryStr = "select m.title as title, mc.comment as comment, mc.updated_at as updated_at, u.userfullname as userfullname, u.profileimg as profileimg, 'milestone' as type from milestonecomments mc left join milestones m on m.id = mc.milestone_id left join main_employees_summary u on u.user_id = mc.user_id left join goals g on g.id = mc.goal_id where u.reporting_manager = $user_id OR  mc.goal_id in (select gf.goal_id from goalfollowers gf where gf.user_id = $user_id)";
        $qry = $db->query($qryStr);
        $res = $qry->fetchAll();
        return $res;
    }
    public function getTeamAndFollowedAuditComments($goal_id, $user_id) {
        $db = Zend_Db_Table::getDefaultAdapter();
        if($goal_id != 0)
            $qryStr = "select g.name as title, ga.comment as comment, ga.updated_at as updated_at, u.userfullname as userfullname, u.profileimg  as profileimg, 'audit' as type from goalassessments ga left join goals g on g.id = ga.goal_id left join main_employees_summary u on u.user_id = ga.assessed_by where ga.goal_id = $goal_id and u.reporting_manager = $user_id";
        else
            $qryStr = "select g.name as title, ga.comment as comment, ga.updated_at as updated_at, u.userfullname as userfullname, u.profileimg  as profileimg, 'audit' as type from goalassessments ga left join goals g on g.id = ga.goal_id left join main_employees_summary u on u.user_id = ga.assessed_by where u.reporting_manager = $user_id OR ga.goal_id in (select gf.goal_id from goalfollowers gf where gf.user_id = $user_id)";
        $qry = $db->query($qryStr);
        $res = $qry->fetchAll();
        return $res;
    }

    public function saveGoalComment($goal_id, $comment, $user_id) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $qry = "insert into goalcomments (comment, goal_id, user_id, created_at, updated_at) values (" . $db->quote($comment) . ", ".$goal_id.",".$user_id.",now(), now())";
        $db->query($qry);
    }


    public function getMilestoneMeasurementCriteria() {
        $db = Zend_Db_Table::getDefaultAdapter();
        $qryStr = "select * from milestonemeasurementcriteria";
        $qry = $db->query($qryStr);
        $res = $qry->fetchAll();
        return $res;
    }

    public function saveMiletsone($goal_id, $name, $target, $criteria_unit, $loginUserId) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $qry = "insert into milestones (title, goal_id, target, goal_align_id, measurement_criteria_id,created_by, updated_by,created_at, updated_at) values 
        (" . $db->quote($name) . ", ".$goal_id.",".$target.",0,".$criteria_unit.",".$loginUserId.",".$loginUserId.",now(), now())";
        
        $db->query($qry);
        return $db->lastInsertId();
    }

    public function editMiletsone($milestone_id, $goal_id, $name, $target, $criteria_unit, $loginUserId) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $qry = "update milestones set title =" .  $db->quote($name) . ", goal_id=".$goal_id.", target=".$target.", measurement_criteria_id = ".$criteria_unit.",created_by=".$loginUserId.", updated_by=".$loginUserId.", updated_at = now() 
                where id =".$milestone_id; 
        $db->query($qry);
    }

    public function setMiletsoneProgress($milestone_id, $progress, $loginUserId) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $qry = "update milestones set progress =" . $progress . ", updated_by=".$loginUserId.", updated_at = now() 
                where id =".$milestone_id; 
        $db->query($qry);
    }
    
    public function updateGoalIsAssessment($data) 
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $qry = "update " . $this->_name . " SET ";
        if($data['is_self_assessment_done'])
        {
            $qry .= " is_self_assessment_done = '" . $data['is_self_assessment_done'] . "' , ";
        }
        else if($data['is_manager_assessment_done'])
        {
            $qry .= " is_manager_assessment_done = '" . $data['is_manager_assessment_done'] . "' , ";
        }
        $qry .= " updated_at = NOW() where id = " . $data['id'];
        return $db->query($qry);
    }
    
    public function search($search)
    {
        try
        {
            $db = Zend_Db_Table::getDefaultAdapter();
       
            $search = array_merge(array(
                'owner_id' => array(),
                'reporting_manager' => array(),
                'assessment_cycle_id' => '',
                'align_goal_id' => '',
                'end_date' => '',
                'start_date' => '',
                'status' => '', //planned , in_progress, archived
                'assessment' => '', //assessment_self , assessment_manager, assessment_any, assessment_no, assessment_no_manager, assessment_no_self

                'goal_type_id' => 2,
                'name' => '',
                'can_assesst_by' => '',
                'limit' => '2',
                'page' => '0',
                
            ), $search);
           
            
            if($search['page'] == 0)
            {
                $search['offset'] = 0;
            }
            else
            {
                $search['offset'] = $search['page'] * $search['limit'];
            }
            
            
            $qry = "SELECT g.id, g.name, g.descrption, g.owner_id, g.start_date, g.end_date, g.created_by, g.created_at, "
                    . "g.updated_at, g.is_archieved, g.is_manager_assessment_done, g.is_self_assessment_done, "
                    . "mu.id user_id, mu.userfullname userfullname, mu.emailaddress, mu.profileimg FROM goals g "
                    . " join main_users mu on g.owner_id = mu.id WHERE 1 " ;
            if($search['owner_id'])
            {
                $qry .= " and g.owner_id in (".implode(',', $search['owner_id']).") ";
            }
            if($search['name'])
            {
                $qry .= " and g.name LIKE '%".$search['name']."%'  ";
            }
            if($search['reporting_manager'])
            {
                $qry .= " and g.owner_id in ( select me.user_id from main_employees me where me.reporting_manager in (".implode(',', $search['reporting_manager']).")     ) ";
            }
            if($search['align_goal_id'])
            {
                $qry .= " and g.goal_align_id = ".$search['align_goal_id'];
            }
            if($search['assessment_cycle_id'])
            {
                $qry .= " and g.assessment_cycle_id = ".$search['assessment_cycle_id'];
            }
            
            if($search['can_assesst_by'])
            {
                $qry .= " and (g.id in (select ar.goal_id "
                        . "from assessmentrights ar "
                        . "where ar.user_id = ".$search['can_assesst_by'].""
                        . " ))";
            }
            
            if($search['start_date'] && $search['end_date'])
            {
                $qry .= " and g.start_date >= '" . $search['start_date'] . "'  and end_date <= '".$search['end_date']."' ";
            }
            
            if($search['status'])
            {
                $cycleModel = new Default_Model_Assessmentcycle();
                $currentCycles = $cycleModel->getCurrentAssessmentCycle('current')[0];
                if($search['status'] == 'in_progress')
                {
                    $qry .= " and  (g.start_date BETWEEN '".$currentCycles['startDate']."' AND '".$currentCycles['endDate']."')";
                }
                else if($search['status'] == 'planned')
                {
                    $nextCycles = $cycleModel->getCurrentAssessmentCycle('next' , $currentCycles)[0];
                    
                    $qry .= " and  (g.start_date BETWEEN '".$nextCycles['startDate']."' AND '".$nextCycles['endDate']."')";
                }
                
            }
            
            if($search['assessment'])
            {
               
                if($search['assessment'] == 'assessment_self')
                {
                   $qry .= " and g.is_self_assessment_done = 1 ";
                }
                else if($search['assessment'] == 'assessment_manager')
                {
                    $qry .= " and g.is_manager_assessment_done = 1 ";
                }
                else if($search['assessment'] == 'assessment_any')
                {
                    $qry .= " and ( g.is_manager_assessment_done = 1 or g.is_self_assessment_done = 1 )";
                }
                else if($search['assessment'] == 'assessment_no')
                {
                    $qry .= " and g.is_manager_assessment_done = 0 and g.is_self_assessment_done = 0 ";
                }
                else if($search['assessment'] == 'assessment_no_manager')
                {
                    $qry .= " and g.is_manager_assessment_done = 0 ";
                }
                else if($search['assessment'] == 'assessment_no_self')
                {
                    $qry .= " and g.is_self_assessment_done = 0 ";
                }
                
                
            }
            
            $archived = 0;
            if($search['status'] && $search['status'] == 'archived')
            { 
                $archived = 1;
            }
            $qry .= " And g.is_archieved = ".$archived." and g.goal_type_id = ".$search['goal_type_id'];
            $qry .= ' ORDER BY updated_at DESC ';
//           $qry .= ' limit ' . $search['limit'] . ' offset '. $search['offset'];
           
//            echo $qry; exit;
            $data = $db->query($qry);
            $goals = $data->fetchAll();
            $dataByUser = array();
            if($goals)
            {
                
                
                foreach ($goals as $goal)
                {
                    $dataByUser[$goal['user_id']]['user_id']  = $goal['user_id'];
                    $dataByUser[$goal['user_id']]['userfullname']  = $goal['userfullname'];
                    $dataByUser[$goal['user_id']]['goals'][]  = $goal;
                }
            }
            return $dataByUser;
        }
        catch(Exception $e)
        {
             //no any log method found
            return false;
        }


    }

    public function followUnfollowGoal($goal_id, $follow, $user_id) {
        $db = Zend_Db_Table::getDefaultAdapter();
        if($follow) {
            $qry = "insert into goalfollowers (goal_id, user_id, created_at, updated_at) 
                        values ($goal_id, $user_id, now(), now())";
        } else {
            $qry = "delete from goalfollowers where goal_id = $goal_id and user_id = $user_id";
        }
        $db->query($qry);
    }
    
    public function changeGoalOwner($goalId, $newOwnerId) 
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $qry = "update goals set owner_id ='" . $newOwnerId . "', updated_at = now() 
                where id =".$goalId; 
        return $db->query($qry);
    }
    public function moveMilestoneToAnotherGoal($goalId, $milestoneId) 
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $qry = "update milestones set goal_id ='" . $goalId . "', updated_at = now() 
                where id =".$milestoneId;
        $db->query($qry);
        $qry = "update milestoneassessment set goal_id ='" . $goalId . "', updated_at = now() 
                where milestone_id =".$milestoneId; 
        $db->query($qry);
        $qry = "update milestonecomments set goal_id ='" . $goalId . "', updated_at = now() 
                where milestone_id =".$milestoneId; 
        $db->query($qry);
        return true;
    }
    public function addMilestoneComment($goalId, $milestone_id, $comment, $userId) 
    {
        $db = Zend_Db_Table::getDefaultAdapter();
                echo $qry = "insert into milestonecomments (comment, goal_id, milestone_id, user_id, created_at, updated_at) "
                        . "values (" . $db->quote($comment) . ", ".$goalId.",".$milestone_id.",".$userId.",now(), now())";
        return $db->query($qry);
    }

    public function getGoalProgress ($goal_id) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $qryStr = "select * from milestones where goal_id = " . $goal_id;
        $qry = $db->query($qryStr);
        $goals = $qry->fetchAll();
        $totalCount = 0;
        $totalPercentage = 0;
        //echo "<pre>"; print_r($goals); exit;
        foreach($goals as $goal) {
            $target = $goal['target'];
            $progress = is_null($goal['progress']) ? 0 : $goal['progress'];
            $percentage = $progress * 100 / $target;
            $totalPercentage += $percentage;
            $totalCount++;
        }
        $progress =  ($totalCount == 0) ? 0 : round($totalPercentage/$totalCount);
        return $progress;
    }

    public function isArchiveable($goal_id) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity()){
            $user_id = $auth->getStorage()->read()->id;
        }
        $qry = "select * from goals where id = $goal_id";
        $res = $db->fetchRow($qry);

        $userModel = new Default_Model_Users;
        $managers = $userModel->getReportingManagers($user_id);

        if($user_id != $res['owner_id']) {
            if(!in_array($user_id, $managers)) {
                return false;
            }
        }
        
        //$qry = "select  is_self_assessment_done,  is_archieved, is_manager_assessment_done from goals where id = $goal_id";
        //$res = $db->fetchRow($qry);
        if($res['is_archieved'])
            return false;
        if(!$res['is_self_assessment_done'])
            return false;
        if(!$res['is_manager_assessment_done'])
            return false;
        //if(!$this->isChildGoalsArchived($goal_id))
            //return false;
        return true;
    }

    public function isChildGoalsArchived ($goal_id) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $qry = "select  count(*) from goals where goal_align_id = $goal_id and is_archieved = 0";
        $count = $db->fetchOne($qry);
        if($count == 0)
            return true;
        return false;
    }

    public function getDependentGoals ($goal_id) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $qry = "select  g.*, u.userfullname from goals g left join main_users u on u.id = g.owner_id where goal_align_id = $goal_id";
        return $db->fetchAll($qry);
    }

    public function setArchived ($goal_id, $loginUserId) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $qry = "update goals set is_archieved ='1', updated_at = now(), updated_by = $loginUserId
                where id =".$goal_id;  //die($qry);
        $db->query($qry);
        return true;
    }
}
?>