<?php
	include_once "templates/base.php";
	session_start();
	require_once realpath(dirname(__FILE__) . '/../src/Google/autoload.php');
	/************************************************
	ATTENTION: Fill in these values! Make sure
	the redirect URI is to this page, e.g:
	http://localhost:8080/user-example.php
	************************************************/
	
	$client_id = '152514991884-ra5f4rjg9fahh9somqnr23dese9e34nt.apps.googleusercontent.com';
	$client_secret = 'LQXJ2gotiIPMqGkqzqzqBBA-';
	$redirect_uri = 'http://cuelogic-phrms-dev.herokuapp.com/index.php';
	$client = new Google_Client();
	$client->setClientId($client_id);
	$client->setClientSecret($client_secret);
	$client->setRedirectUri($redirect_uri);
	$client->setScopes('email');

	/************************************************
	If we're logging out we just need to clear our
	local access token in this case
	************************************************/

	if (isset($_REQUEST['logout'])) {
		unset($_SESSION['access_token']);
	}

	/************************************************
	If we have a code back from the OAuth 2.0 flow,
	we need to exchange that with the authenticate()
	function. We store the resultant access token
	bundle in the session, and redirect to ourself.
	************************************************/

	if (isset($_GET['code'])) {
		$client->authenticate($_GET['code']);
		$_SESSION['access_token'] = $client->getAccessToken();
		$redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
		header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
	}

	/************************************************
	If we have an access token, we can make
	requests, else we generate an authentication URL.
	************************************************/

	if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
		$client->setAccessToken($_SESSION['access_token']);
	} else {
		$authUrl = $client->createAuthUrl();
	}

	/************************************************
	If we're signed in we can go ahead and retrieve
	the ID token, which is part of the bundle of
	data that is exchange in the authenticate step
	- we only need to do a network call if we have
	to retrieve the Google certificate to verify it,
	and that can be cached.
	************************************************/

	if ($client->getAccessToken()) {
		$_SESSION['access_token'] = $client->getAccessToken();
		$token_data = $client->verifyIdToken()->getAttributes();
	}
	
	//echo pageHeader("User Query - Retrieving An Id Token");
	if (strpos($client_id, "googleusercontent") == false) {
		echo missingClientSecretsWarning();
		exit;
	}
	
	/*if (isset($authUrl)) {
	echo "<a class='login' href='" . $authUrl . "'>Connect Me!</a>";
	} else {
	echo "<a class='logout' href='?logout'>Logout</a>";
	}*/

	/*if (isset($token_data)) {
		var_dump($token_data);
	}*/
	
	return $authUrl;
?>