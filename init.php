<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL);

define('TWITTER_USERNAME', 'YourUsername');
define('OAUTH_CONSUMERKEY', 'YourConsumerKey');
define('OAUTH_CONSUMERSECRET', 'YourConsumerSecret');
define('OAUTH_CALLBACKURL', 'YourCallbackUrl');
define('OAUTH_TOKENFILE', realpath('tokens').'/token');

// Include zend framework
set_include_path(get_include_path().PATH_SEPARATOR.realpath('..'));
require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance();
