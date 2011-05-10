<?php
	class CFactory {
	
		static function getMailer()
		{
			if(!class_exists("PHPMailer"))
			{
				include(Settings::getRoot() . '/includes/phpmailer/class.phpmailer.php');
			}
			return new PHPMailer();
		}
	
	}
?>