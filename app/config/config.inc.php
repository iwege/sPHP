<?php
if(!defined('IN_MANA'))
{
    exit('Access Denied');
}
$dbhost = '127.0.0.1';
$dbuser = 'root';
$dbpw = '';
$pconnect = '0';
$dbname = 'xx';
$prefix  = '';
$cookie_conf['pre'] = $cookiepre = 'xx_';
$cookie_conf['path'] = '/';
$cookie_conf['domain'] =  '';
$dbcharset ='utf8';
$page_num = 10;

define('KEY','a4a2781d6cefcd6957d25396c2ab7a99');
define('TPLDIR','template/default');
define('TEMPLATEID','0');

$tplrefresh =1;
date_default_timezone_set("Etc/GMT-8");
// login
$config['auto_login'] = 60*60*24;
$config['max_auto_login'] = 60*60*24*14;
$login_url = 'index.php?ac=login';

//upload
$config['upload_path'] = './upload';
$config['allowed_types'] = 'gif|jpg|png|jpeg|rar|zip|doc|xls|pdf|dwg';
$config['max_size']	= '100000000';
$config['max_width']  = '102400';
$config['max_height']  = '76800';


define('CHARSET', 'utf-8');
$config['siteurl'] = 'siteurl';
$config['sitename'] = 'sitename,fixed me in app/config/config.inc.php';
$config['version'] = 1;
$config['charset'] = 'utf-8';
$config['check_time'] = 100000;
$config['check_limit'] = 100;
$config['allow_mail_limit'] = 60;
$config['mail_limit'] = 10;
$config['mail'] = array(
	'smtp'=>array(
		array(
			'server'=>'',
			'port' => 25,
			'auth'=> 1,
			'from' => '',
			'auth_username' => '',
			'auth_password' => '',
		)
	),
	'mailsend'=>3,
	'maildelimiter'=>1,
);

