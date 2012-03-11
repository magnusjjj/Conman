<?php
class Auth {
	public static function initSession()
	{
		session_set_cookie_params(0); // PHP-sidan	
		session_start();
	}
	
	public static function login($username, $password)
	{
		$user = Model::getModel('user');
		$_SESSION['id'] = $user->auth($username, $password);
		return $_SESSION['id'];
	}
	
	public static function logout()
	{
		$_SESSION['id'] = false;
		session_destroy();
	}
	
	public static function user($getmodel = false)
	{
		if (!$getmodel) {
			return @$_SESSION['id'];
		} else {
			$user = Model::getModel('user');
			$user = $user->get(@$_SESSION['id']);
			return @$user[0];
		}
	}
}
