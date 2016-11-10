<?php
class Default_Model_Employeedocs extends Zend_Db_Table_Abstract
{
	protected $_name = 'main_employeedocuments';
    protected $_primary = 'id';
	
	public function getEmpDocumentsByFieldOrAll($field='',$value='')
	{
		$where = '';
		
		if($field && $value)
			$where = ' AND ed.'.$field.' = "'.$value.'"';
			
		$select = $this->select()
						->setIntegrityCheck(false)
						->from(array('ed'=>'main_employeedocuments'),array('ed.*'))
						->where('ed.isactive = 1'.$where);
					
		return $this->fetchAll($select)->toArray();
	}
	
	public function SaveorUpdateEmpDocuments($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_employeedocuments');
			return $id;
		}
	}
	
	public function checkDocNameByUserIdAndDocId($userId, $docName, $docId='')
	{
		$where = '';
		if($docId)
			$where = ' AND id != '.$docId;
		$select = $this->select()
						->setIntegrityCheck(false)
						->from(array('ed'=>'main_employeedocuments'),array('ed.*'))
						->where('ed.isactive = 1 AND ed.user_id = '.$userId.' AND ed.name = "'.$docName.'"'.$where);
					
		return $this->fetchAll($select)->toArray();
	}
}
?>