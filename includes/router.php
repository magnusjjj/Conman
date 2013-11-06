<?php
class Router {
	public static $controller = "";
	
	public static function url($relative, $complete = false)
	{
		if (isset($relative[0]) && $relative[0] == '/') {
			return "?conman_q=$relative";//($complete ? Settings::$Url : "") . Settings::$path . substr($relative, 1);
		} else {
			return "?conman_q=/".Router::$controller . '/' .$relative;//($complete ? Settings::$Url : "") . Settings::$path . Router::$controller . '/' . $relative;
		}
	}
}
