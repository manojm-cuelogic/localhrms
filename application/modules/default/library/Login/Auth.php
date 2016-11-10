<?php

class Login_Auth {
	
	private function __construct() {
		// private - should not be used
	}
	
	public static function _getAdapter($adapter,$options) {
		if (empty($adapter) || empty($options) || !is_array($options)) {
            return false;
        }
        if (!in_array($adapter,array('ldap','db','email'))) {
        	return false;
        }
		if (!array_key_exists('username',$options) ||  !array_key_exists('user_password',$options)) {
			return false;
		} 
		$username= $options['username'];
		$password= $options['user_password'];
        switch ($adapter) {
        	case 'ldap' :
        		$auth= new Zend_Auth_Adapter_Ldap($options['ldap'],$username,$password);
        		break;
        	case 'db' :
				$password=md5($password);        		
				$auth= new Zend_Auth_Adapter_DbTable($options['db'],
        									    	 'main_users',
        											 'employeeId',
        											 'emppassword');
        		$auth->setIdentity($username)->setCredential($password);
        		break;
        	case 'email' :
				$password=md5($password);        		
				$auth= new Zend_Auth_Adapter_DbTable($options['db'],
        									    	 'main_users',
        											 'emailaddress',
        											 'emppassword');
        		$auth->setIdentity($username)->setCredential($password);
        		break;	
        }
        
        //To create organization image session
        
        $orgInfoModel = new Default_Model_Organisationinfo();
		$getorgData = $orgInfoModel->getorgrecords();
		if(!empty($getorgData))
		{
			$id = $getorgData[0]['id'];
			$data = $orgInfoModel->getOrganisationData($id);
			
		    $organizationImg = new Zend_Session_Namespace('organizationinfo');
			if(!empty($data['org_image']))
			{
				$organizationImg->orgimg = $data['org_image'];
			}
		}
        return $auth;
	}
}