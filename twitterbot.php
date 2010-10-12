<?php
require_once 'init.php';

$data=include 'mensa-export.php';

// Determine the current weekday
$weekdays=array(1=>'Mo', 'Di', 'Mi', 'Do', 'Fr');

$dayOfWeek=date('w');
if(!isset($weekdays[$dayOfWeek])) {
	return; // Nothing to do today
}

$todaysMeal=$data[$weekdays[$dayOfWeek]];

$token=unserialize(file_get_contents(OAUTH_TOKENFILE));
$twitter=new Zend_Service_Twitter(array(
	'username' => TWITTER_USERNAME,
	'consumerKey' => OAUTH_CONSUMERKEY,
	'consumerSecret' => OAUTH_CONSUMERSECRET,
	'accessToken' => $token
));

$twitter->status->update($todaysMeal);