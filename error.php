<?php

require_once 'application/modules/default/library/sapp/Global.php';

if(!empty($_GET))
{
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>HRMS</title>
		<meta name="viewport" content="width=device-width,initial-scale=1.0">
		<link rel="shortcut icon" href="public/media/images/favicon.ico" />
		<link href="public/media/css/successstyle.css" rel="stylesheet">
		<link href='http://fonts.googleapis.com/css?family=Lato:400,700,400italic,300,300italic,100italic,100,700italic,900,900italic' rel='stylesheet' type='text/css'>
	</head>
	<body>
		<div class="container" >
			<div class="header"> <div class="logo"></div></div>
			<div class="content_wrapper" style="min-height:91px;">
				<?php if(sapp_Global::_decrypt($_GET['param']) == 'error'){?>
					<div class="error_mess">Installation failed,please re-install again.</div>
				<?php }else if(sapp_Global::_decrypt($_GET['param']) == 'db'){?>	
					<div class="error_mess">Database error occurred.Please reinstall the system to proceed.</div>
				<?php }else if(sapp_Global::_decrypt($_GET['param']) == 'tbl'){?>
					<div class="error_mess">Please install database first to proceed.</div>
				<?php }?>
			</div>
		</div>
	</body>
</html>
<?php }else{
header("Location: index.php");	
}?>