<?php
if (!defined('IN_MANA')) {
	exit("no right");
}


# App custom bootstrap
require_once API_ROOT . 'app/bootstrap.php';


# check file is exist
if (!file_exists(API_ROOT . 'app/controller/' . $file . '.func.php')) {
	exit('no file');
}


# require controller 
require_once API_ROOT . 'app/controller/' . $file . '.func.php';
