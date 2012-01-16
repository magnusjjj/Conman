<?php
class ErrorHelper {
	private static $errors = array();

	public static function error($error)
	{
		self::$errors[] = array('error', $error);
	}
	
	public static function warning($warning)
	{
		self::$errors[] = array('warning', $warning);
	}

	public static function notice($notice)
	{
		self::$errors[] = array('notice', $notice);
	}
	
	public static function success($success)
	{
		self::$errors[] = array('notice', $success);
	}
	
	public static function print_errors()
	{
		foreach(self::$errors as $error)
			echo "<div class=\"{$error[0]}\">".nl2br(htmlspecialchars($error[1]))."</div>";
	}
}
