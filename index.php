<?php
define('IN_MANA', true);
define('API_ROOT', dirname(__FILE__) . '/');

#error_reporting(E_ALL & ~E_NOTICE);
error_reporting(0);
require_once API_ROOT . '/source/lib/common.inc.php';

if (file_exists(API_ROOT.'source/bootstrap.php')) {
	require_once API_ROOT.'source/bootstrap.php';
}

?>
