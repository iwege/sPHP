<?php if (!defined("API_ROOT")) {exit('no right');}

class Autoloader {
	
	public static function system_load($class){
		Autoloader::load($class,'system');
	}
	
	public static function app_load($class){
		Autoloader::load($class,'app');
	}
	
	
	public static function load($class,$type){
		$className = strtolower($class);
		$path = '/source/lib/';
		if ($type == 'app' ) {
			$path =  '/app/model/';
		}
	    $_file = API_ROOT . $path . $className . '.class.php';

	    if (file_exists($_file)) {
	        require_once $_file;
			return true;
	    }
		return false;
	}
}

# register php autoload
spl_autoload_register(array('Autoloader','system_load'));
spl_autoload_register(array('Autoloader','app_load'));
