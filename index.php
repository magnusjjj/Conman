<?php

	include("includes/auth.php");
	header('content-type: text/html; charset: utf-8');
	Auth::initSession();	
	error_reporting(E_ALL);
	include("config.php");
	include("includes/controller.php");
	include("includes/database.php");
	include("includes/session.php");
	include("includes/router.php");
	include("includes/model.php");
	include("includes/cfactory.php");
	$q = @$_REQUEST['q']; // The query string :)
	$qe = explode('/', $q);
	$controller = !empty($qe[0]) && preg_match("/^[A-Za-z0-9_]+\z/", $qe[0]) ? $qe[0] : 'index'; // Set the controller to be 'index' if no controller is supplied
	$action = !empty($qe[1]) && preg_match("/^[A-Za-z0-9_]+\z/", $qe[1]) ? $qe[1] : 'index'; // Set the action to be 'index' if no action is supplied
	include('modules/'.$controller.'/'.$controller.'_controller.php');
	Router::$controller = $controller;
	$controllername = ucfirst(strtolower($controller)) . "Controller";
	$con = new $controllername();
	$con->name = strtolower($controller);
	$con->view = $controller.'.'.$action.'.php';
	call_user_func_array(array($con, $action), array_slice($qe, 2));
	include("templates/default/default.php");
?>