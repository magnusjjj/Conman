<?php
class Database {
	private static $_instance = null;

	private function __construct()
	{
	}
	
	public static function getInstance()
	{
		if (self::$_instance === null)
			self::$_instance = new self();
			
		return self::$_instance;
	}
	
	public function insertid()
	{
		return mysql_insert_id();
	}
	
	// Takes infinite parameters
	public function query()
	{
		global $wpdb;
		$arguments = func_get_args();
		if(count($arguments) > 1){
			$prepared = call_user_func_array(array($wpdb, 'prepare'), $arguments); // It takes the same type of parameters as the old Conman DB function, woo!
			return $wpdb->get_results($prepared, ARRAY_A); 
		} else {
			return $wpdb->get_results($arguments[0], ARRAY_A);
		}
		
/*		$arguments = func_get_args();
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
		*/
	}
}
