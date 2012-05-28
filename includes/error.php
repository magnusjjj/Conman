<?php

class ConmanFatal extends Exception{
    var $message = "";
    
    function __construct($message){
        $this->message = $message;
    }
}

class ErrorHelper {
	private static $errors = array();
        

	public static function error($error, $break = false)
	{
            if($break)
                throw new ConmanFatal($error);
            else
		self::$errors[] = array('error', $error);
	}
	
	public static function warning($warning, $break = false)
	{
            if($break)
                throw new ConmanFatal($warning);
            else
		self::$errors[] = array('warning', $warning);
	}

	public static function notice($notice, $break = false)
	{
            if($break)
                throw new ConmanFatal($notice);
            else
		self::$errors[] = array('notice', $notice);
	}
	
	public static function success($success, $break = false)
	{
            if($break)
                throw new ConmanFatal($success);
            else
		self::$errors[] = array('notice', $success);
	}
	
	public static function print_errors()
	{
		foreach(self::$errors as $error)
			echo "<div class=\"{$error[0]}\">".nl2br(htmlspecialchars($error[1]))."</div>";
	}
}
