<?php

/** 
 * Helper class to define lot of useful functions.
 * @author ramakrishna
 */
class sapp_Helper 
{
	/**
	 * This function is used in header to display left side menu of service desk.
	 * @param integer $login_id = id of the login user.
	 * @param string $call       = from where function is calling
	 * @return string HTML content
	 */
	public static function service_header($data,$call)
	{
		$login_id = $data->id;
		$pending_url = BASE_URL."servicerequests/index/t/".sapp_Global::_encrypt("1")."/v/".sapp_Global::_encrypt("17");
		$closed_url = BASE_URL."servicerequests/index/t/".sapp_Global::_encrypt("1")."/v/".sapp_Global::_encrypt("2");
		$cancel_url = BASE_URL."servicerequests/index/t/".sapp_Global::_encrypt("1")."/v/".sapp_Global::_encrypt("3");
		$reject_url = BASE_URL."servicerequests/index/t/".sapp_Global::_encrypt("1")."/v/".sapp_Global::_encrypt("16");
		$all_url = BASE_URL."servicerequests/index/t/".sapp_Global::_encrypt("1");
		$sd_req_model = new Default_Model_Servicerequests();
		$counts = $sd_req_model->getRequestsCnt($login_id,'request');        
				
		$pending_cnt = $closed_cnt = $cancel_cnt = $rejected_cnt = 0;
		if(count($counts) > 0)
		{
			foreach($counts as $cnt)
			{
				if($cnt['status'] != 'Closed' && $cnt['status'] != 'Cancelled' && $cnt['status'] != 'Rejected') $pending_cnt += $cnt['cnt'];
				if($cnt['status'] == 'Closed') $closed_cnt += $cnt['cnt'];
				if($cnt['status'] == 'Cancelled') $cancel_cnt += $cnt['cnt'];
				if($cnt['status'] == 'Rejected') $rejected_cnt += $cnt['cnt'];
			}
		}
		$html = '';
		if($call == 'helper')
		{
			$html .= '<div style="" class="side-menu div_mchilds_'.SERVICEDESK.' selected_menu_class">';
			$html .= '    <ul>';
		}
		$html .= '        <li class="acc_li"><span id="acc_li_toggle_'.SD_TRANS.'" class="acc_li_toggle" onclick="togglesubmenus('.SD_TRANS.')"><b>My request summary</b></span>';
		$html .= '            <ul>';
		$html .= '                <li menu-url="'.$all_url.'" parent-div="div_mchilds_'.SERVICEDESK.'" super-parent="main_parent_'.SERVICEDESK.'"  class="clickable_menu" primary_parent="'.SD_TRANS.'"><a href="'.(($call == 'menusettings')?"javascript:void(0);":$all_url).'" ><i class="span_sermenu">All</i> <b class="super_cnt">'.($pending_cnt+$cancel_cnt+$closed_cnt+$rejected_cnt).'</b></a></li>';
		$html .= '                <li menu-url="'.$pending_url.'" parent-div="div_mchilds_'.SERVICEDESK.'" super-parent="main_parent_'.SERVICEDESK.'"  class="clickable_menu" primary_parent="'.SD_TRANS.'"><a href="'.(($call == 'menusettings')?"javascript:void(0);":$pending_url).'" ><i class="span_sermenu">Open</i> <b class="super_cnt">'.$pending_cnt.'</b></a></li>';
		$html .= '                <li menu-url="'.$closed_url.'" parent-div="div_mchilds_'.SERVICEDESK.'" super-parent="main_parent_'.SERVICEDESK.'"  class="clickable_menu" primary_parent="'.SD_TRANS.'"><a href="'.(($call == 'menusettings')?"javascript:void(0);":$closed_url).'" ><i class="span_sermenu">Closed</i> <b class="super_cnt">'.$closed_cnt.'</b></a></li>';
		$html .= '                <li menu-url="'.$reject_url.'" parent-div="div_mchilds_'.SERVICEDESK.'" super-parent="main_parent_'.SERVICEDESK.'"  class="clickable_menu" primary_parent="'.SD_TRANS.'"><a href="'.(($call == 'menusettings')?"javascript:void(0);":$reject_url).'" ><i class="span_sermenu">Rejected</i> <b class="super_cnt">'.$rejected_cnt.'</b></a></li>';
		$html .= '                <li menu-url="'.$cancel_url.'" parent-div="div_mchilds_'.SERVICEDESK.'" super-parent="main_parent_'.SERVICEDESK.'"  class="clickable_menu" primary_parent="'.SD_TRANS.'"><a href="'.(($call == 'menusettings')?"javascript:void(0);":$cancel_url).'" ><i class="span_sermenu">Cancelled</i> <b class="super_cnt">'.$cancel_cnt.'</b></a></li>';
		$html .= '            </ul>';
		$html .= '        </li>';
		if($call == 'helper')
		{
			$html .= '    </ul>';
			$html .= '</div>';
		}
		if($data->is_orghead == 1)
			$html = '';
		$check_receiver = $sd_req_model->check_receiver($login_id, $data->businessunit_id);
		$check_reporting = $sd_req_model->check_reporting($login_id);
		$check_approver = $sd_req_model->check_approver($login_id);
		
		if($check_receiver == 'yes' && $check_reporting == 'yes')
		{            
			$html .= self::sd_req_summary($login_id,'rec_rept',$call);            
		}
		else if($check_approver == 'yes' && $check_reporting == 'yes')
		{            
			$html .= self::sd_req_summary($login_id,'rept_app',$call);
		}
		else if($check_receiver == 'yes')
		{            
			$html .= self::sd_req_summary($login_id,'receiver',$call);            
		}
		else if($check_reporting == 'yes')
		{            
			$html .= self::sd_req_summary($login_id,'reporting',$call);
		}
		else if($check_approver == 'yes')
		{            
			$html .= self::sd_req_summary($login_id,'approver',$call);
		}
		if($data->is_orghead == 1)
		{
			$html .= self::sd_all_summary($login_id,'org_head',$call);
		}
				
		return $html;
	}//end of service_header function
	
	/**
	 * This function is helper function to service_header to handle all request summary.
	 * @param integer $login_id  = id of login user
	 * @param string $context    = tells which type of call
	 * @param string $call       = from where function is calling
	 * @return string HTML content
	 */
	public static function sd_all_summary($login_id,$context,$call)
	{
		$sd_req_model = new Default_Model_Servicerequests();
		$url_arr = array();
		$html = "";
		if($context == 'rec_rept' || $context == 'receiver')
		{
			$grid_type = 7;
			
			$all_counts = $sd_req_model->getRequestsCnt($login_id,'all_rec_rept');
			
			$to_app_cnt = isset($all_counts['To management approve'])?$all_counts['To management approve']:0;
			$to_mapp_cnt = isset($all_counts['To manager approve'])?$all_counts['To manager approve']:0;
			$tot_toapp_cnt = $to_app_cnt + $to_mapp_cnt;
			$url_arr['All'] = array('url'=>  self::sd_url_builder($grid_type, ''),'count' => (isset($all_counts['all'])?$all_counts['all']:0));
			$url_arr['Pending'] = array('url'=>  self::sd_url_builder($grid_type, '1'),'count' => (isset($all_counts['Pending'])?$all_counts['Pending']:0));
			$url_arr['Closed'] = array('url'=>  self::sd_url_builder($grid_type, '2'),'count' => (isset($all_counts['Closed'])?$all_counts['Closed']:0));
			$url_arr['Cancelled'] = array('url'=>  self::sd_url_builder($grid_type, '3'),'count' => (isset($all_counts['Cancelled'])?$all_counts['Cancelled']:0));
			$url_arr['Overdue'] = array('url'=>  self::sd_url_builder($grid_type, '4'),'count' => (isset($all_counts['overdue'])?$all_counts['overdue']:0));
			$url_arr['Due today'] = array('url'=>  self::sd_url_builder($grid_type, '5'),'count' => (isset($all_counts['duetoday'])?$all_counts['duetoday']:0));
			$url_arr['To approve'] = array('url'=>  self::sd_url_builder($grid_type, '6'),'count' => $tot_toapp_cnt);
			$url_arr['Approved'] = array('url'=>  self::sd_url_builder($grid_type, '7'),'count' => (isset($all_counts['Approved'])?$all_counts['Approved']:0));
		}        
		if($context == 'org_head')
		{
			$grid_type = 9;
			
			$all_counts = $sd_req_model->getRequestsCnt($login_id,'org_head');
			
			$to_app_cnt = isset($all_counts['To management approve'])?$all_counts['To management approve']:0;
			$to_mapp_cnt = isset($all_counts['To manager approve'])?$all_counts['To manager approve']:0;
			$tot_toapp_cnt = $to_app_cnt + $to_mapp_cnt;
			
			$app1_cnt = isset($all_counts['Management approved'])?$all_counts['Management approved']:0;
			$app2_cnt = isset($all_counts['Manager approved'])?$all_counts['Manager approved']:0;
			$tot_app_cnt = $app1_cnt + $app2_cnt;
			
			$rej1_cnt = isset($all_counts['Management rejected'])?$all_counts['Management rejected']:0;
			$rej2_cnt = isset($all_counts['Manager rejected'])?$all_counts['Manager rejected']:0;
			$tot_rej_cnt = $rej1_cnt + $rej2_cnt;
			
			$close_cnt = isset($all_counts['Closed'])?$all_counts['Closed']:0;
			$rej_cnt = isset($all_counts['Rejected'])?$all_counts['Rejected']:0;
			$cl_rj_cnt = $close_cnt + $rej_cnt;
			
			$url_arr['All'] = array('url'=>  self::sd_url_builder($grid_type, ''),'count' => (isset($all_counts['all'])?$all_counts['all']:0));
			$url_arr['Open'] = array('url'=>  self::sd_url_builder($grid_type, '1'),'count' => (isset($all_counts['Open'])?$all_counts['Open']:0));
			$url_arr['Closed/Rejected'] = array('url'=>  self::sd_url_builder($grid_type, '22'),'count' => $cl_rj_cnt);
			$url_arr['Cancelled'] = array('url'=>  self::sd_url_builder($grid_type, '3'),'count' => (isset($all_counts['Cancelled'])?$all_counts['Cancelled']:0));
			$url_arr['Overdue'] = array('url'=>  self::sd_url_builder($grid_type, '4'),'count' => (isset($all_counts['overdue'])?$all_counts['overdue']:0));
			$url_arr['Due today'] = array('url'=>  self::sd_url_builder($grid_type, '5'),'count' => (isset($all_counts['duetoday'])?$all_counts['duetoday']:0));
			$url_arr['To approve'] = array('url'=>  self::sd_url_builder($grid_type, '6'),'count' => $tot_toapp_cnt);            
			$url_arr['Approved'] = array('url'=>  self::sd_url_builder($grid_type, '20'),'count' => $tot_app_cnt);
			$url_arr['Rejected'] = array('url'=>  self::sd_url_builder($grid_type, '21'),'count' => $tot_rej_cnt);
		}
		if(count($url_arr) > 0)
		{                
			if($call == 'helper')
			{
				$html .= '<div style="" class="side-menu div_mchilds_'.SERVICEDESK.' selected_menu_class"><ul>'; 
			}

			$html .='<li class="acc_li"><span id="acc_li_toggle_ars" class="acc_li_toggle" onclick=togglesubmenus("ars")><b>All request summary</b></span>';
			$html .='  <ul>';
		
			foreach($url_arr as $menu_name => $menu_arr)
			{
				$html .='    <li menu-url="'.$menu_arr['url'].'" parent-div="div_mchilds_'.SERVICEDESK.'" super-parent="main_parent_'.SERVICEDESK.'"  class="clickable_menu"  primary_parent="ars"><a href="'.(($call == 'menusettings')?"javascript:void(0);":$menu_arr['url']).'" ><i class="span_sermenu">'.$menu_name.'</i> <b class="super_cnt">'.$menu_arr['count'].'</b></a></li>';
			}
		
		
			$html .='  </ul>';
			$html .='</li>';

			if($call == 'helper')
			{
				$html .='</ul></div>';
			}        
		}
		return $html;
	}
	/**
	 * This function helps to build URL to service desk.
	 * @param int $grid_type    = type of grid 
	 * @param int $status       = status of service desk.
	 * @return string Formatted URL.
	 */
	public static function sd_url_builder($grid_type,$status)
	{
		if($status == '')
			return BASE_URL."servicerequests/index/t/".sapp_Global::_encrypt($grid_type);
		else 
			return BASE_URL."servicerequests/index/t/".sapp_Global::_encrypt($grid_type)."/v/".sapp_Global::_encrypt($status);
	}
	/**
	 * This function is helper function to service_header to handle my action summary.
	 * @param integer $login_id  = id of login user
	 * @param string $context    = tells which type of call
	 * @param string $call       = from where function is calling
	 * @return string HTML content
	 */
	public static function sd_req_summary($login_id,$context,$call)
	{
		$sd_req_model = new Default_Model_Servicerequests();
		$action_counts = array();
		$url_arr = array();
		$html = "";
		if($context == 'receiver')
		{
			$action_counts = $sd_req_model->getRequestsCnt($login_id,'receiver');
			$grid_type = 2;
			
			$mapp_cnt = isset($action_counts['Manager approved'])?$action_counts['Manager approved']:0;
			$app_cnt = isset($action_counts['Management approved'])?$action_counts['Management approved']:0;
			$rmapp_cnt = isset($action_counts['Manager rejected'])?$action_counts['Manager rejected']:0;
			$rapp_cnt = isset($action_counts['Management rejected'])?$action_counts['Management rejected']:0;
			$wmapp_cnt = isset($action_counts['To manager approve'])?$action_counts['To manager approve']:0;
			$wapp_cnt = isset($action_counts['To management approve'])?$action_counts['To management approve']:0;
			
			$pending_cnt = $mapp_cnt + $app_cnt + $rmapp_cnt + $rapp_cnt;
			$waiting_cnt = $wapp_cnt + $wmapp_cnt;
			
			$url_arr['All'] = array('url' => self::sd_url_builder($grid_type, ''),'count' => (isset($action_counts['all'])?$action_counts['all']:0),);
			$url_arr['Open'] = array('url' => self::sd_url_builder($grid_type, '1'),'count' => isset($action_counts['Open'])?$action_counts['Open']:0,);
			$url_arr['Pending'] = array('url' => self::sd_url_builder($grid_type, '8'),'count' => $pending_cnt,);
			$url_arr['Closed'] = array('url' => self::sd_url_builder($grid_type, '2'),'count' => (isset($action_counts['Closed'])?$action_counts['Closed']:0),);
			$url_arr['Rejected'] = array('url' => self::sd_url_builder($grid_type, '16'),'count' => (isset($action_counts['Rejected'])?$action_counts['Rejected']:0),);
			$url_arr['Cancelled'] = array('url' => self::sd_url_builder($grid_type, '3'),'count' => (isset($action_counts['Cancelled'])?$action_counts['Cancelled']:0),);
			$url_arr['Due today'] = array('url' => self::sd_url_builder($grid_type, '5'),'count' => (isset($action_counts['duetoday'])?$action_counts['duetoday']:0),);
			$url_arr['Overdue'] = array('url' => self::sd_url_builder($grid_type, '4'),'count' => (isset($action_counts['overdue'])?$action_counts['overdue']:0),);
			$url_arr['Sent for approval'] = array('url' => self::sd_url_builder($grid_type, '9'),'count' => $waiting_cnt,);
		}
		else if($context == 'reporting')
		{
			$grid_type = 4;
			$action_counts = $sd_req_model->getRequestsCnt($login_id,'reporting');
			
			$app_count = isset($action_counts['Manager approved'])?$action_counts['Manager approved']:0;
			$reject_cnt = isset($action_counts['Manager rejected'])?$action_counts['Manager rejected']:0;
			$rp_rj_cnt =  isset($action_counts['Rejected'])?$action_counts['Rejected']:0;
			$rp_cl_cnt =  isset($action_counts['Closed'])?$action_counts['Closed']:0;
						
			$cl_rj_cnt = $rp_cl_cnt + $rp_rj_cnt;
			
			$url_arr['All'] = array('url' => self::sd_url_builder($grid_type, ''),'count' => (isset($action_counts['all'])?$action_counts['all']:0),);
			$url_arr['To approve'] = array('url' => self::sd_url_builder($grid_type, '13'),'count' => (isset($action_counts['To manager approve'])?$action_counts['To manager approve']:0),);
			$url_arr['Approved'] = array('url' => self::sd_url_builder($grid_type, '18'),'count' =>$app_count,);
			$url_arr['Rejected'] = array('url' => self::sd_url_builder($grid_type, '19'),'count' => $reject_cnt,);
			$url_arr['Closed/Rejected'] = array('url' => self::sd_url_builder($grid_type, '22'),'count' => $cl_rj_cnt,);
		}
		else if($context == 'rept_app')
		{
			$grid_type = 8;
			$action_counts = $sd_req_model->getRequestsCnt($login_id,'rept_app');
			
			$mapp_cnt = isset($action_counts['Manager approved'])?$action_counts['Manager approved']:0;
			$app_cnt = isset($action_counts['Management approved'])?$action_counts['Management approved']:0;
			$mrej_cnt = isset($action_counts['Manager rejected'])?$action_counts['Manager rejected']:0;
			$rej_cnt = isset($action_counts['Management rejected'])?$action_counts['Management rejected']:0;
			$wmapp_cnt = isset($action_counts['To manager approve'])?$action_counts['To manager approve']:0;
			$wapp_cnt = isset($action_counts['To management approve'])?$action_counts['To management approve']:0;
			$close_cnt = isset($action_counts['Closed'])?$action_counts['Closed']:0;
			$reject_cnt = isset($action_counts['Rejected'])?$action_counts['Rejected']:0;
			
			$approved_cnt = $mapp_cnt + $app_cnt; 
			$tot_reject_cnt =  $mrej_cnt + $rej_cnt;
			$waiting_cnt = $wapp_cnt + $wmapp_cnt;
			$tot_close_cnt = $reject_cnt + $close_cnt;
						
			$url_arr['All'] = array('url' => self::sd_url_builder($grid_type, ''),'count' => (isset($action_counts['all'])?$action_counts['all']:0),);                        
			$url_arr['To approve'] = array('url' => self::sd_url_builder($grid_type, '6'),'count' => $waiting_cnt,);
			$url_arr['Approved'] = array('url' => self::sd_url_builder($grid_type, '20'),'count' => $approved_cnt,);
			$url_arr['Rejected'] = array('url' => self::sd_url_builder($grid_type, '21'),'count' => $tot_reject_cnt,);
			$url_arr['Closed/Rejected'] = array('url' => self::sd_url_builder($grid_type, '22'),'count' => $tot_close_cnt,);
		}
		else if($context == 'approver')
		{
			$grid_type = 5;
			$action_counts = $sd_req_model->getRequestsCnt($login_id,'approver');
						
			$app_cnt = isset($action_counts['Management approved'])?$action_counts['Management approved']:0;            
			$rej_cnt = isset($action_counts['Management rejected'])?$action_counts['Management rejected']:0;            
			$wapp_cnt = isset($action_counts['To management approve'])?$action_counts['To management approve']:0;
			$close_cnt = isset($action_counts['Closed'])?$action_counts['Closed']:0;
			$reject_cnt = isset($action_counts['Rejected'])?$action_counts['Rejected']:0;
												
			$tot_close_cnt = $reject_cnt + $close_cnt;
						
			$url_arr['All'] = array('url' => self::sd_url_builder($grid_type, ''),'count' => (isset($action_counts['all'])?$action_counts['all']:0),);                        
			$url_arr['To approve'] = array('url' => self::sd_url_builder($grid_type, '23'),'count' => $wapp_cnt,);
			$url_arr['Approved'] = array('url' => self::sd_url_builder($grid_type, '24'),'count' => $app_cnt,);
			$url_arr['Rejected'] = array('url' => self::sd_url_builder($grid_type, '25'),'count' => $rej_cnt,);
			$url_arr['Closed/Rejected'] = array('url' => self::sd_url_builder($grid_type, '22'),'count' => $tot_close_cnt,);
		}        
		else if($context == 'rec_rept')
		{
			$grid_type = 6;
			$action_counts = $sd_req_model->getRequestsCnt($login_id,'rec_rept');
			
			$mapp_cnt = isset($action_counts['Manager approved'])?$action_counts['Manager approved']:0;
			$app_cnt = isset($action_counts['Management approved'])?$action_counts['Management approved']:0;
			$rmapp_cnt = isset($action_counts['Manager rejected'])?$action_counts['Manager rejected']:0;
			$rapp_cnt = isset($action_counts['Management rejected'])?$action_counts['Management rejected']:0;
			$wmapp_cnt = isset($action_counts['To manager approve'])?$action_counts['To manager approve']:0;
			$wapp_cnt = isset($action_counts['To management approve'])?$action_counts['To management approve']:0;
			$mrejected_cnt = isset($action_counts['mrejected'])?$action_counts['mrejected']:0;
			$mclosed_cnt = isset($action_counts['mclosed'])?$action_counts['mclosed']:0;
			
			$pending_cnt = $mapp_cnt + $app_cnt + $rmapp_cnt + $rapp_cnt;
			$waiting_cnt = $wapp_cnt + $wmapp_cnt;
			$cl_rj_cnt = $mrejected_cnt + $mclosed_cnt;
			
			$url_arr['All'] = array('url' => self::sd_url_builder($grid_type, ''),'count' => (isset($action_counts['all'])?$action_counts['all']:0),);
			$url_arr['Open'] = array('url' => self::sd_url_builder($grid_type, '1'),'count' => isset($action_counts['Open'])?$action_counts['Open']:0,);
			$url_arr['Pending'] = array('url' => self::sd_url_builder($grid_type, '8'),'count' => $pending_cnt,);
			$url_arr['Closed'] = array('url' => self::sd_url_builder($grid_type, '2'),'count' => (isset($action_counts['Closed'])?$action_counts['Closed']:0),);
			$url_arr['Rejected'] = array('url' => self::sd_url_builder($grid_type, '16'),'count' => (isset($action_counts['Rejected'])?$action_counts['Rejected']:0),);
			$url_arr['Cancelled'] = array('url' => self::sd_url_builder($grid_type, '3'),'count' => (isset($action_counts['Cancelled'])?$action_counts['Cancelled']:0),);
			$url_arr['Due today'] = array('url' => self::sd_url_builder($grid_type, '5'),'count' => (isset($action_counts['duetoday'])?$action_counts['duetoday']:0),);
			$url_arr['Overdue'] = array('url' => self::sd_url_builder($grid_type, '4'),'count' => (isset($action_counts['overdue'])?$action_counts['overdue']:0),);
			$url_arr['Sent for approval'] = array('url' => self::sd_url_builder($grid_type, '9'),'count' => $waiting_cnt,);
			$url_arr['As a reporting manager'] = array('url' => '','count' => 0,);
			$url_arr['To approve'] = array('url' => self::sd_url_builder($grid_type, '10'),'count' => (isset($action_counts['to_approve'])?$action_counts['to_approve']:0),);
			$url_arr['Approved '] = array('url' => self::sd_url_builder($grid_type, '18'),'count' => (isset($action_counts['manager_approved'])?$action_counts['manager_approved']:0),);
			$url_arr['Rejected '] = array('url' => self::sd_url_builder($grid_type, '19'),'count' => (isset($action_counts['manager_rejected'])?$action_counts['manager_rejected']:0),);
			$url_arr['Closed/Rejected'] = array('url' => self::sd_url_builder($grid_type, '22'),'count' => $cl_rj_cnt,);
		}
		if(count($url_arr) > 0)
		{        
			if($call == 'helper')
			{
				$html .='<div style="" class="side-menu div_mchilds_'.SERVICEDESK.' selected_menu_class"><ul>';
			}

			$html .='<li class="acc_li"><span id="acc_li_toggle_mas" class="acc_li_toggle" onclick=togglesubmenus("mas")><b>My action summary</b></span>';
			$html .='  <ul>';

			foreach($url_arr as $menu_name => $menu_arr)
			{
				if($menu_arr['url'] != '')
					$html .='    <li menu-url="'.$menu_arr['url'].'" parent-div="div_mchilds_'.SERVICEDESK.'" super-parent="main_parent_'.SERVICEDESK.'"  class="clickable_menu" primary_parent="mas"><a href="'.(($call == 'menusettings')?"javascript:void(0);":$menu_arr['url']).'" ><i class="span_sermenu">'.$menu_name.'</i> <b class="super_cnt">'.$menu_arr['count'].'</b></a></li>';
				else 
					$html .= '<span><b>'.$menu_name.'</b></span>';
			}

			$html .='  </ul>';
			$html .='</li>';

			if($call == 'helper')
			{
				$html .='</ul></div>';
			}       
		}
		return $html;
	}
	/**
	 * This function is used in views of service desk related,this will help to reuse html tags in view files
	 * @param array $msg_array     = array of error messages
	 * @param object $form         = form object
	 * @param string $element      = name of the element
	 * @param string $imgtitle      = title of the image
	 * @param string $extra_class  = extra classes that will apply to master div tag.
	 * @param string $required     = required class name.
	 * @param array $popup_arr     = array of parameters that can form link for popup.
	 * @return string HTML content
	 */
	public static function sd_form_helper($msg_array,$form,$element,$imgtitle,$extra_class,$required,$popup_arr)
	{
		if($imgtitle !='')
		   $labelimg = "<img class='tooltip' title='".$imgtitle."' src='".DOMAIN."public/media/images/help.png' />";
		else
		   $labelimg = '';       		
?>
		<div class="new-form-ui <?php echo $extra_class;?>">
			<label class="<?php echo $required;?>"><?php echo $form->$element->getLabel();?> <?php echo $labelimg;?></label>
			<div class="division"><?php echo $form->$element; ?>
				<?php if(isset($msg_array[$element])){?>
					<span class="errors" id="errors-<?php echo $form->$element->getId(); ?>"><?php echo $msg_array[$element];?></span>
				<?php }?>
<?php 
					if(count($popup_arr) > 0)
					{                        
?>	
						<span class="add-coloum" onclick="displaydeptform('<?php echo BASE_URL.$popup_arr['popup_url'] ?>','<?php echo $popup_arr['popup_disp_name'];?>');"> <?php echo $popup_arr['popup_link_name'];?> </span>			
<?php       
					}
?>
			</div>
		</div>
<?php
	}//end of sd_form_helper function.
			
	/**
	 * This function is used for popups in views of service desk related,this will help to reuse popup container view files
	 * @param string $controllername = name of the controller
	 * @return string HTML content
	 */
	public static function popup_helper($controllername)
	{
?>
		<div id="<?php echo $controllername?>Container"  style="display: none; overflow: auto;">
			<iframe id="<?php echo $controllername?>Cont" class="business_units_iframe" frameborder="0"></iframe>
		</div>
<?php     	
	}// end of popup_helper 
	
	/**
	 * This function gives names of menu names
	 * @return array Array of grid menu names
	 */
	public static function sd_menu_names()
	{
		return array(1=> 'My Request ',2=>'My Action ',3=>'My Action ', 
					 4 => 'My Action ',5=> 'My Action ',6 => 'My Action ',
					 7=> 'My Action ',8 => 'My Action ',9 => 'All Request');
	}
	
	public static function sd_action_names()
	{
		return array(1 => 'Open',2 => 'Closed',3 => 'Cancelled',4 => 'Overdue',5 => 'Due today',
					6 => 'To approve',7 => 'Approved',8 => 'Pending',9 => 'Sent for approval',
					10 => 'To approve',11 => 'To approve',12 => 'Approved/Rejected',13 => 'To approve',
					14 => 'Approved/Rejected',15 => 'Pending',16 => 'Rejected',17 => 'Open',
					18 => 'Approved',19 => 'Rejected',22 => 'Closed/Rejected',20 => 'Approved',
					21 => 'Rejected',23 => 'To approve',24 => 'Approved',25 => 'Rejected');
	}
	
	public static function process_emp_excel($file_name)
	{        
		require_once 'Classes/PHPExcel.php';
		require_once 'Classes/PHPExcel/IOFactory.php';
		
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity())
		{
			$loginUserId = $auth->getStorage()->read()->id;
		}
				
		$emp_model = new Default_Model_Employee();
		$usersModel = new Default_Model_Usermanagement();
		$identity_code_model = new Default_Model_Identitycodes();
				
		$objReader = PHPExcel_IOFactory::createReaderForFile($file_name);
		$objPHPExcel = $objReader->load($file_name);
		//Read first sheet
		$sheet 	= $objPHPExcel->getSheet(0);

		// Get worksheet dimensions
		$sizeOfWorksheet = $sheet->getHighestDataRow();
		$highestColumn 	 = $sheet->getHighestDataColumn();
		if($sizeOfWorksheet > 1)
		{

			$column_salary_currency = 17;$column_salary_type = 18;$column_salary = 19;
			$arrReqHeaders = array(
				'Employee Id', 'Prefix', 'First Name', 'Last Name', 'Middle Name', 'Reporting manager employee ID', 'Work status', 'Project Name', 'Business Unit', 'Department', 'Functional Area', 'Technology', 'Role', 'Employment Status', 'Job Title','Joining Date', 'Total Exp', $column_salary_currency =>'Salary Currency', $column_salary_type =>'Pay Frequency', $column_salary =>'Current Salary', 'DOB', 'Gender', 'Skype ID', 'Official Email ID', 'Personal Email ID', 'Educational Qualification', 'Current address', 'Current country', 'Current state', 'Current city', 'Current pincode', 'Permanent address', 'Permanent Country', 'Permanent State', 'Permanent City', 'Permanent Pincode', 'Contact Number', 'Bank Account number', 'Pan No.', 'Driving Licence No.', 'PF No.', 'Passport No.', 'Emergency Contact Person 1', 'Emergency Contact Number 1', 'Relation with Emergency Contact 1', 'Emergency Contact Person 2', 'Emergency Contact Number 2', 'Relation with Emergency Contact 2', 'Maritial Status', 'Resigned/PIP', 'Resigned/PIP start Date', 'Resigned/PIP end Date', 'Prev Org HR ID');

			/*
			$arrReqHeaders = array(
				'Prefix','First name','Last name','Role Type','Email','Business Unit','Department','Reporting manager','Job Title' ,
				'Position','Employment Status','Date of joining','Date of leaving','Experience','Extension',
				'Work telephone number','Fax',$column_salary_currency => 'Salary Currency',
				$column_salary_type =>'Pay Frequency',$column_salary => 'Salary'
			);
			*/
								
			//Get first/header from excel
			$firstRow = $sheet->rangeToArray('A' . 1 . ':' . $highestColumn . 1, NULL, TRUE, TRUE);
			$arrGivenHeaders = $firstRow[0];
			
			$diffArray = array_diff_assoc($arrReqHeaders,$arrGivenHeaders);	
			$prefix_arr = $emp_model->getPrefix_emp_excel();
			$roles_arr = $emp_model->getRoles_emp_excel();
			$bu_arr = $emp_model->getBU_emp_excel();
			$dep_arr = $emp_model->getDep_emp_excel();
			$job_arr = $emp_model->getJobs_emp_excel();
			$positions_arr = $emp_model->getPositions_emp_excel();
			$users_arr = $emp_model->getUsers_emp_excel();
			$emp_stat_arr = $emp_model->getEstat_emp_excel();
			$dol_emp_stat_arr = $emp_model->getDOLEstat_emp_excel();
			$mng_roles_arr = $emp_model->getMngRoles_emp_excel();
			$emps_arr = $emp_model->getEmps_emp_excel();
			$emails_arr = $emps_arr['email'];
			$emp_ids_arr = $emps_arr['ids'];
			$emp_depts_arr = $emp_model->getEmpsDeptWise();
			$dept_bu_arr = $emp_model->getDeptBUWise();
			$pos_jt_arr = $emp_model->getPosJTWise();
			$currency_arr = $emp_model->getCurrency_excel();
			$salary_type_arr = $emp_model->getPayfrequency_excel();
			$city_arr = $emp_model->getCity_emp_excel();
			$state_arr = $emp_model->getState_emp_excel();
			$country_arr = $emp_model->getCountry_emp_excel();
			$marital_sts_arr = $emp_model->getMaritalStatus_emp_excel();
			$gen_arr = $emp_model->getGender_emp_excel();
			$work_sts_arr = $emp_model->getWorkStatus_emp_excel();

			$identity_codes = $identity_code_model->getIdentitycodesRecord();	
			$emp_identity_code = isset($identity_codes[0])?$identity_codes[0]['employee_code']:"";
			$trDb = Zend_Db_Table::getDefaultAdapter();
			// starting transaction
			$trDb->beginTransaction();
			try
			{
				//start of validations
				$ex_prefix_arr = array();
				$ex_firstname_arr = array();$ex_lastname_arr = array();
				$ex_role_arr = array();$ex_email_arr = array();
				$ex_bu_arr = array();$ex_dep_arr = array();$ex_rm_arr = array();$ex_jt_arr = array();$ex_pos_arr = array();
				$ex_es_arr = array();$ex_doj_arr = array();$ex_dol_arr = array();$ex_exp_arr = array();$ex_ext_arr = array();
				$ex_wn_arr = array();$ex_fax_arr = array();$tot_rec_cnt = 0;
				
				$err_msg = "";
				for($i=2; $i <= $sizeOfWorksheet; $i++ )
				{
					$rowData_org = $sheet->rangeToArray('A' . $i . ':' . $highestColumn . $i, NULL, TRUE, TRUE);
					$rowData = $rowData_org[0];    
					$rowData_cpy = $rowData;

					foreach($rowData_cpy as $rkey => $rvalue)
					{
						$rowData[$rkey] = trim($rvalue);
					}

					//start of mandatory checking

					if(empty($rowData[0])){
						$err_msg = "Employee Id column cannot be empty at row ".$i.".";
						break;
					}
					if(empty($rowData[1])){
						$err_msg = "Prefix column cannot be empty at row ".$i.".";
						break;
					}
					if(empty($rowData[2])){
						$err_msg = "First name column cannot be empty at row ".$i.".";
						break;
					}
					if(empty($rowData[3])){
						$err_msg = "Last name column cannot be empty at row ".$i.".";
						break;
					}
					if(empty($rowData[5])){
						$err_msg = "Reporting manager column cannot be empty at row ".$i.".";
						break;
					}
					if(empty($rowData[8])){
						$err_msg = "Business unit column cannot be empty at row ".$i.".";
						break;
					}
					if(empty($rowData[9])){
						$err_msg = "Department column cannot be empty at row ".$i.".";
						break;
					}
					if(empty($rowData[10])){
						$err_msg = "Functional Area column cannot be empty at row ".$i.".";
						break;
					}
					if(empty($rowData[11])){
						$err_msg = "Technology column cannot be empty at row ".$i.".";
						break;
					}
					if(empty($rowData[12])){
						$err_msg = "Role cannot be empty at row ".$i.".";
						break;
					}
					if(empty($rowData[13])){
						$err_msg = "Employment status column cannot be empty at row ".$i.".";
						break;
					}
					if(empty($rowData[14])){
						$err_msg = "Job Title column cannot be empty at row ".$i.".";
						break;
					}
					if(empty($rowData[15])){
						$err_msg = "Date of joining column cannot be empty at row ".$i.".";
						break;
					}
					if(empty($rowData[20])){
						$err_msg = "Date of birth column cannot be empty at row ".$i.".";
						break;
					}
					if(empty($rowData[21])){
						$err_msg = "Gender column cannot be empty at row ".$i.".";
						break;
					}
					if(empty($rowData[22])){
						$err_msg = "SkypeId column cannot be empty at row ".$i.".";
						break;
					}
					if(empty($rowData[23])){
						$err_msg = "Official Email ID cannot be empty at row ".$i.".";
						break;
					}
					if(!in_array($rowData[12], $mng_roles_arr) && empty($rowData[9])){
						$err_msg = "Department cannot be empty at row ".$i.".";
						break;
					}
					// end of mandatory checking
					// start of pattern checking
					if (!preg_match("/^(?=.*[a-zA-Z])([^ ][a-zA-Z0-9 ]*)$/", trim($rowData[1])) && !empty($rowData[1]) ){
						$err_msg = "Prefix is not a valid format at row ".$i.".";
						break;
					}
					if (!preg_match("/^([a-zA-Z.]+ ?)+$/", $rowData[2])  && !empty($rowData[2])){
						$err_msg = "First name is not a valid format at row ".$i.".";
						break;
					}
					if (!preg_match("/^([a-zA-Z.]+ ?)+$/", $rowData[3])  && !empty($rowData[3])){
						$err_msg = "Last name is not a valid format at row ".$i.".";
						break;
					}
					if (!preg_match("/^([a-zA-Z.]+ ?)+$/", $rowData[4])  && !empty($rowData[4])){
						$err_msg = "Last name is not a valid format at row ".$i.".";
						break;
					}
					if (!preg_match("/^[a-zA-Z]+?$/", $rowData[12])  && !empty($rowData[12])){
						$err_msg = "Role type is not a valid format at row ".$i.".";
						break;
					}
					if (!preg_match("/^(?!.*\.{2})[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/", $rowData[23])  && !empty($rowData[23])){
						$err_msg = "Official Email is not a valid format at row ".$i.".";                        
						break;
					}
					if (!preg_match("/^(?!.*\.{2})[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/", $rowData[24])  && !empty($rowData[24])){
						$err_msg = "Personal Email is not a valid format at row ".$i.".";                        
						break;
					}
					if(!preg_match("/^[a-zA-Z0-9\&\'\.\s]+$/", $rowData[8])  && !empty($rowData[8])){
						$err_msg = "Business unit is not a valid format at row ".$i.".";
						break;
					}
					if(!preg_match("/^[a-zA-Z0-9\&\'\.\s]+$/", $rowData[9])  && !empty($rowData[9])){
						$err_msg = "Department is not a valid format at row ".$i.".";
						break;
					}
					if(!preg_match("/^[a-zA-Z0-9\&\'\.\s]+$/", $rowData[5])  && !empty($rowData[5])){
						$err_msg = "Reporting manager is not a valid format at row ".$i.".";
						break;
					}
					if(!preg_match("/^[a-zA-Z][a-zA-Z0-9\s]*$/", $rowData[14])  && !empty($rowData[14])){
						$err_msg = "Job title is not a valid format at row ".$i.".";
						break;
					}
					if(!preg_match("/^(?=.*[a-zA-Z])([^ ][a-zA-Z0-9 ]*)$/", $rowData[13])  && !empty($rowData[13])){
						$err_msg = "Employment status is not a valid format at row ".$i.".";
						break;
					}
					if (!preg_match("/^(?!.*\.{2})[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/", $rowData[52])  && !empty($rowData[52])){
						$err_msg = "Previous HR email ID is not in a valid format at row ".$i.".";
						break;
					}

					$test_doj = '';
					if(!empty($rowData[15])){
						try{
							$var = $rowData[15];
							$date = str_replace('/', '-', $var);
							$test_doj = date('Y-m-d', strtotime($date));
						}
						catch (Exception $ex) {
							return array('status' => 'error' , 'msg' => "Date of joining is not a valid format at row ".$i.".");
						}
					}

					$dob = '';
					if(!empty($rowData[20])){
						try{
							$var = $rowData[20];
							$date = str_replace('/', '-', $var);
							$dob = date('Y-m-d', strtotime($date));
						}
						catch (Exception $ex) {
							return array('status' => 'error' , 'msg' => "Date of birth is not a valid format at row ".$i.".");
						}
					}

					if(!preg_match("/^[0-9]\d{0,1}(\.\d*)?$/", $rowData[16])  && !empty($rowData[16])){
						$err_msg = "Experience is not a valid format at row ".$i.".";
						break;
					}
					if(!preg_match("/^[a-zA-Z][a-zA-Z0-9]*$/", $rowData[$column_salary_currency])  && !empty($rowData[$column_salary_currency])){
						$err_msg = $arrReqHeaders[$column_salary_currency]." is not a valid format at row ".$i.".";
						break;
					}
					if(!preg_match("/^[a-zA-Z][a-zA-Z0-9]*$/", $rowData[$column_salary_type])  && !empty($rowData[$column_salary_type])){
						$err_msg = $arrReqHeaders[$column_salary_type]." is not a valid format at row ".$i.".";
						break;
					}
					if(!preg_match("/^([0-9]*\.?[0-9]{1,2})$/", $rowData[$column_salary])  && !empty($rowData[$column_salary])){
						$err_msg = $arrReqHeaders[$column_salary]." is not a valid format at row ".$i.".";
						break;
					}
					if(!empty($rowData[$column_salary]) && $rowData[$column_salary] == 0){
						$err_msg = $arrReqHeaders[$column_salary]." cannot be zero at row ".$i.".";
						break;
					}
					// end of pattern checking
					// start of checking existence in the system.
					if(!array_key_exists(strtolower($rowData[1]), $prefix_arr) && !empty($rowData[1])){
						$err_msg = "Unknown prefix at row ".$i.".";
						break;
					}
					if(!array_key_exists(strtolower($rowData[12]), $roles_arr)  && !empty($rowData[12])){
						$err_msg = "Unknown role type at row ".$i.".";
						break;
					}
					if(!array_key_exists(strtolower($rowData[8]), $bu_arr)  && !empty($rowData[8])){
						$err_msg = "Unknown business unit at row ".$i.".";
						break;
					}
					if(!array_key_exists(strtolower($rowData[9]), $dep_arr)  && !empty($rowData[9])){
						$err_msg = "Unknown department at row ".$i.".";
						break;
					}
					if(in_array(strtolower($rowData[23]),$emails_arr) && !empty($rowData[23])){
						$err_msg = "Email already exists at row ".$i.".";
						break;
					}
					if(!array_key_exists(strtolower($rowData[14]), $job_arr)  && !empty($rowData[14])){
						$err_msg = "Unknown job title at row ".$i.".";
						break;
					}
					if(!array_key_exists(strtolower($rowData[13]), $emp_stat_arr)  && !empty($rowData[13])){
						$err_msg = "Unknown employment status at row ".$i.".";
						break;
					}
					if(!array_key_exists(strtolower($rowData[$column_salary_currency]), $currency_arr)  && !empty($rowData[$column_salary_currency])){
						$err_msg = "Unknown ".  strtolower($arrReqHeaders[$column_salary_currency])." at row ".$i.".";
						break;
					}
					if(!array_key_exists(strtolower($rowData[$column_salary_type]), $salary_type_arr)  && !empty($rowData[$column_salary_type])){
						$err_msg = "Unknown ".  strtolower($arrReqHeaders[$column_salary_type])." at row ".$i.".";
						break;
					}
					if(!array_key_exists(strtolower($rowData[34]), $city_arr)  && !empty($rowData[34])){
						$err_msg = "Unknown permanent city at row ".$i.".";
						break;
					}
					if(!array_key_exists(strtolower($rowData[29]), $city_arr)  && !empty($rowData[29])){
						$err_msg = "Unknown current city at row ".$i.".";
						break;
					}
					if(!array_key_exists(strtolower($rowData[33]), $state_arr)  && !empty($rowData[33])){
						$err_msg = "Unknown permanent state at row ".$i.".";
						break;
					}
					if(!array_key_exists(strtolower($rowData[28]), $state_arr)  && !empty($rowData[28])){
						$err_msg = "Unknown current state at row ".$i.".";
						break;
					}
					if(!array_key_exists(strtolower($rowData[32]), $country_arr)  && !empty($rowData[32])){
						$err_msg = "Unknown permanent country at row ".$i.".";
						break;
					}
					if(!array_key_exists(strtolower($rowData[27]), $country_arr)  && !empty($rowData[27])){
						$err_msg = "Unknown current country at row ".$i.".";
						break;
					}
					if(!array_key_exists(strtolower($rowData[48]), $marital_sts_arr)  && !empty($rowData[48])){
						$err_msg = "Unknown marital status at row ".$i.".";
						break;
					}
					if(!array_key_exists(strtolower($rowData[21]), $gen_arr)  && !empty($rowData[21])){
						$err_msg = "Unknown gender at row ".$i.".";
						break;
					}
					if(!array_key_exists(strtolower($rowData[6]), $work_sts_arr)  && !empty($rowData[6])){
						$err_msg = "Unknown work status at row ".$i.".";
						break;
					}
					// end of checking existence in the system.
					
					if(!empty($rowData[9])){
						if(isset($dept_bu_arr[0]) && is_array($dept_bu_arr[0])){
							if(in_array(strtolower($rowData[9]),$dept_bu_arr[0]) && !empty($rowData[8])){
								$err_msg = "Business unit is not needed for this department '".$rowData[12]."' at row ".$i.".";
								break;
							}
							if(!in_array(strtolower($rowData[9]),$dept_bu_arr[0]) && empty($rowData[8])){
								$err_msg = "Business unit cannot be empty at row ".$i.".";
								break;
							}
						}
						if(!empty($rowData[8])){
							if(isset($dept_bu_arr[$bu_arr[strtolower($rowData[8])]]) && !in_array(strtolower($rowData[9]),$dept_bu_arr[$bu_arr[strtolower($rowData[8])]])  && !empty($rowData[8])){
								$err_msg = "Department does not belong to '".$rowData[8]."' business unit at row ".$i.".";
								break;
							}
						}
						
					}
				}//end of for loop
				
				
				if(!empty($err_msg))
					return array('status' => 'error' , 'msg' => $err_msg);
				$err_msg = "";
				
				
				for($i=2; $i <= $sizeOfWorksheet; $i++ ){
					$rowData_org = $sheet->rangeToArray('A' . $i . ':' . $highestColumn . $i, NULL, TRUE, TRUE);
					$rowData = $rowData_org[0];
					$rowData_cpy = $rowData;
					foreach($rowData_cpy as $rkey => $rvalue)
					{
						$rowData[$rkey] = trim($rvalue);
					}

					$ex_prefix_arr[] = $rowData[1]; 
					$ex_firstname_arr[] = $rowData[2];$ex_lastname_arr[] = $rowData[3];
					$ex_role_arr[] = $rowData[12];
					$ex_email_arr[$i] = $rowData[23]; $ex_bu_arr[] = $rowData[8]; $ex_dep_arr[] = $rowData[9];
					$ex_rm_arr[] = $rowData[5]; $ex_jt_arr[] = $rowData[14]; $ex_es_arr[] = $rowData[13];
					$ex_doj_arr[] = $rowData[15];
					$ex_exp_arr[] = $rowData[16];

					$tot_rec_cnt++;
				}
				
				foreach($ex_email_arr as $key1 => $value1)
				{
					$d = 0;
					foreach($ex_email_arr as $key2 => $value2)
					{
						if($key1 != $key2 && $value1 == $value2)
						{
							$err_msg = "Duplicate email entry at row ".$key2.".";
							$d++;
							break;
						}
					}
					if($d>0)
						break;
				}

				if(!empty($err_msg))
					return array('status' => 'error' , 'msg' => $err_msg);

				//end of validations
				//start of saving
				if($tot_rec_cnt > 0)
				{

					for($i=2; $i <= $sizeOfWorksheet; $i++ )
					{
						//$emp_id = $emp_identity_code.str_pad($usersModel->getMaxEmpId($emp_identity_code), 3, '0', STR_PAD_LEFT);
						$rowData_org = $sheet->rangeToArray('A' . $i . ':' . $highestColumn . $i, NULL, TRUE, TRUE);
						$rowData = $rowData_org[0];
						$rowData_cpy = $rowData;
						foreach($rowData_cpy as $rkey => $rvalue)
						{
							$rowData[$rkey] = trim($rvalue);
						}
						
						$emppassword = sapp_Global::generatePassword();
						$date_of_leaving = "";
						if($rowData[15] != '')
						{
							$date_of_leaving=$test_dol;
						}

						$test_doj = '';
						if(!empty($rowData[15])){
							try{
								$var = $rowData[15];
								$date = str_replace('/', '-', $var);
								$test_doj = date('Y-m-d', strtotime($date));
							}
							catch (Exception $ex) {
								return array('status' => 'error' , 'msg' => "Date of joining is not a valid format at row ".$i.".");
							}
						}
						$date_of_joining = $test_doj;

						//start of saving into user table
						$userfullname = $rowData[2].' '.$rowData[3];
						$user_data = array(
							'emprole' =>$roles_arr[strtolower($rowData[12])],
							'userfullname' => $userfullname,
							'firstname' => $rowData[2],
							'lastname' => $rowData[3],
							'middlename' => $rowdata[4],
							'emailaddress' => $rowData[23],
							'jobtitle_id'=> isset($job_arr[strtolower($rowData[14])])?$job_arr[strtolower($rowData[14])]:null,
							'modifiedby'=> $loginUserId,
							'modifieddate'=> gmdate("Y-m-d H:i:s"),
							'emppassword' => md5($emppassword),
							'employeeId' => $rowData[0],
							'modeofentry' => "Direct",
							'selecteddate' => $date_of_joining,
							'userstatus' => 'old',
						);
						$user_data['createdby'] = $loginUserId;
						$user_data['createddate'] = gmdate("Y-m-d H:i:s");
						$user_data['isactive'] = 1;

						$user_id = $usersModel->SaveorUpdateUserData($user_data, '');
						//end of saving into user table.
						//start of saving into employee table
						$data = array(  
							'user_id'=>$user_id,
							'reporting_manager'=>1,
							'emp_status_id'=>$emp_stat_arr[strtolower($rowData[13])],
							'businessunit_id'=>(!empty($rowData[23]))?$bu_arr[strtolower($rowData[8])]:0,
							'department_id'=>(!empty($rowData[8]))?$dep_arr[strtolower($rowData[9])]:null,
							'jobtitle_id'=>isset($job_arr[strtolower($rowData[14])])?$job_arr[strtolower($rowData[14])]:null, 
							'prefix_id'=> isset($prefix_arr[strtolower($rowData[1])])?$prefix_arr[strtolower($rowData[1])]:null,
							'date_of_joining'=>$date_of_joining,
							'years_exp'=>($rowData[16]=='')?null:$rowData[16],
							'modifiedby'=>$loginUserId,				
							'modifieddate'=>gmdate("Y-m-d H:i:s")
						);
					 
						$data['createdby'] = $loginUserId;
						$data['createddate'] = gmdate("Y-m-d H:i:s");;
						$data['isactive'] = 1;
						$emp_model->SaveorUpdateEmployeeData($data, '');
						//end of saving into employee table
					
						//start of saving into salary details
						$salary_data = array(
							'user_id' => $user_id,
							'currencyid' => isset($currency_arr[strtolower($rowData[$column_salary_currency])])?$currency_arr[strtolower($rowData[$column_salary_currency])]:null,
							'salarytype' => isset($salary_type_arr[strtolower($rowData[$column_salary_type])])?$salary_type_arr[strtolower($rowData[$column_salary_type])]:null,
							'salary' => !empty($rowData[$column_salary])?$rowData[$column_salary]:null,
							'isactive' => 1,
							'accountnumber' =>$rowData[37],
							'pf_number' =>$rowData[40],
							'pan_number' =>$rowData[38],
							'driver_license_number'=>$rowData[39],
							'modifiedby'=> $loginUserId,
							'modifieddate'=> gmdate("Y-m-d H:i:s"),
							'createdby'=> $loginUserId,
							'createddate'=> gmdate("Y-m-d H:i:s"),
						);
						$salary_model = new Default_Model_Empsalarydetails();
						$salary_model->SaveorUpdateEmpSalaryData($salary_data,'');
						//end of saving into salary details

						//start of saving employee communication data
						$empcommunication_data = array(
							'user_id'=> $user_id,
							'personalemail'=> $rowData[24],
							'perm_streetaddress'=>$rowData[31],
							'perm_country'=>$rowData[32],
							'perm_state'=>$rowData[33],
							'perm_city'=>$rowData[34],
							'perm_pincode'=>$rowData[35],
							'current_streetaddress'=>$rowData[26],
							'current_country'=>$rowData[27],
							'current_state'=>$rowData[28],
							'current_city'=>$rowData[29],
							'current_pincode'=>$rowData[30],
							'emergency_number_1'=>$rowData[43],
							'emergency_name_1'=>$rowData[42],
							'relation_emergency_1'=>$rowData[44],
							'emergency_number_2'=>$rowData[46],
							'emergency_name_2'=>$rowData[45],
							'relation_emergency_2'=>$rowData[47],
							'isactive'=>1,
							'modifiedby'=> $loginUserId,
							'modifieddate'=> gmdate("Y-m-d H:i:s"),
							'createdby'=> $loginUserId,
							'createddate'=> gmdate("Y-m-d H:i:s"),
						);
						$empcommunicationdetails_model = new Default_Model_Empcommunicationdetails();
						$empcommunicationdetails_model->SaveorUpdateEmpcommData($empcommunication_data,'');
						//end of saving employee communication data

						//start of saving employee education data
						$empeducation_data = array(
							'user_id'=>$user_id,
							'course'=> $rowData[25],
							'modifiedby'=> $loginUserId,
							'modifieddate'=> gmdate("Y-m-d H:i:s"),
							'createdby'=> $loginUserId,
							'createddate'=> gmdate("Y-m-d H:i:s"),
						);
						$empeducationaldetails_model = new Default_Model_Educationdetails();
						$empeducationaldetails_model->SaveorUpdateEducationDetails($empeducation_data, '');
						//end of saving employee education data

						//start of saving employee work data
						$empwork_data = array(
							'user_id'=>$user_id,
							'contact_number'=> $rowData[36],
							'skype_id'=> $rowData[22],
							'emp_designation'=> $rowData[14],
							'emp_fromdate'=> $date_of_joining,
							'emp_todate'=> gmdate("Y-m-d H:i:s"),
							'isactive'=> 1,
							'modifiedby'=> $loginUserId,
							'modifieddate'=> gmdate("Y-m-d H:i:s"),
							'createdby'=> $loginUserId,
							'createddate'=> gmdate("Y-m-d H:i:s"),
						);
						$empworkdetails_model = new Default_Model_Empworkdetails();
						$temp = $empworkdetails_model->SaveorUpdateEmpWorkData($empwork_data, '');

						//end of saving employee work data

						//start of saving employee experience data
						$empexperience_data = array(
							'user_id'=>$user_id,
							'hr_email_id'=> $rowData[52],
							'isactive'=> 1,
							'modifiedby'=> $loginUserId,
							'modifieddate'=> gmdate("Y-m-d H:i:s"),
							'createdby'=> $loginUserId,
							'createddate'=> gmdate("Y-m-d H:i:s"),
						);
						$empexperiencedetails_model = new Default_Model_Experiencedetails();
						$empexperiencedetails_model->SaveorUpdateEmployeeexperienceData($empexperience_data, '');
						//end of saving employee experience data

						$dob = '';
						if(!empty($rowData[20])){
							try{
								$var = $rowData[20];
								$date = str_replace('/', '-', $var);
								$dob = date('Y-m-d', strtotime($date));
							}
							catch (Exception $ex) {
								return array('status' => 'error' , 'msg' => "Date of birth is not a valid format at row ".$i.".");
							}
						}
						
						//start of saving employee personal data
						$emppersonal_data = array(
							'user_id'=>$user_id,
							'genderid'=> isset($gen_arr[strtolower($rowData[21])])?$gen_arr[strtolower($rowData[21])]:null,
							'maritalstatusid'=> isset($marital_sts_arr[strtolower($rowData[48])])?$marital_sts_arr[strtolower($rowData[48])]:null,
							'dob'=> $dob,
							'isactive'=> 1,
							'modifiedby'=> $loginUserId,
							'modifieddate'=> gmdate("Y-m-d H:i:s"),
							'createdby'=> $loginUserId,
							'createddate'=> gmdate("Y-m-d H:i:s"),
						);
						$emppersonaldetails_model = new Default_Model_Emppersonaldetails();
						$emppersonaldetails_model->SaveorUpdateEmpPersonalData($emppersonal_data, '');
						//end of saving employee personal data

						//start of saving employee visa data
						$empvisa_data = array(
							'user_id'=>$user_id,
							'passport_number'=> $rowData[41],
							'isactive'=> 1,
							'modifiedby'=> $loginUserId,
							'modifieddate'=> gmdate("Y-m-d H:i:s"),
							'createdby'=> $loginUserId,
							'createddate'=> gmdate("Y-m-d H:i:s"),
						);
						$empvisadetails_model = new Default_Model_Visaandimmigrationdetails();
						$empvisadetails_model->SaveorUpdatevisaandimmigrationDetails($empvisa_data, '');
						//end of saving employee visa data

						//start of saving employee workstatus data
						$empworkinfo_data = array(
							'user_id'=>$user_id,
							'work_status'=>  isset($work_sts_arr[strtolower($rowData[6])])?$work_sts_arr[strtolower($rowData[6])]:null,
							'project_name'=> $rowData[7],
							'functional_area'=> $rowData[10],
							'technology'=> $rowData[11],
							'modifiedby'=> $loginUserId,
							'modifieddate'=> gmdate("Y-m-d H:i:s"),
							'createdby'=> $loginUserId,
							'createddate'=> gmdate("Y-m-d H:i:s"),
						);
						$empworkinfo_model = new Default_Model_Empworkinfo();
						$empworkinfo_model->SaveorUpdateEmpWorkInfoData($empworkinfo_data, '');
						//end of saving employee workstatus data

						//start of mail
						/*$text = "<div style='padding: 0; text-align: left; font-size:14px; font-family:Arial, Helvetica, sans-serif;'>				
										<span style='color:#3b3b3b;'>Hello ".ucfirst($userfullname).",</span><br />

										<div style='padding:20px 0 0 0;color:#3b3b3b;'>You have been added to ". APPLICATION_NAME.". The login credentials for your Cuelogic HRMS account is your cuelogic gmail account Id.</div>
										<div style='padding:20px 0 10px 0;'>Please <a href='".BASE_URL."index' target='_blank' style='color:#b3512f;'>click here</a> to login  to your Cuelogic HRMS account.</div>

								</div>";
						$options['subject'] = APPLICATION_NAME.': Login Credentials';
						$options['header'] = 'Greetings from Cuelogic HRMS';
						$options['toEmail'] = $rowData[23];
						$options['toName'] = $userfullname;
						$options['message'] = $text;
						//$options['cron'] = 'yes';
						$result = sapp_Global::_sendEmail($options);*/
						//end of mail
					}//end of for loop

					//Loop for importing manager id into all users
					for($i=2; $i <= $sizeOfWorksheet; $i++ ) {

						$rowData_org = $sheet->rangeToArray('A' . $i . ':' . $highestColumn . $i, NULL, TRUE, TRUE);
						$rowData = $rowData_org[0];
						$rowData_cpy = $rowData;
						foreach($rowData_cpy as $rkey => $rvalue)
						{
							$rowData[$rkey] = trim($rvalue);
						}

						$employeeId = $rowData[0];
						$managerId = $rowData[5];
						if(strlen(trim($managerId)) <= 0)
							continue;

						$userModel = new Default_Model_Users;
						$managerUserModel = $userModel->getUserModelsFromEmployeeIds(array($managerId));
						if(!isset($managerUserModel[0])) {
							continue;
						}
						$managerId = $managerUserModel[0]['id'];

						$employeeUserModel = $userModel->getUserModelsFromEmployeeIds(array($employeeId));
						if(!isset($employeeUserModel[0])) {
							continue;
						}
						$employeeUserId = $employeeUserModel[0]['id'];

						if(!empty($rowData[9])){
							if(isset($emp_depts_arr[$dep_arr[strtolower($rowData[9])]]) && !in_array(strtolower($rowData[5]),$emp_depts_arr[$dep_arr[strtolower($rowData[9])]]) ){
								if(isset($emp_depts_arr[0]) && is_array($emp_depts_arr[0])){
									if(!in_array(strtolower($rowData[5]),$emp_depts_arr[0])){
										$err_msg = "Reporting manager does not belong to '".$rowData[9]."' department at row ".$i;
										break;
									}
								}
							}
						} else {
							if(isset($emp_depts_arr[0]) && is_array($emp_depts_arr[0])){
								if(!in_array(strtolower($rowData[5]),$emp_depts_arr[0])){
									$err_msg = "Reporting manager does not belong to management group at row ".$i.".";
									break;
								}
							}
						}


						$db = Zend_Db_Table::getDefaultAdapter();
						$qry = "update main_employees set reporting_manager = " . $managerId . " where user_id = ".$employeeUserId;
						$db->query($qry);
					}

					$trDb->commit();
					return array('status' =>"success",'msg' => 'Employees saved successfully.');
				}
				else
				{
					return array('status' => 'error' , 'msg' => "No records to save.");
				}
				//end of saving
			}
			catch(Exception $e)
			{
				print_r($e); exit;
				$trDb->rollBack();
				return array('status' => 'error' , 'msg' => "Something went wrong,please try again");
			}            
		}
		else 
		{
		   return array('status' => 'error' , 'msg' => "No records to save.");
		}
		
	}//end of process_emp_excel function
	
	/**
	 * 
	 * Function to redirect to employee screen if wizard configuration is completed
	 * @param array $wizardData
	 * @param string $flag
	 */
	
	public static function dispayAddEmployeeLink($wizardData,$flag) {
		$html = '';
			if($flag == 'hr') {
				if($wizardData['leavetypes'] == 2 && $wizardData['holidays']) {
					$html = "<div class='add_emp_new'><a href='".BASE_URL."employee'>Add Employee</a></div>";
				}
			}
			if($flag == 'superadmin') {
				if($wizardData['org_details'] == 2 && $wizardData['site_config'] && $wizardData['departments']) {
					$html = "<div class='add_emp_new'><a href='".BASE_URL."employee'>Add Employee</a></div>";
				}
			}
		return $html;	
	}

	/**
	** Function to display Policy documents categories
	** Based on privileges for View/Manage Policy documents, categories are listed as menu items
	** 1. get categories created for policy documents
	** 2. build menu with respective urls
	**/
	public static function viewPolicyDocuments($call)
	{

		/**
		* Instantiate categories model
		* to get categories and documents count for each category
		**/
		$categoriesModel = new Default_Model_Categories();
		$dataObj = $categoriesModel->getCategories('menu');

		$categoriesObj = $documentsObj = '';
		$documentsCntArr = array();

		if(!empty($dataObj))
		{
			$categoriesObj = $dataObj['res'];
			$documentsObj = $dataObj['docs'];
		
			/** 
			** looping through documents object
			** to build an array with category_id as index and documents count as value
			**/
			if(!empty($documentsObj))
			{
				for($i = 0; $i < sizeof($documentsObj); $i++)
				{
					$documentsCntArr[$documentsObj[$i]['category_id']] = $documentsObj[$i]['doccnt'];
				}
			}
		}
		$html = '';
		
		/**
		** looping through categories object
		** to build menu items under Organization > Policy documents
		** with documents count for each category/menu item
		**/
		if(!empty($categoriesObj))
		{
			$html .= '    <ul>';
			for($c = 0; $c < sizeof($categoriesObj); $c++)
			{
				$catId = $categoriesObj[$c]['id'];
				$url = BASE_URL.'policydocuments/id/'.$catId;

				$html .= '<li menu-url="'.$url.'" parent-div="div_mchilds_'.ORGANIZATION.'" super-parent="main_parent_'.ORGANIZATION.'" class="clickable_menu set_over_text" primary_parent="'.POLICY_DOCUMENTS.'"><a href="'.(($call == 'menusettings')?"javascript:void(0);":$url).'"><i class="span_sermenu">'.$categoriesObj[$c]['category'].'</i> ';
				
				if(isset($documentsCntArr[$catId]) && !empty($documentsCntArr[$catId]))
					$html .= '<b class="super_cnt">'.$documentsCntArr[$catId].'</b></a></li>';
				else
					$html .= '<b class="super_cnt">0</b></a></li>';
			}
			$html .= '    </ul>';
		}
		return $html;
	}

	/**
	** Function to display breadcrums for Policy documents module
	**/
	public static function policyDocsBreadcrum()
	{
		$actionName = $bredcrumUrl= $categoryName ='';
		$url = BASE_URL.'policydocuments';
		/**
		** to handle policy documents page urls
		**/
		$pd_array = array('id','cat','view','edit','add','addmultiple');
		$documentsModel = new Default_Model_Documents();
		
		$pageUrl = explode("/",$_SERVER['REQUEST_URI']);
		
		if(isset($pageUrl[4])&& in_array($pageUrl[4],$pd_array))
		{
			if($pageUrl[4] == 'id'){
				$bredcrumUrl = $url.'/id/'.$pageUrl[5];
				$tmpCatObj = $documentsModel->getCategoryById($pageUrl[5]);
				if(!empty($tmpCatObj))
				{
					$categoryName = $tmpCatObj['category'];
				}
			}
			else if($pageUrl[4] == 'add' && !isset($pageUrl[5]))
			{
					$actionName = '<span>'.ucfirst($pageUrl[4]).'</span>';
			}
			else if($pageUrl[4] == 'add' && $pageUrl[5] == 'cat' && !empty($pageUrl[6]))
			{
				$tmpCatObj = $documentsModel->getCategoryById($pageUrl[6]);
				if(!empty($tmpCatObj))
				{
					$categoryName = $tmpCatObj['category'];
					$bredcrumUrl = $url.'/id/'.$pageUrl[6];
					$actionName = '<span class="arrows">&rsaquo;</span><span>'.ucfirst($pageUrl[4]).'</span>';
				}										
				else
					$actionName = '<span>'.ucfirst($pageUrl[4]).'</span>';

			}
			else if($pageUrl[4] == 'addmultiple' && isset($pageUrl[5]) )
			{
				$tmpCatObj = $documentsModel->getCategoryById($pageUrl[5]);
				if(!empty($tmpCatObj))
				{
					$categoryName = $tmpCatObj['category'];
					$bredcrumUrl = $url.'/id/'.$pageUrl[5];
					$actionName = '<span class="arrows">&rsaquo;</span><span>Add Multiple Documents</span>';
				}										
				else
					$actionName = '<span>Add Multiple Documents</span>';
			}
			else if(($pageUrl[4] == 'edit' || $pageUrl[4] == 'view')  && !empty($pageUrl[6]))
			{
				$tmpCatObj = $documentsModel->getCategoryByDocId($pageUrl[6]);
				if(!empty($tmpCatObj))
				{
					$categoryName = $tmpCatObj['category'];
					$bredcrumUrl = $url.'/id/'.$tmpCatObj['id'];	
					$actionName = '<span class="arrows">&rsaquo;</span><span>'.ucfirst($pageUrl[4]).'</span>';
				}										
				else
					$actionName = '<span>'.ucfirst($pageUrl[4]).'</span>';

			}
			else
			{
				$actionName = '<span>'.ucfirst($pageUrl[4]).'</span>';
			}
		}
		$onclickUrl = "window.location='".BASE_URL."'";
		$breacrumHtml = '<div id="breadcrumdiv"> 
							<div class="breadcrumbs">
								<span onclick="'.$onclickUrl.'" class="firstbreadcrumb">Home</span> 	<span class="arrows">&rsaquo;</span> 
								<span>Organization</span> <span class="arrows">&rsaquo;</span> 
								<span>Policy Documents</span> <span class="arrows">&rsaquo;</span> 
								<a href="'.$bredcrumUrl.'">'.$categoryName.'</a> 
								'.$actionName.'				
							</div>    
						</div>';
		echo $breacrumHtml;
	}	
 /**
	 * 
	 * Function to add remove active class and inactive class for Configure Wizard
	 * This functionality is based on $controllerName and $actionName
	 * @param array $wizardData
	 */
	public static function modifyClass($wizardData)
	{
		$request = Zend_Controller_Front::getInstance();
		$controllerName = $request->getRequest()->getControllerName();
		$actionName = $request->getRequest()->getActionName();	
	?>		
				<?php if($wizardData['iscomplete'] == 1) {?>
						$(".configlater").show();
				<?php } ?>
				
				<?php if($controllerName == 'wizard') { ?>
					<?php if($wizardData['manage_modules'] == 2) {?>
						$(".manage_modules").removeClass('inactive').addClass('completed inactive');
						$(".manage_menu").removeClass('progress').addClass('completed_show');
						$(".manage_menu").html('Completed');
					<?php } ?>
					<?php if($wizardData['site_config'] == 2) {?>
						$(".site_config").removeClass('inactive').addClass('completed inactive');
						$(".config_site").removeClass('progress').addClass('completed_show');
						$(".config_site").html('Completed');
					<?php } ?>
					<?php if($wizardData['org_details'] == 2) {?>
						$(".organization").removeClass('inactive').addClass('completed inactive');
						$(".config_organization").removeClass('progress').addClass('completed_show');
						$(".config_organization").html('Completed');
					<?php } ?>
					<?php if($wizardData['departments'] == 2) {?>
						$(".businessunit").removeClass('inactive').addClass('completed inactive');
						$(".config_dept").removeClass('progress').addClass('completed_show');
						$(".config_dept").html('Completed');
					<?php } ?>
					<?php if($wizardData['servicerequest'] == 2) {?>
						$(".servicerequest").removeClass('inactive').addClass('completed inactive');
						$(".config_request").removeClass('progress').addClass('completed_show');
						$(".config_request").html('Completed');
					<?php } ?>
					<?php if($wizardData['iscomplete'] == 1) {?>
						$(".configlater").show();
					<?php } ?>

					<?php if($actionName == 'managemenu') { ?>
						$(".manage_modules").removeClass('inactive');
						$("#manage_modules").removeAttr("onclick");
						$( "#manage_modules" ).unbind( "click");
					<?php } else if($actionName == 'configuresite') {?>
						$(".site_config").removeClass('inactive');
						$("#site_config").removeAttr("onclick");
						$("#site_config" ).unbind( "click");
					<?php } else if($actionName == 'configureorganisation') { ?>
						$(".organization").removeClass('inactive');
						$("#organization").removeAttr("onclick");
						$("#organization" ).unbind("click");
					<?php } else if($actionName == 'configureunitsanddepartments') { ?>
						$(".businessunit").removeClass('inactive');
						$("#business_unit").removeAttr("onclick");
						$("#business_unit" ).unbind( "click");
					<?php } else if($actionName == 'configureservicerequest') {?>
						$(".servicerequest").removeClass('inactive');
						$("#service_request").removeAttr("onclick");
						$( "#service_request" ).unbind( "click");
					<?php }?>
					
				<?php } else { ?>
					<?php if($wizardData['leavetypes'] == 2) {?>
						$(".leave_types").removeClass('inactive').addClass('completed inactive');
						$(".config_leaves").removeClass('progress').addClass('completed_show');
						$(".config_leaves").html('Completed');
					<?php } ?>
					<?php if($wizardData['holidays'] == 2) {?>
						$(".holidays").removeClass('inactive').addClass('completed inactive');
						$(".config_holidays").removeClass('progress').addClass('completed_show');
						$(".config_holidays").html('Completed');
					<?php } ?>
					<?php if($wizardData['perf_appraisal'] == 2) {?>
						$(".category").removeClass('inactive').addClass('completed inactive');
						$(".config_category").removeClass('progress').addClass('completed_show');
						$(".config_category").html('Completed');
					<?php } ?>
					
					<?php if($actionName == 'configureleavetypes') { ?>
						$(".leave_types").removeClass('inactive');
						$("#leave_types").removeAttr("onclick");
						$( "#leave_types" ).unbind( "click");
					<?php } else if($actionName == 'configureholidays') {?>
						$(".holidays").removeClass('inactive');
						$("#holidays").removeAttr("onclick");
						$( "#holidays" ).unbind( "click");
					<?php } else if($actionName == 'configureperformanceappraisal') { ?>
						$(".category").removeClass('inactive');
						$("#category").removeAttr("onclick");
						$( "#category" ).unbind( "click");
					<?php } ?>
						
				<?php }?>
	<?php 	
	}
	//restrict time management module for external users & check module is enable or not
	public static function checkTmEnable()
	{
		$userModel = new Timemanagement_Model_Users();
		$checkTmEnable = $userModel->checkTmEnable();
		$auth = Zend_Auth::getInstance();
		$loginuserGroup = '';
		$result = 1;
		if($auth->hasIdentity())
			$loginuserGroup = $auth->getStorage()->read()->group_id;
			
		if(!$checkTmEnable || $loginuserGroup == USERS_GROUP){
			$result = 0;
		}	
		return $result;
	}
	
	public static function time_diff_string($from, $to = "", $full = false) {

		if($to == "") $to = date("Y-m-d H:i:s");
		//echo $from ." == " . $to;
	    $from = new DateTime($from);
	    $to = new DateTime($to);
	    $diff = $to->diff($from);

	    $diff->w = floor($diff->d / 7);
	    $diff->d -= $diff->w * 7;

	    $string = array(
	        'y' => 'year',
	        'm' => 'month',
	        'w' => 'week',
	        'd' => 'day',
	        'h' => 'hour',
	        'i' => 'minute',
	        's' => 'second',
	    );
	    foreach ($string as $k => &$v) {
	        if ($diff->$k) {
	            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
	        } else {
	            unset($string[$k]);
	        }
	    }

	    if (!$full) $string = array_slice($string, 0, 1);
	    return $string ? implode(', ', $string) . ' ago' : 'just now';
	}



	public static function sendGoalUpdateNotification($goalId, $type="goalupdate", $options= array()){

		$userModel = new Default_Model_Users();
		$goalModel = new Default_Model_Goal();

		$goalDetails = $goalModel->getSingleGoalById($goalId);
		$goalOwner = $goalModel->getGoalOwnerName($goalId);

		$goalFollowersIds= array();
		
		if($type != 'goaladded'){
			
			$goalFollowers = $goalModel->getGoalFollowers($goalId);

			if(is_array($goalFollowers) && count($goalFollowers) > 0) {
			
				foreach ($goalFollowers as $key => $value) {
					$goalFollowersIds []= $value['userId'];
				}
			}
		}

		
		//get goal assessors
		$assessors =array();
		$assessorsIds =array();
		
		if( ($type == 'goalassessment' || $type == 'milestoneassessment') && $type != 'goaladded'){

			$assessors = $goalModel->getGoalAssessmentAssignee($goalId);


			if(is_array($assessors) && count($assessors) > 0) {
		
				foreach ($goalFollowers as $key => $value) {
					$assessorsIds []= $value['userId'];
				}
			}
		}

		
		$userIds = array();
		
		//get immediate managers
		
		//for testing
		//$users =$userModel->getImmediateManager(5);
		 


		$users =$userModel->getImmediateManager($goalDetails[0]['owner_id']);

		$managerId= array();
		
		if(is_array($users) && count($users) > 0) {
			$managerId[]= $users[0]['user_id'];

		}
		
		//$userIds = array_merge($goalFollowersIds, $assessorsIds, $managerId));

		$includeOwner= array();
		$includeOwner[]=$goalDetails[0]['owner_id'];

		if(($type == 'goaladded') && $goalDetails[0]['owner_id'] ==  $goalDetails[0]['createdby']) {
			
			$includeOwner= array();
		}

		//for owner change, include old owner
		if($type == 'goalownerchanged') {
			$includeOwner[]= $options['oldOwner'];
		}

		$userIds = $goalFollowersIds + $assessorsIds + $managerId + $includeOwner;

		$userDetails =$userModel->getUserDetailsByID($userIds);

		$options = array();

		$message = '';
		

		switch ($type) {
			
			
			case 'goaladded':
				$options['subject'] = APPLICATION_NAME.' Goal Added';
				$options['header'] = APPLICATION_NAME.' Goal Added';
				$message = 'A new goal has been added';					
				break;
			
			case 'goalarchieved':
				$options['subject'] = APPLICATION_NAME.' Goal Archieved';
				$options['header'] = APPLICATION_NAME.' Goal Archieved';
				$message = 'Goal has been archieved';					
				break;
			
			case 'milestoneadded':
					
				$options['subject'] = APPLICATION_NAME.' New milestone added';
				$options['header'] = APPLICATION_NAME.' New milestone added';
				$message = 'New Milestone added for goal';

				break;
			
			case 'milestoneupdated':

				$options['subject'] = APPLICATION_NAME.' milestone updated';
				$options['header'] = APPLICATION_NAME.' New milestone updated';
				$message = 'Milestone updated for goal';

				break;
			
			case 'milestonecompleted':

				$options['subject'] = APPLICATION_NAME.' Milestone completed';
				$options['header'] = APPLICATION_NAME.' Milestone completed';
				$message = 'Milestone completed for goal';
				
				break;
			
			case 'goalassessment':
				
				$options['subject'] = APPLICATION_NAME.' Goal assessment added';
				$options['header'] = APPLICATION_NAME.' Goal assessment added';
				$message = 'Assessment added for goal';
				
				break;

			case 'goalownerchanged':
				
				$options['subject'] = APPLICATION_NAME.' Goal owner changed';
				$options['header'] = APPLICATION_NAME.' Goal owner changed ';
				$message = 'Goal owner changed';
				
				break;



			case 'milestoneassessment':
							
				$options['subject'] = APPLICATION_NAME.' Milestone assessment added';
				$options['header'] = APPLICATION_NAME.' Milestone assessment added';
				$message = 'Milestone assessment added for goal';
							
				break;

			case 'goalcomment':
										
				$options['subject'] = APPLICATION_NAME.' comment for a goal';
				$options['header'] = APPLICATION_NAME.' comment for a goal';
				$message = 'A new comment has been added for a goal';
							
				break;

			case 'milestonecomment':
										
				$options['subject'] = APPLICATION_NAME.' comment for a milestone';
				$options['header'] = APPLICATION_NAME.' comment for a milestone';
				$message = 'A new comment has been added for a milestone';
							
				break;

			
			default:
				$options['subject'] = APPLICATION_NAME.' Goal Updated';
				$options['header'] = APPLICATION_NAME.' Goal Updated';
				$message = 'Goal has been updated';
				break;
		}


		$result= array();
		if(is_array($userDetails) && count($userDetails) > 0) {

			foreach ($userDetails as $key => $user) {
				
				$options['toName'] = $user['userfullname'];
				//$options['toEmail'] =  $user['emailaddress'];
				$options['toEmail'] = 'rahul.palake@cuelogic.co.in';
				$options['message'] = "<div>Hello ".$user['userfullname'].",</div>
										<div>$message </div> <div> Goal Owner: <b>".$goalOwner[0]['userfullname']."</b></div>
										<div>Goal:<a style='color: rgb(172, 88, 26); text-decoration: none;' href=".BASE_URL."goal/view/id/".$goalId.">".$goalDetails[0]['name']."</a></div>";
				
				//$res = sapp_Mail::_email($options);

				if($res){

					$result['success'][]=$user['emailaddress'];
				} else {
					$result['error'][]=$user['emailaddress'];
				}
			}

			return $result;
		}
	}


}//end of class