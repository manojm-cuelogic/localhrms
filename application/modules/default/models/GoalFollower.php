<?php

class Default_Model_Goals extends Zend_Db_Table_Abstract
{
	protected $_name = 'goalfollowers';
	protected $_primary = 'id';		

	public function getGoalFollowCount($userId)
    {
        $select = $this->select()
        ->setIntegrityCheck(false)
        ->from(array('gf'=>'goalfollowers'), array('GoalFollowCount'=>'count(gf.id)'))
        ->where('g.user_id="'.$userId.'"');
        
        return $this->fetchAll($select)->toArray();
    }
	
}
?>