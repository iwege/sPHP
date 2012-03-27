<?php
if (!defined('IN_MANA')) {
	exit("no right");
}


//error_reporting(E_ALL);
$ac = $do = $file = '';
$ac = getgpc('ac');
$do = getgpc('do');



if (!$ac) {
	$ac = 'home';
}
# action mapping
$controllerMap = array();


$file = $controllerMap[$ac] ? $controllerMap[$ac]:$ac;
# for flash uploader
if ($file  == 'file' || ($file == 'user' && $do=='avatar' || ($do=='rdfile' && $file=="task"))) {
	if (getgpc("PHPSESSID","P")&& getgpc("xx_info","P")) {
		$_COOKIE['PHPSESSID'] = getgpc("PHPSESSID",'P'); 
		$_COOKIE['xx_info'] = getgpc("xx_info",'P');
		session_id($_COOKIE['PHPSESSID']);
	}
}

session_start();

# if action is login
if ($ac == 'login') {
	$do = 'login';
}
