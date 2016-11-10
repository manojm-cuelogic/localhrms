<?php
	if(isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] == "cuelogic-phrms-dev.herokuapp.com") {
		define('GOOGLE_OAUTH_CLIENT_ID', '169777179868-t2riiahuib78befng57pd20va575sc74.apps.googleusercontent.com');
		define('GOOGLE_OAUTH_CLIENT_SECRET', 'gAjh18eYSw9WNSsDARHPVdN3');
		define('GOOGLE_OAUTH_REDIRECT_URI', 'http://cuelogic-phrms-dev.herokuapp.com');
		define("GOOGLE_SITE_NAME", 'http://cuelogic-phrms-dev.herokuapp.com');
	} else if(isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] == "cuelogic-phrms-stage.herokuapp.com") {
		//define('GOOGLE_OAUTH_CLIENT_ID', '480912168970-01j21q68hc7gpd9iokusaj65i9f967s0.apps.googleusercontent.com');
		//define('GOOGLE_OAUTH_CLIENT_SECRET', 'qs5tdg2ejNx9p-Rtc2KwTC4y');
        define('GOOGLE_OAUTH_CLIENT_ID', '169777179868-t2riiahuib78befng57pd20va575sc74.apps.googleusercontent.com');
        define('GOOGLE_OAUTH_CLIENT_SECRET', 'gAjh18eYSw9WNSsDARHPVdN3');
        define('GOOGLE_OAUTH_REDIRECT_URI', 'http://cuelogic-phrms-stage.herokuapp.com');
		define("GOOGLE_SITE_NAME", 'http://cuelogic-phrms-stage.herokuapp.com');
	} else {// exit;
        define('GOOGLE_OAUTH_CLIENT_ID', '40714559048-hoqd0de46k17ute143ptlvvrnaau6q6o.apps.googleusercontent.com');
        define('GOOGLE_OAUTH_CLIENT_SECRET', 'vQDHwWNw5K5F4vVY-hBIXybI');
        define('GOOGLE_OAUTH_REDIRECT_URI', 'http://hrmslocal.com/');
        define("GOOGLE_SITE_NAME", 'http://hrmslocal.com/');
	}
?>