<?php
class Database {
	private static $_instance = null;

	private function __construct()
	{
		if(!mysql_connect(Settings::$DbHost, Settings::$DbUser, Settings::$DbPassword))
			ErrorHelper::error('Fel! Kunde inte kontakta databasen!');
		if(!mysql_select_db(Settings::$DbName))
			ErrorHelper::error('Fel! Kunde inte välja databasen: ' . Settings::$DbName);
		mysql_query("SET NAMES utf8;");
	}
	
	public static function getInstance()
	{
		if (self::$_instance === null)
			self::$_instance = new self();
			
		return self::$_instance;
	}
	
	private function _safe($var)
	{
        if(is_array($var))
            var_dump(debug_backtrace());
		return mysql_real_escape_string($var);
    }
	
	public function insertid()
	{
		return mysql_insert_id();
	}
	
	// Takes infinite parameters
	public function query()
	{
		$arguments = func_get_args();
		if (count($arguments > 1)) {
			$nosafe = array_shift($arguments); // Remove the first part of the array
			foreach($arguments as &$v)
				$v = $this->_safe($v);
			array_unshift($arguments, $nosafe);
			$result = mysql_query(call_user_func_array('sprintf', $arguments));
			$resultarr = array();
			
			if ($result === false || $result === true)
				return $result;
			
			while ($row = mysql_fetch_assoc($result)) {
				$resultarr[] = $row;
			}
			
			return $resultarr;
		} else {
			$result = mysql_query($arguments[0]);
			if ($result === false || $result === true)
				return $result;
			
			$resultarr = array();
			while ($row = mysql_fetch_assoc($result)) {
				$resultarr[] = $row;
			}
			
			return $resultarr;
		}
	}
}
