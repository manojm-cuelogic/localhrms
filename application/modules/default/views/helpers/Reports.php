<?php

Class Zend_View_Helper_Reports extends Zend_View_Helper_Abstract{
	
	public function reports(){
		return $this;	
	}
	
	public function displayCandidatesData($emp_data, $column_key, $title=false){
		if(isset($emp_data[$column_key])){
			if(!$title && strlen($emp_data[$column_key])>25){
				echo substr($emp_data[$column_key],0,22).'...';
			}else{
				echo $emp_data[$column_key];
			}
		}else{
			echo '--';
		}
	}
	
	public function displayInterviewsData($req, $column_key, $title=false){
		if(isset($req[$column_key])){
			if(!$title && strlen($req[$column_key])>25){
				echo substr($req[$column_key],0,22).'...';
			}else{
				echo $req[$column_key];
			}
		}else{
			echo '--';
		}
	}
	
}