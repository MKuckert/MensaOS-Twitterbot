<?php
require_once 'init.php';

$config=array(
	'callbackUrl' => OAUTH_CALLBACKURL,
	'siteUrl' => Zend_Service_Twitter::OAUTH_BASE_URI,
	'consumerKey' => OAUTH_CONSUMERKEY,
	'consumerSecret' => OAUTH_CONSUMERSECRET
);

$consumer=new Zend_Oauth_Consumer($config);
session_start();

if(!empty($_GET)&&isset($_SESSION['TWITTER_REQUEST_TOKEN'])) {
	$token=$consumer->getAccessToken($_GET, unserialize($_SESSION['TWITTER_REQUEST_TOKEN']));
	//var_dump('<pre>', $token);
	$token=serialize($token);
	
	if(file_put_contents(OAUTH_TOKENFILE, $token)) {
		echo 'Stored token as '.OAUTH_TOKENFILE;
	}
	else {
		echo 'Failed to store token';
	}
	
	$_SESSION['TWITTER_REQUEST_TOKEN']=null;
}
else {
	$token=$consumer->getRequestToken();
	
	$_SESSION['TWITTER_REQUEST_TOKEN']=serialize($token);
	
	$consumer->redirect();
}

