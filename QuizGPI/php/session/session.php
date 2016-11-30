<?php


/**
* 
*/
class Session
{
	
	public static function init(){
		@session_start();		
	}

	public static function setVar($name, $value){
		$_SESSION[$name] = $value;
	}

	public static function getVar($name){
        if(isset($_SESSION[$name])){
            return $_SESSION[$name];
        }else{
            return false;
        }
	}

	public static function unsetVar($name){
		session_unset($name);
	}

	public static function destroy(){
		session_unset();
		session_destroy();
	}
	
}
?>