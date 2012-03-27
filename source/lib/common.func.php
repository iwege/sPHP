<?php

if (!defined('IN_MANA')) {
    exit('Access Denied');
}

/**
 * 获取参数
 * @param string $k 参数名称
 * @param string $var 参数获取源， G  = $_GET ,P = $_POST ,C = $_COOKIE ,R = $_REQUEST
 * @return string 参数值
 */
function getgpc($k, $var='R') {
    switch ($var) {
        case 'G': $var = &$_GET;
            break;
        case 'P': $var = &$_POST;
            break;
        case 'C': $var = &$_COOKIE;
            break;
        case 'R': $var = &$_REQUEST;
            break;
    }
    return isset($var[$k]) ? $var[$k] : NULL;
}

/**
 * 过滤函数
 * @param string $string
 * @param bool $force
 * @param bool $strip
 * @return string/array
 */
function daddslashes($string, $force = 0, $strip = FALSE) {
    if (!MAGIC_QUOTES_GPC || $force) {
        if (is_array($string)) {
            foreach ($string as $key => $val) {
                $string[$key] = daddslashes($val, $force, $strip);
            }
        } else {
            $string = addslashes($strip ? stripslashes($string) : $string);
        }
    }
    return $string;
}

/**
 * 大数的16进制转化为10进制
 * 
 * @param string $number
 * @return string
 */
function dec2hex($number) {
    $hexvalues = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 'F');
    $hexval = '';
    while ($number != '0') {
        $hexval = $hexvalues[bcmod($number, '16')] . $hexval;
        $number = bcdiv($number, '16', 0);
    }
    return $hexval;
}

/**
 * 获取用户的ip
 * 
 * @return string
 */
function getUserIp() {
    $ip = "";
    if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
        $ip = getenv('HTTP_CLIENT_IP');
    } elseif (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
        $ip = getenv('HTTP_X_FORWARDED_FOR');
    } elseif (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
        $ip = getenv('REMOTE_ADDR');
    } elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

/**
 * 转化成为SQL用字符串
 * 
 * @param array $array
 * @return string
 */
function sqlString($array) {
    $array = is_array($array) ? $array : array();
    if (!empty($array)) {
        $str = $comma = '';
        foreach ($array as $k => $v) {
            $str .= $comma . $k . ' = \'' . $v . '\'';
            $comma = ',';
        }
        return $str;
    }
    return false;
}

/**
 * 转化为字符串 
 * @param array $selects
 * @return string/bool
 */
function toString($selects) {
    $selects = is_array($selects) ? $selects : array();
    if (!empty($selects)) {
        $str = implode(',', $selects);
        return $str;
    }
    return false;
}

/**
 * Filte the var If the var in the array ,else return $default
 *
 * @param string $var  The var which use to check
 * @param array $array  The wihte List
 * @param string $default  If no default ,return the array first data
 * @return string  The filted varible
 */
function checkVar($var, $array, $default = NULL) {
    if (!$var && !in_array($var, $array)) {
        return $default;
    }
    return $var;
}

/**
 * check email format
 *
 * @param string $email
 * @return bool
 */
function is_email($email) {
    return strlen($email) > 6 && preg_match('/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/', $email);
}

/**
 * check the zipcode format
 *
 * @param string $zip
 * @return bool
 */
function is_zipcode($zip) {
    return (bool)preg_match('/^(\d{6})?$/', $zip);
}

/**
 * check the phone and the phone is not empty
 * @param string $number
 * @param boolen $allowEmpty
 * @return boolen
 */
function is_mobile($number, $allowEmpty = 0) {
    if ($allowEmpty) {
        $regx = '/^((13|15|18)\d{9}|(00)\d{8,14})?$/';
    } else {
        $regx = '/^((13|15|18)\d{9})?$/';
    }
    return preg_match($regx, $number);
}

/**
 * 传真和电话都可以检查
 *
 * @param string $number
 * @param boolen $allowEmpty
 * @return boolen
 */
function is_phone($number, $allowEmpty = 0) {
    if ($allowEmpty) {
        $regx = '/^(?:0\d{2,3}-[1-9]\d{5,7}-(?:\d{1,6})?|--)$/';
    } else {
        $regx = '/^(0[0-9]{2,3}\-)?([2-9][0-9]{6,7})+(\-[0-9]{1,6})?$/';
    }
    return preg_match($regx, $number);
}

/**
 * 检查日期YYYY-MM-DD
 * @param string $ymd
 * @param string $sep
 * @return mixed
 */
function datecheck($ymd, $sep='-') {
    if (!empty($ymd)) {
        list($year, $month, $day) = explode($sep, $ymd);
        return checkdate($month, $day, $year);
    }
    return false;
}

/**
 * 页面来源引用
 * @global string $referer
 * @param string $default
 * @return string
 */
function dreferer($default = '') {
    global $referer;
    $indexname = '/';
    $default = empty($default) ? $indexname : '';
    if (empty($referer) && isset($_SERVER['HTTP_REFERER'])) {
        $referer = preg_replace("/([\?&])((sid\=[a-z0-9]{6})(&|$))/i", '\\1', $_SERVER['HTTP_REFERER']);
        $referer = substr($referer, -1) == '?' ? substr($referer, 0, -1) : $referer;
    }

    if (!preg_match("/(\.php|[a-z]+(\-\d+)+\.html)/", $referer) || strpos($referer, 'logging.php')) {
        $referer = $default;
    }
    return $referer;
}

/**
 * discuz 替换函数
 * @param string/array $string
 * @return tring
 */
function dhtmlspecialchars($string) {
    if (is_array($string)) {
        foreach ($string as $key => $val) {
            $string[$key] = dhtmlspecialchars($val);
        }
    } else {
        $string = preg_replace('/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4}));)/', '&\\1',
                        str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $string));
    }
    return $string;
}

/**
 * 语言包调用
 *
 * @version discuz7
 * @param string $file
 * @param number $templateid
 * @param string $tpldir
 * @return mixed
 */
function language($file, $templateid = 0, $tpldir = '') {
    $tpldir = $tpldir ? $tpldir : TPLDIR;
    $templateid = $templateid ? $templateid : TEMPLATEID;
    $languagepack = API_ROOT . $tpldir . '/' . $file . '.lang.php';
    if (file_exists($languagepack)) {

        return $languagepack;
    } elseif ($templateid != 1 && $tpldir != 'template/default') {

        return language($file, 1, 'template/default');
    } else {

        return false;
    }
}

function lang($file, $langvar = null, $vars = array(), $default = null) {

	include API_ROOT.'./source/template/'.$file.'.lang.php';

	$returnvalue = $lang;
	$return = $langvar !== null ? (isset($returnvalue[$langvar]) ? $returnvalue[$langvar] : null) : $returnvalue;
	$return = $return === null ? ($default !== null ? $default : $langvar) : $return;
	$searchs = $replaces = array();
	if($vars && is_array($vars)) {
		foreach($vars as $k => $v) {
			$searchs[] = '{'.$k.'}';
			$replaces[] = $v;
		}
	}

	$return = str_replace($searchs, $replaces, $return);
	return $return;
}

/**
 * 模板缓存刷新机制
 * @global bool $tplrefresh
 * @param string $maintpl
 * @param string $subtpl
 * @param <type> $timecompare
 * @param int $templateid
 * @param string $tpldir
 * @return <type>
 */
function checktplrefresh($maintpl, $subtpl, $timecompare, $templateid, $tpldir) {
    global $tplrefresh;
    //如果模板的修改时间为空 或者 刷新时间为1 或者（刷新时间大于1 并且 非（现在的时间和刷新时间取模））

    if (empty($timecompare) || $tplrefresh == 1 || ($tplrefresh > 1 && !($GLOBALS['timestamp'] % $tplrefresh))) {

        if (empty($timecompare) || @filemtime($subtpl) > $timecompare) {

            require_once API_ROOT . 'source/lib/template.func.php';
			
            parse_template($maintpl, $templateid, $tpldir);

            return true;
        }
    }
    return false;
}

/**
 * 模板系统
 * @global <bool> $inajax
 * @param <string> $file
 * @param <int> $templateid
 * @param <string> $tpldir
 * @return <mixed>
 */
function template($file, $templateid = 0, $tpldir = '') {
    global $inajax;
    $file .= $inajax && ($file == 'header' || $file == 'footer') ? '_ajax' : '';
    $tpldir = $tpldir ? $tpldir : TPLDIR;
    $templateid = $templateid ? $templateid : TEMPLATEID;

    $tplfile = API_ROOT . $tpldir . '/' . $file . '.htm';
	
    $objfile = API_ROOT . 'cache/template/' . $templateid . '_' . $file . '.tpl.php';
    if ($templateid != 1 && !file_exists($tplfile)) {
        $tplfile = API_ROOT . 'template/default/' . $file . '.htm';
    }
	
    checktplrefresh($tplfile, $tplfile, filemtime($objfile), $templateid, $tpldir);

    # filetime 取得objfile的修改时间 也就是缓存模板的修改时间
    return $objfile;
}

/**
 * 检查是否包含字符串
 * @param string $haystack
 * @param string $needle
 * @return bool
 */
function strexists($haystack, $needle) {
    return!(strpos($haystack, $needle) === false);
}

/**
 * 获取debug信息
 *
 * @author dicuz7
 * @global <type> $db //数据库程序
 * @global <type> $job_starttime
 * @global <num> $debuginfo
 * @return <type>
 */
function debuginfo() {
    if ($GLOBALS['debug']) {
        global $db, $job_starttime, $debuginfo;
        $mtime = explode(' ', microtime());
        $debuginfo = array('time' => number_format(($mtime[1] + $mtime[0] - $job_starttime), 6), 'queries' => $db->querys);
        return true;
    } else {
        return false;
    }
}

/**
 * discuz 重新定向代码
 * @version discuz 7正式版
 * @param String $string
 * @param bool $replace
 * @param mixed $http_response_code
 */
function dheader($string, $replace = true, $http_response_code = 0) {
    $string = str_replace(array("\r", "\n"), array('', ''), $string);
    if (empty($http_response_code) || PHP_VERSION < '4.3') {
        @header($string, $replace);
    } else {
        @header($string, $replace, $http_response_code);
    }
    if (preg_match('/^\s*location:/is', $string)) {
        exit();
    }
}

/**
 * showmessage函数,显示错误信息.
 * @param <type> $message
 * @param <type> $url
 * @param <type> $template
 * @param <type> $ajaxon
 */
function showmessage($message, $template='', $url='', $inajax='') {
    $refreshtime = 2000;
    $nav = $nav ? $nav : ' <span class="bold">信息提示</span>';
    $pre = $inajax ? 'ajax_' : '';
    include language('message');
    if (is_array($message)) {
        $operation = ($message[0] == 'error') ? 'error' : 'success';
        $message[0] = '';
        $message = array_filter($message);
        $msg_lang = array();
        foreach ($message as $key => $value) {
            array_push($msg_lang, $system_message[$pre . $value] ? $system_message[$pre . $value] : $value);
        }

        $show_message = array_combine($message, $msg_lang);
        $show_message = $inajax ? json_encode($show_message) : $show_message;
        $style = 'list';
    } else {
        $show_message = $system_message[$pre . $message] ? $system_message[$pre . $message] : $message;
        $style = 'text';
    }
    # 定义url转向
    if ($url) {
        $to_url = '<script>setTimeout("window.location.href =\'' . $url . '\';", ' . $refreshtime . ');</script>';
    }
    $templatefile = $template ? $template : 'show_message';
    if ($inajax) {
        $show_message = trim($show_message);
        echo $show_message;
    } else {
        include template($templatefile);
    }
    exit();
}

/**
 * 分页函数 by discuz 7
 * $iwege= multi($tds, $tpp, $page, "$urlconf[domains]index.php?action=corporation&option=jobs&opera=list", '5','1');
 * @param number $num
 * @param number $perpage
 * @param number $curpage
 * @param string $mpurl
 * @param number $maxpage
 * @param number $page
 * @param bool $autogoto
 * @param bool $simple
 * @return mixed
 */
function multi($num, $perpage, $curpage, $mpurl, $maxpage, $page = 25, $autogoto = TRUE, $simple = FALSE) {

    $shownum = $showkbd = FALSE;
    $lang['prev'] = '上一页';
    $lang['next'] = '下一页';

    $multipage = '';
    $mpurl .= strpos($mpurl, '?') ? '&amp;' : '?';
    $realpages = 1;
    if ($num > $perpage) {
        $offset = 1;

        $realpages = @ceil($num / $perpage);
        $pages = $realpages;

        if ($page > $pages) {
            $from = 1;
            $to = $pages;
        } else {
            $from = $curpage - $offset;
            $to = $from + $page - 1;
            if ($from < 1) {
                $to = $curpage + 1 - $from;
                $from = 1;
                if ($to - $from < $page) {
                    $to = $page;
                }
            } elseif ($to > $pages) {
                $from = $pages - $page + 1;
                $to = $pages;
            }
        }
        $multipage = ($curpage - $offset > 1 && $pages > $page ? '<a href="' . $mpurl . 'page=1" class="first">1 ...</a>' : '') . ($curpage > 1 && !$simple ? '<a href="' . $mpurl . 'page=' . ($curpage - 1) . '" class="prev">' . $lang['prev'] . '</a>' : '');
        for ($i = $from; $i <= $to; $i++) {
            $multipage .= $i == $curpage ? '<span>' . $i . '</span>' : '<a href="' . $mpurl . 'page=' . $i . ( $i == $pages ? '#' : '') . '">' . $i . '</a>';
        }

        $multipage .= ( $to < $pages ? '<a href="' . $mpurl . 'page=' . $pages . '" class="last">... ' . $realpages . '</a>' : '') . ($curpage < $pages && !$simple ? '<a href="' . $mpurl . 'page=' . ($curpage + 1) . '" class="next">' . $lang['next'] . '</a>' : '');

        $multipage = $multipage ? '<div class="page cleafix" >' . ($shownum && !$simple ? '<em>&nbsp;' . $num . '&nbsp;</em>' : '') . $multipage . '</div>' : '';
    }
    $maxpage = $realpages;
    return $multipage;
}

/**
 * 获取精确的生日
 * @param string $bday
 * @return int
 */
function getage($bday) {
    $bday = explode('-', $bday);
    $y = $bday[0];
    $m = $bday[1];
    $d = $bday[2];
    if (!$y || !is_numeric($y)) {
        $age = 0;
    } elseif ($y > 1900) {
        $age = date('Y') - $y;
        if ($m) {
            $m = date('m') - $m;
            if ($m < 0) {
                $age = $age - 1;
            } elseif ($m == 0) {
                $d = date('d') - $d;
                if ($d < 0) {
                    $age = $age - 1;
                }
            }
        }
    } else {
        $age = 0;
    }
    return $age;
}

/**
 * 设置 Cookie
 * @global array $cookie_conf
 * @global int $timestamp
 * @global array $_SERVER
 * @param string $var
 * @param string $value
 * @param int $life
 * @param string $prefix
 * @param bool $httponly
 */
function msetcookie($var, $value = '', $life = 0, $prefix = 1, $httponly = false) {
    global $cookie_conf, $timestamp, $_SERVER;
    $var = ($prefix ? $cookie_conf['pre'] : '') . $var;
    if ($value == '' || $life < 0) {
        $value = '';
        $life = -1;
    }
    $life = $life > 0 ? $timestamp + $life : ($life < 0 ? $timestamp - 31536000 : 0);
    $path = $httponly && PHP_VERSION < '5.2.0' ? "$cookie_conf[path]; HttpOnly" : $cookie_conf['path'];
    $secure = $_SERVER['SERVER_PORT'] == 443 ? 1 : 0;
    if (PHP_VERSION < '5.2.0') {
        setcookie($var, $value, $life, $path, $cookie_conf['domain'], $secure);
    } else {
        setcookie($var, $value, $life, $path, $cookie_conf['domain'], $secure, $httponly);
    }
}

/**
 * discuz7 加密
 * @param string $string
 * @param string $operation
 * @param string $key
 * @param bool $expiry
 * @return string
 */
function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {

    $ckey_length = 4;
    $key = md5($key ? $key : KEY);
    $keya = md5(substr($key, 0, 16));
    $keyb = md5(substr($key, 16, 16));
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';

    $cryptkey = $keya . md5($keya . $keyc);
    $key_length = strlen($cryptkey);

    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
    $string_length = strlen($string);

    $result = '';
    $box = range(0, 255);

    $rndkey = array();
    for ($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }

    for ($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }

    for ($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }

    if ($operation == 'DECODE') {
        if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
            return substr($result, 26);
        } else {
            return '';
        }
    } else {
        return $keyc . str_replace('=', '', base64_encode($result));
    }
}


/**
 * 返回标准日期格式 
 * @param <type> $date
 * @return <type>
 */
function mmktime($date) {
    $date = explode('-', $date);
    $time = mktime(0, 0, 0, $date[1], $date[2], $date[0]);
    return $time;
}

/**
 * 返回格式化之后的时间差
 * @global <type> $timestamp
 * @param <type> $dateformat
 * @param <type> $time
 * @param <type> $format
 * @return <type>
 */
function mgmdate($dateformat, $time='', $format=0) {
    global $timestamp;
    if (empty($time)) {
        $time = $timestamp;
    }
    $result = '';
    if ($format) {
        $t = $timestamp - $time;
        if ($t > 24 * 3600) {
            $result = gmdate($dateformat, $time + 8 * 3600);
        } elseif ($t > 3600) {
            $result = intval($t / 3600) . '小时以前';
        } elseif ($t > 60) {
            $result = intval($t / 60) . '分钟以前';
        } elseif ($t > 0) {
            $result = $t . '秒以前';
        } else {
            $result = '现在';
        }
    } else {
        $result = gmdate($dateformat, $time + $_SCONFIG['timeoffset'] * 3600);
    }
    return $result;
}

/**
 * 数组转化成为字符串
 * @param array $array
 * @param int $level
 * @return string
 */
function arrayeval($array, $level = 0) {

    if (!is_array($array)) {
        return "'" . $array . "'";
    }
    if (is_array($array) && function_exists('var_export')) {
        return var_export($array, true);
    }

    $space = '';
    for ($i = 0; $i <= $level; $i++) {
        $space .= "\t";
    }
    $evaluate = "Array\n$space(\n";
    $comma = $space;
    if (is_array($array)) {
        foreach ($array as $key => $val) {
            $key = is_string($key) ? '\'' . addcslashes($key, '\'\\') . '\'' : $key;
            $val = !is_array($val) && (!preg_match("/^\-?[1-9]\d*$/", $val) || strlen($val) > 12) ? '\'' . addcslashes($val, '\'\\') . '\'' : $val;
            if (is_array($val)) {
                $evaluate .= "$comma$key => " . arrayeval($val, $level + 1);
            } else {
                $evaluate .= "$comma$key => $val";
            }
            $comma = ",\n$space";
        }
    }
    $evaluate .= "\n$space)";
    return $evaluate;
}

/**
 * 检查是否为数字，并返回数字
 * @param <type> $id
 * @return <type> 
 */
function checkInt($id) {
    return is_numeric($id) && $id > 0 ? $id + 0 : 0;
}

function checkEmpty($str, $return = NULL){
	if (isset($str) && !empty($str)) {
		return $return ? $return: $str;
	}
	return false;
}

/**
 * 计算两个日期的天数
 * @param int $day1
 * @param int $day2
 * @return int
 */
function dayDiff($day1, $day2 = '') {

    if (!$day2) {
        $day2 = time();
    }
    return intval(($day2 - $day1) / 86400);
}


function rand_string( $length = 8 ) {
	
	//$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_ []{}<>~`+=,.;:/?　';
	$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
	$str = '';
	for ( $i = 0; $i < $length; $i++ ) 
	{
		$str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
		//$password .= $chars[ mt_rand(0, strlen($chars) - 1) ];
	}
	return $str;
}

function from_to_date_sql($date1,$date2,$str){ 
            ///$gl is expecting either a G or an L to know whether you want 
            ///greater or lesser returned 
			$where =  '';
			if(!empty($date1)){
            	$start = mmktime($date1);
			}
			if (!empty($date2)) {
				$end = mmktime($date2) + 86400;
			}
			if (isset($end) && isset($start) && $end <= $start) {
				$end = $start + 86400;
			}
			
			if (isset($end)) {
				$where .= " AND $str <= ".$end;
			}
			if (isset($start)) {
				$where .= " AND $start <= ".$str;
			}
			return $where;
}

?>
