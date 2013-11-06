<?php
/**
 * Plugin Name: Conman
 * Plugin URI: https://github.com/magnusjjj/conman
 * Description: ConMan
 * Version: 4.0
 * Author: Magnus Johnsson
 * Author URI: http://tuxie.se
 * License: AGPL v2.0
 */

$conman_content = '';
 
add_action('wp', 'conman_do_content');
add_action('the_content', 'conman_print_content');
add_action('admin_menu', 'conman_admin_menu');

function conman_activate() {
	global $wpdb;
    $commands = explode(";", file_get_contents(__DIR__ . "/sql.sql"));
	foreach($commands as $command)
	{
		$wpdb->query($command);
	}
}
register_activation_hook( __FILE__, 'conman_activate' );


// Innehåller alla säkerhets och sessionshanteringar
include("includes/auth.php");
// Fixar sessionerna.
Auth::initSession(); 

function conman_set_mail_html_content_type()
{
	return 'text/html';
}

function conman_do_content()
{
	global $post;
	
	 if(is_page() && $post->post_name == 'conman')
	 {
		define("DO_CONMAN", true);
	 }

	if(defined('DO_CONMAN') && DO_CONMAN)
	{
		add_filter( 'wp_mail_content_type', 'conman_set_mail_html_content_type' );
		ignore_user_abort(true);
		ob_start();

		// Inställningarna för Conman
		require(__DIR__ ."/config.php"); 

		require(__DIR__ ."/includes/controller.php");
		require(__DIR__ ."/includes/error.php");
		require(__DIR__ ."/includes/database.php");
		require(__DIR__ ."/includes/model.php");
		//include("includes/session.php"); // Stub!
		require(__DIR__ ."/includes/router.php");
		require(__DIR__ ."/includes/cfactory.php");

		// Vi vill ha error reporting på, så vi vet vad som händer.
		error_reporting(Settings::$ErrorReporting); 

		/* Säg att conman ligger på /conman/, och användaren går in på /conman/index/logout
		 q nedan kommer innnehålla index/logout
		 Den kommer sedan delas upp till en array, där den första saken, index, är controllern
		 och logout är actionen. Controllern är en klass från modules/controllernamn/controllernamn_controller.php
		 och action är en funktion i den som kommer köras.
		 Har man extra parametrar, säg, /conman/pages/view/1, så kommer 1 bli första parametern till funktionen.
		*/
		$q = @$_REQUEST['conman_q']; // Den här kommer innehålla alla requests.

		$qe = explode('/', $q); // Dela upp, splitta på /

		// Sätter controllern till att vara index om inget annat är sagt
		$controller = !empty($qe[1]) && preg_match("/^[A-Za-z0-9_]+\z/", $qe[1]) ? $qe[1] : 'index'; 
		// Sätter actionen till index om inget annat är satt
		$action = !empty($qe[2]) && preg_match("/^[A-Za-z0-9_]+\z/", $qe[2]) ? $qe[2] : 'index'; 
		
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
			if (file_exists(__DIR__ .'/modules/'.$controller.'/'.$controller.'_controller.php')) {
				include(__DIR__ .'/modules/'.$controller.'/'.$controller.'_controller.php'); // Hämta rätt controller
				Router::$controller = $controller; // Routern använder namnet på controllern för att generera URL'er senare
				// Instansiera controllern, och kalla på actionen:
				$controllername = ucfirst(strtolower($controller)) . "Controller"; 
				$con = new $controllername();
				$con->name = strtolower($controller);
				$con->view = $controller.'.'.$action.'.php';

				if (method_exists($con, $action)) {
					call_user_func_array(array($con, $action), array_slice($qe, 3)); // Pang, iväg!
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
		}


		include("templates/" . Settings::$Template . "/default.php"); // Visa templaten
		remove_filter( 'wp_mail_content_type', 'conman_set_mail_html_content_type');
		global $conman_content;
		$conman_content = ob_get_contents();
		ob_end_clean();
	}
}

function conman_print_content()
{
	global $conman_content;
	echo $conman_content;
}

function conman_admin_menu()
{
	add_menu_page( 'Conman', 'Conman', 'manage_options', ' ', '', '', 6 );
}