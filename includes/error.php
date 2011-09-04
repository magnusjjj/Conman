<?php
	Class ErrorHelper {
		public static $errors;	

		function setup(){ // Instansiates the error variable
			self::$errors = array();
		}

		function error($error)
		{
			self::$errors[] = array('error', $error);
		}
		
		function warning($warning)
		{
			self::$errors[] = array('warning', $warning);
		}

		function notice($notice)
		{
			self::$errors[] = array('notice', $notice);
		}
		
		function success($success)
		{
			self::$errors[] = array('notice', $success);
		}
		
		function print_errors()
		{
			foreach(self::$errors as $error)
				echo "<div class=\"{$error[0]}\">".nl2br(htmlspecialchars($error[1]))."</div>";
		}
	}
?>