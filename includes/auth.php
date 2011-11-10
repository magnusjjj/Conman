<?php
	class Auth {
		static function initSession()
		{
			session_start();
		}
		
		static function login($username, $password)
		{
			$user = Model::getModel('user');
			$_SESSION['id'] = $user->auth($username, $password);
			return $_SESSION['id'];
		}
		
		static function logout()
		{
			$_SESSION['id'] = false;
		}
		
		static function user($getmodel = false)
		{
			if(!$getmodel)
			{
				return @$_SESSION['id'];
			} else {
				$user = Model::getModel('user');
				$user = $user->get($_SESSION['id']);
				return @$user[0];
			}
		}
	}
?>