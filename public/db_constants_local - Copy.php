<?php
	if(isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] == "cuelogic-phrms-dev.herokuapp.com") {
		defined('HRMS_HOST') || define('HRMS_HOST','54.152.13.148');
		defined('HRMS_USERNAME') || define('HRMS_USERNAME','root');
		defined('HRMS_PASSWORD') || define('HRMS_PASSWORD','cuelogic');
		defined('HRMS_DBNAME') || define('HRMS_DBNAME','hrmsdev');
	} else if(isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] == "cuelogic-phrms-stage.herokuapp.com") {
		defined('HRMS_HOST') || define('HRMS_HOST','54.152.13.148');
		defined('HRMS_USERNAME') || define('HRMS_USERNAME','root');
		defined('HRMS_PASSWORD') || define('HRMS_PASSWORD','cuelogic');
		defined('HRMS_DBNAME') || define('HRMS_DBNAME','hrmsstage');
	} else {
        defined('HRMS_HOST') || define('HRMS_HOST','localhost');
		defined('HRMS_USERNAME') || define('HRMS_USERNAME','root');
		defined('HRMS_PASSWORD') || define('HRMS_PASSWORD','');
		defined('HRMS_DBNAME') || define('HRMS_DBNAME', 'cue_hrms');
        /*defined('HRMS_HOST') || define('HRMS_HOST','54.152.13.148');
        defined('HRMS_USERNAME') || define('HRMS_USERNAME','root');
        defined('HRMS_PASSWORD') || define('HRMS_PASSWORD','cuelogic');
        defined('HRMS_DBNAME') || define('HRMS_DBNAME','hrmsdev');*/
	}
?>
