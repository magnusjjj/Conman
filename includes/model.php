<?php
	class Model {
		var $db;
		
		function __construct()
		{
			$this->db = Database::getInstance();
		}
		
		static function getModel($name)
		{
			include_once(Settings::getRoot() . '/models/' . strtolower($name) . '_model.php');
			$modelname = ucfirst(strtolower($name)) . 'Model';
			return new $modelname();
		}
	}
?>