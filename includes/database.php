<?php
	class Database {
		static $instance = null;
		
		static function getInstance()
		{
			if(Database::$instance === null)
				Database::$instance = new Database();
				
			return Database::$instance;
		}
	
		function __construct()
		{
			mysql_connect(Settings::$DbHost, Settings::$DbUser, Settings::$DbPassword);
			mysql_select_db(Settings::$DbName);
			mysql_query("SET NAMES utf8;");
		}
		
		function safe($var)
		{
			return mysql_real_escape_string($var);
		}
		
		function insertid()
		{
			return mysql_insert_id();
		}
		
		// Takes infinite parameters
		function query()
		{
			$arguments = func_get_args();
			if(count($arguments > 1))
			{
				$nosafe = array_shift($arguments); // Remove the first part of the array
				foreach($arguments as &$v)
					$v = $this->safe($v);
				array_unshift($arguments, $nosafe);
				$result = mysql_query(call_user_func_array('sprintf', $arguments));
				$resultarr = array();
				if($result === false || $result === true)
					return $result;
				
				while($row = mysql_fetch_assoc($result))
				{
					$resultarr[] = $row;
				}
				return $resultarr;
			} else {
				$result = mysql_query($arguments[0]);
				if($result === false || $result === true)
					return $result;
				
				$resultarr = array();
				while($row = mysql_fetch_assoc($result))
				{
					$resultarr[] = $row;
				}
				return $resultarr;
			}
		}
	}
?>