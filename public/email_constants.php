<?php 
if(isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] =="hrms.cuelogic.co.in"){
defined('EMAIL_LEAVE_CC') || define('EMAIL_LEAVE_CC','hr@cuelogic.co.in,accounts@cuelogic.co.in');
defined('EMAIL_HR_CC') || define('EMAIL_HR_CC','hr@cuelogic.co.in');
}
else{
defined('EMAIL_LEAVE_CC') || define('EMAIL_LEAVE_CC','manoj.mahamunkar@cuelogic.co.in');
defined('EMAIL_HR_CC') || define('EMAIL_HR_CC','manoj.mahamunkar@cuelogic.co.in');
}?>