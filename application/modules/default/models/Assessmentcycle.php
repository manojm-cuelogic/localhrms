<?php
class Default_Model_Assessmentcycle extends Zend_Db_Table_Abstract
{
    protected $_name = 'assessmentcycle';
    protected $_primary = 'id';     

   public function getAllAssessmentCycles() {
        $select = $this->select()
        ->setIntegrityCheck(false)
        ->from(array('g'=>'assessmentcycle'), array('id'=>'g.id', 'display_name'=> 'g.display_name'));
        
        return $this->fetchAll($select)->toArray();
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
   
   public function getAssessmentPeriod()
   {
       $db = Zend_Db_Table::getDefaultAdapter();
        
        $qryStr = "select ap.* , ap.id pid,af.* from assessmentperiod ap left join assessmentfrequency af on ap.frequency_assessment_id = af.id order by ap.id DESC";
         $qry = $db->query($qryStr);
        $res = $qry->fetchAll();
        return $res;
        
   }
   public function getAssessmentPeriodById($id)
   {
       $db = Zend_Db_Table::getDefaultAdapter();
        
        $qryStr = "select ap.* , ap.id pid from assessmentperiod ap where ap.id = ".$id;
         $qry = $db->query($qryStr);
        $res = $qry->fetchAll();
        return $res[0];
        
   }
   public function getAssessmentCyclesByPeriodId($id)
   {
       $db = Zend_Db_Table::getDefaultAdapter();
        
        $qryStr = "select ac.* from assessmentcycle ac where ac.assessment_period_id = ".$id;
        $qry = $db->query($qryStr);
        $res = $qry->fetchAll();
        return $res;
        
   }
   public function updateAssessmentCycleLabel ($id, $new_label) {
      $db = Zend_Db_Table::getDefaultAdapter();
      $qry = "update assessmentcycle set display_name = '". $new_label ."' where id = $id";
      $db->query($qry);
   }
   public function getAssessmentFrequency()
   {
       $select = $this->select()
       ->setIntegrityCheck(false)
       ->from(array('ap'=>'assessmentfrequency'));
       return $this->fetchAll($select)->toArray();
   }
   public function getAssessmentFrequencyById($frequencyId)
   {
       $select = $this->select()
       ->setIntegrityCheck(false)
       ->from(array('ap'=>'assessmentfrequency'))
        ->where('ap.id = "'.$frequencyId.'"');
       return $this->fetchAll($select)->toArray();
   }

    public function saveAssessmentPeriod($frequency_assessment_id, $start_date_financial_year, $end_date_financial_year, $creator_user_id ) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $qry = "insert into assessmentperiod (frequency_assessment_id, start_date_financial_year,"
                . " end_date_financial_year, creator_user_id) "
                . "values (" . 
                                $frequency_assessment_id . ", " . 
                                "'" .date ( 'Y-m-d H:i:s' , strtotime($start_date_financial_year)) . "', " . 
                                "'" .date ( 'Y-m-d H:i:s' , strtotime($end_date_financial_year)) . "', " . 
                                $creator_user_id . ")";
        $db->query($qry);
        return $this->getAdapter()->lastInsertId('assessmentperiod');
    }
    
    public function saveAssessmentCycle($data) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $qry = "insert into assessmentcycle (display_name"
                . ", assessment_period_id"
                . ", start_date"
                . ", end_date"
                . ", creator_user_id"
                . ", created_at"
                . ", modified_at) "
                . "values ('" . 
                                $data['display_name'] . "', " . 
                                $data['assessment_period_id'] . ", " . 
                                "'" .date ( 'Y-m-d H:i:s' , strtotime($data['start_date'])) . "', " . 
                                "'" .date ( 'Y-m-d H:i:s' , strtotime($data['end_date'])) . "', " . 
                                $data['creator_user_id'] . ", '".
                                $data['created_at'] . "', '".
                                $data['modified_at'] . "')";
        return $db->query($qry);
    }
    
   public function getCurrentAssessmentCycle($onlyCurrent = false , $currentCycle = array())
   {
        $period = $this->getCurrentFinancialYear();
        $assessmentperiod = $period[0]['id'];
        $now = new \DateTime();
        $now->setTime(00, 00, 00);
        $currentDate = $now->format("Y-m-d h:i:s"); 
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(array('ap'=>'assessmentcycle'), array('id'=>'ap.id', 'displayName'=>'ap.display_name', 'startDate'=>'ap.start_date', 'endDate'=>'ap.end_date'))
            ->where('ap.assessment_period_id = "'.$assessmentperiod.'"');
        
        if($onlyCurrent == 'current')
        {
            $select->where('ap.start_date <= CURDATE() and ap.end_date >= CURDATE()');
        }
        elseif ($onlyCurrent == 'next' && $currentCycle)
        {
            $select->where('ap.start_date = DATE_ADD("'.$currentCycle['endDate'].'", INTERVAL 1 day) ');
        }
        return $this->fetchAll($select)->toArray();
   }


}
