<?php

ignore_user_abort(true);
ob_start();

// Sätt rätt encoding, så att åäö blir rätt.
header('Content-type: text/html; charset=utf-8'); 
// Innehåller alla säkerhets och sessionshanteringar
include("includes/auth.php");
// Fixar sessionerna.
Auth::initSession(); 

// Inställningarna för Conman
if (file_exists("config.php"))
	include("config.php");
else {
	include("install.php");
	die();
}

include("includes/controller.php");
include("includes/error.php");
include("includes/database.php");
include("includes/model.php");
//include("includes/session.php"); // Stub!
include("includes/router.php");
include("includes/cfactory.php");

// Vi vill ha error reporting på, så vi vet vad som händer.
error_reporting(Settings::$ErrorReporting); 

/* Säg att conman ligger på /conman/, och användaren går in på /conman/index/logout
 q nedan kommer innnehålla index/logout
 Den kommer sedan delas upp till en array, där den första saken, index, är controllern
 och logout är actionen. Controllern är en klass från modules/controllernamn/controllernamn_controller.php
 och action är en funktion i den som kommer köras.
 Har man extra parametrar, säg, /conman/pages/view/1, så kommer 1 bli första parametern till funktionen.
*/
$q = @$_REQUEST['q']; // Den här kommer innehålla alla requests.

$qe = explode('/', $q); // Dela upp, splitta på /

// Sätter controllern till att vara index om inget annat är sagt
$controller = !empty($qe[0]) && preg_match("/^[A-Za-z0-9_]+\z/", $qe[0]) ? $qe[0] : 'index'; 
// Sätter actionen till index om inget annat är satt
$action = !empty($qe[1]) && preg_match("/^[A-Za-z0-9_]+\z/", $qe[1]) ? $qe[1] : 'index'; 

// På grund av en bugg i hanteringen av sessioner måte inloggen ske här. Man får inte redirecta och sätta sessioner samtidigt.
if ($action == 'login') { 
	if (Auth::login($_REQUEST['username'], $_REQUEST['password'])) {
		$controller = 'ticket';
		$action = 'index';
	} else {
		ErrorHelper::error('Fel användarnamn eller lösenord. Vänligen försök igen');
		$controller = 'index';
		$action = 'index';
	}
}

try {
	if (file_exists('modules/'.$controller.'/'.$controller.'_controller.php')) {
		include('modules/'.$controller.'/'.$controller.'_controller.php'); // Hämta rätt controller
		Router::$controller = $controller; // Routern använder namnet på controllern för att generera URL'er senare
		// Instansiera controllern, och kalla på actionen:
		$controllername = ucfirst(strtolower($controller)) . "Controller"; 
		$con = new $controllername();
		$con->name = strtolower($controller);
		$con->view = $controller.'.'.$action.'.php';
		if (method_exists($con, $action)) {
			call_user_func_array(array($con, $action), array_slice($qe, 2)); // Pang, iväg!
		} else {
			// Felmeddelande här
			ErrorHelper::error('Någon har fumlat, den här sidan finns inte. Kontakta admin :)');
		}
	} else {
		ErrorHelper::error('Någon har fumlat, den här sidan finns inte. Kontakta admin :)');
	}
} catch(ConmanFatal $e)
{
	unset($con);
	ErrorHelper::error($e->message);
	include("templates/" . Settings::$Template . "/default.php"); // Visa templaten;
	die();
}


include("templates/" . Settings::$Template . "/default.php"); // Visa templaten
