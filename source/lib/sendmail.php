<?php
if (!defined('IN_MANA')) {
    exit('Access Denied');
}
//$config['mail']['mailsend'] = 3;
function sendmail($toemail, $subject, $message, $from = '') {
	global $config;
	if(!is_array($config['mail'])) {
		$config['mail'] = unserialize($config['mail']);
	}
	$config['mail']['server'] = $config['mail']['port'] = $config['mail']['auth'] = $config['mail']['from'] = $config['mail']['auth_username'] = $config['mail']['auth_password'] = '';
	if($config['mail']['mailsend'] != 1) {
		$smtpnum = count($config['mail']['smtp']);
		if($smtpnum) {
			$rid = rand(0, $smtpnum-1);
			$smtp = $config['mail']['smtp'][$rid];
			$config['mail']['server'] = $smtp['server'];
			$config['mail']['port'] = $smtp['port'];
			$config['mail']['auth'] = $smtp['auth'] ? 1 : 0;
			$config['mail']['from'] = $smtp['from'];
			$config['mail']['auth_username'] = $smtp['auth_username'];
			$config['mail']['auth_password'] = $smtp['auth_password'];
		}
	}
	$message = preg_replace("/href\=\"(?!http\:\/\/)(.+?)\"/i", 'href="'.$config['siteurl'].'\\1"', $message);
 
$message = <<<EOT
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=$config[charset]">
<title>$subject</title>
</head>
<body>
$subject<br />
$message
</body>
</html>
EOT;
 
	$maildelimiter = $config['mail']['maildelimiter'] == 1 ? "\r\n" : ($config['mail']['maildelimiter'] == 2 ? "\r" : "\n");
	$mailusername = isset($config['mail']['mailusername']) ? $config['mail']['mailusername'] : 1;
	$config['mail']['port'] = $config['mail']['port'] ? $config['mail']['port'] : 25;
	$config['mail']['mailsend'] = $config['mail']['mailsend'] ? $config['mail']['mailsend'] : 1;
 
	if($config['mail']['mailsend'] == 3) {
		$email_from = empty($from) ? $config['adminemail'] : $from;
	} else {
		$email_from = $from == '' ? '=?'.CHARSET.'?B?'.base64_encode($config['sitename'])."?= <".$config['adminemail'].">" : (preg_match('/^(.+?) \<(.+?)\>$/',$from, $mats) ? '=?'.CHARSET.'?B?'.base64_encode($mats[1])."?= <$mats[2]>" : $from);
	}
 
	$email_to = preg_match('/^(.+?) \<(.+?)\>$/',$toemail, $mats) ? ($mailusername ? '=?'.CHARSET.'?B?'.base64_encode($mats[1])."?= <$mats[2]>" : $mats[2]) : $toemail;
 
	$email_subject = '=?'.CHARSET.'?B?'.base64_encode(preg_replace("/[\r|\n]/", '', '['.$config['sitename'].'] '.$subject)).'?=';
	$email_message = chunk_split(base64_encode(str_replace("\n", "\r\n", str_replace("\r", "\n", str_replace("\r\n", "\n", str_replace("\n\r", "\r", $message))))));
	$host = $_SERVER['HTTP_HOST'];
	$version = $config['version'];
	$headers = "From: $email_from{$maildelimiter}X-Priority: 3{$maildelimiter}X-Mailer: $host $version {$maildelimiter}MIME-Version: 1.0{$maildelimiter}Content-type: text/html; charset=".CHARSET."{$maildelimiter}Content-Transfer-Encoding: base64{$maildelimiter}";
	if($config['mail']['mailsend'] == 1) {
		if(function_exists('mail') && @mail($email_to, $email_subject, $email_message, $headers)) {
			return true;
		}
		return false;
 
	} elseif($config['mail']['mailsend'] == 2) {
 
		if(!$fp = fsockopen($config['mail']['server'], $config['mail']['port'], $errno, $errstr, 30)) {
			runlog('SMTP', "({$config[mail][server]}:{$config[mail][port]}) CONNECT - Unable to connect to the SMTP server", 0);
			return false;
		}
		stream_set_blocking($fp, true);
 
		$lastmessage = fgets($fp, 512);
		if(substr($lastmessage, 0, 3) != '220') {
			runlog('SMTP', "{$config[mail][server]}:{$config[mail][port]} CONNECT - $lastmessage", 0);
			return false;
		}
 
		fputs($fp, ($config['mail']['auth'] ? 'EHLO' : 'HELO')." uchome\r\n");
		$lastmessage = fgets($fp, 512);
		if(substr($lastmessage, 0, 3) != 220 && substr($lastmessage, 0, 3) != 250) {
			runlog('SMTP', "({$config[mail][server]}:{$config[mail][port]}) HELO/EHLO - $lastmessage", 0);
			return false;
		}
 
		while(1) {
			if(substr($lastmessage, 3, 1) != '-' || empty($lastmessage)) {
				break;
			}
			$lastmessage = fgets($fp, 512);
		}
 
		if($config['mail']['auth']) {
			fputs($fp, "AUTH LOGIN\r\n");
			$lastmessage = fgets($fp, 512);
			if(substr($lastmessage, 0, 3) != 334) {
				runlog('SMTP', "({$config[mail][server]}:{$config[mail][port]}) AUTH LOGIN - $lastmessage", 0);
				return false;
			}
 
			fputs($fp, base64_encode($config['mail']['auth_username'])."\r\n");
			$lastmessage = fgets($fp, 512);
			if(substr($lastmessage, 0, 3) != 334) {
				runlog('SMTP', "({$config[mail][server]}:{$config[mail][port]}) USERNAME - $lastmessage", 0);
				return false;
			}
 
			fputs($fp, base64_encode($config['mail']['auth_password'])."\r\n");
			$lastmessage = fgets($fp, 512);
			if(substr($lastmessage, 0, 3) != 235) {
				runlog('SMTP', "({$config[mail][server]}:{$config[mail][port]}) PASSWORD - $lastmessage", 0);
				return false;
			}
 
			$email_from = $config['mail']['from'];
		}
 
		fputs($fp, "MAIL FROM: <".preg_replace("/.*\<(.+?)\>.*/", "\\1", $email_from).">\r\n");
		$lastmessage = fgets($fp, 512);
		if(substr($lastmessage, 0, 3) != 250) {
			fputs($fp, "MAIL FROM: <".preg_replace("/.*\<(.+?)\>.*/", "\\1", $email_from).">\r\n");
			$lastmessage = fgets($fp, 512);
			if(substr($lastmessage, 0, 3) != 250) {
				runlog('SMTP', "({$config[mail][server]}:{$config[mail][port]}) MAIL FROM - $lastmessage", 0);
				return false;
			}
		}
 
		fputs($fp, "RCPT TO: <".preg_replace("/.*\<(.+?)\>.*/", "\\1", $toemail).">\r\n");
		$lastmessage = fgets($fp, 512);
		if(substr($lastmessage, 0, 3) != 250) {
			fputs($fp, "RCPT TO: <".preg_replace("/.*\<(.+?)\>.*/", "\\1", $toemail).">\r\n");
			$lastmessage = fgets($fp, 512);
			runlog('SMTP', "({$config[mail][server]}:{$config[mail][port]}) RCPT TO - $lastmessage", 0);
			return false;
		}
 
		fputs($fp, "DATA\r\n");
		$lastmessage = fgets($fp, 512);
		if(substr($lastmessage, 0, 3) != 354) {
			runlog('SMTP', "({$config[mail][server]}:{$config[mail][port]}) DATA - $lastmessage", 0);
			return false;
		}
 
		$headers .= 'Message-ID: <'.gmdate('YmdHs').'.'.substr(md5($email_message.microtime()), 0, 6).rand(100000, 999999).'@'.$_SERVER['HTTP_HOST'].">{$maildelimiter}";
 
		fputs($fp, "Date: ".gmdate('r')."\r\n");
		fputs($fp, "To: ".$email_to."\r\n");
		fputs($fp, "Subject: ".$email_subject."\r\n");
		fputs($fp, $headers."\r\n");
		fputs($fp, "\r\n\r\n");
		fputs($fp, "$email_message\r\n.\r\n");
		$lastmessage = fgets($fp, 512);
		if(substr($lastmessage, 0, 3) != 250) {
			runlog('SMTP', "({$config[mail][server]}:{$config[mail][port]}) END - $lastmessage", 0);
		}
		fputs($fp, "QUIT\r\n");
 
		return true;
 
	} elseif($config['mail']['mailsend'] == 3) {
 
		ini_set('SMTP', $config['mail']['server']);
		ini_set('smtp_port', $config['mail']['port']);
		ini_set('sendmail_from', $email_from);
 	//	echo function_exists('mail');
		if(function_exists('mail') && @mail($email_to, $email_subject, $email_message, $headers)) {
			return true;
		}
		return false;
	}
}

function runlog($type,$msg){
//	echo $msg;
	return ;
}
