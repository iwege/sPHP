<?php
if(!defined('IN_MANA'))
{
    exit('Access Denied');
}
//error_reporting(0);

$mtime = explode(' ', microtime());
$starttime = $mtime[1] + $mtime[0];

define('NOROBOT', TRUE);

define('MAGIC_QUOTES_GPC',get_magic_quotes_gpc());
$timestamp = intval(time());

# php兼容 by iwege
if(PHP_VERSION < '4.1.0') {
	$_GET = &$HTTP_GET_VARS;
	$_POST = &$HTTP_POST_VARS;
	$_COOKIE = &$HTTP_COOKIE_VARS;
	$_SERVER = &$HTTP_SERVER_VARS;
	$_ENV = &$HTTP_ENV_VARS;
	$_FILES = &$HTTP_POST_FILES;
}

require_once API_ROOT.'app/config/config.inc.php';
require_once API_ROOT.'source/lib/common.func.php';
require_once API_ROOT.'source/lib/template.func.php';


define('ONLINEIP',getUserIp());

unset($GLOBALS, $_ENV, $HTTP_GET_VARS, $HTTP_POST_VARS, $HTTP_COOKIE_VARS, $HTTP_SERVER_VARS, $HTTP_ENV_VARS);

$_GET		= daddslashes($_GET, 1, TRUE);
$_POST		= daddslashes($_POST, 1, TRUE);
$_COOKIE	= daddslashes($_COOKIE, 1, TRUE);
$_SERVER	= daddslashes($_SERVER);
$_FILES		= daddslashes($_FILES);
$_REQUEST	= daddslashes($_REQUEST, 1, TRUE);


# 增加注入防御
if (isset($_REQUEST['GLOBALS']) OR isset($_FILES['GLOBALS'])) {
    die('Request tainting attempted.');
}

# 增加url过滤
if(!empty($_SERVER['REQUEST_URI'])) {
	$temp = urldecode($_SERVER['REQUEST_URI']);
	if(strpos($temp, '<') !== false || strpos($temp, '"') !== false)
		exit('Request Bad url');
}

# 过滤请求 by discuz 7
foreach(array('_COOKIE', '_POST', '_GET') as $_request) {
	foreach($$_request as $_key => $_value) {
		$_key{0} != '_' && $$_key = daddslashes($_value);
	}
}

# 过滤cookie内容 
$_MCOOKIE = array();
$prelen = strlen($cookiepre);
foreach($_COOKIE as $k => $v) {
	if(substr($k, 0, $prelen) == $cookiepre) {
		$_MCOOKIE[(substr($k, $prelen))] = MAGIC_QUOTES_GPC ? $v : daddslashes($v);
	}
}

unset($prelen,$_key,$_value,$_request);
# get form hash

require_once API_ROOT.'source/lib/Autoloader.class.php';

$db = new database();
$db->connect($dbhost, $dbuser, $dbpw, $dbname, $pconnect);
unset($dbhost,$dbuser,$dbpw,$dbname);

?>
