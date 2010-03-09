<?php
ignore_user_abort(true);
define('TWITTER_USER', 'YourTwitterUserHere');
define('TWITTER_PASS', 'YourTwitterPasswordHere');

$data=include 'mensa-export.php';

// Determine the current weekday
$weekdays=array(1=>'Mo', 'Di', 'Mi', 'Do', 'Fr');

$dayOfWeek=date('w');
if(!isset($weekdays[$dayOfWeek])) {
	return; // Nothing to do today
}

$todaysMeal=$data[$weekdays[$dayOfWeek]];

// Include zend framework
set_include_path(get_include_path().PATH_SEPARATOR.'/path/to/zend-framework/');
require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance();


$twitter=new Zend_Service_Twitter(TWITTER_USER, TWITTER_PASS);
$response=$twitter->status->update($todaysMeal);
