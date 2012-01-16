<?php
class Auth {
	public static function initSession()
	{
		session_set_cookie_params(60 * 60 * 24);
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
