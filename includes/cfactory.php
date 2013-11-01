<?php
class CFactory {

	public static function getMailer()
	{
		if (!class_exists("PHPMailer")) {
			include(Settings::getRoot() . '/includes/phpmailer/class.phpmailer.php');
		}
		$mail = new PHPMailer();
//		$mail->IsSMTP();                                      // set mailer to use SMTP
//		$mail->Host = Settings::$SMTPServer;  // specify main and backup server
//		$mail->Port = Settings::$SMTPPort; 
//		$mail->SMTPSecure = 'tls';
//		$mail->SMTPAuth = true;     // turn on SMTP authentication
//		$mail->Username = Settings::$SMTPUser;  // SMTP username
//		$mail->Password = Settings::$SMTPPassword; // SMTP password
		$mail->SetFrom(Settings::$MailFrom, 'noreply');
		$mail->AddReplyTo(Settings::$MailFrom, 'noreply');
		$mail->CharSet = 'UTF-8';		
		return $mail;
	}
	
	public static function getTicketGen($template)
	{
		if (!class_exists("TicketGen")) {
			include(Settings::getRoot() . '/includes/ticketgen.php');
		}
		
		return new TicketGen($template);
	}

    public static function getTicketHelper()
    {
        if(!class_exists('TicketHelper'))
        {
            include_once("tickethelper.php");
        }
        return new TicketHelper();
    }

    public static function getTicketMover()
    {
        if(!class_exists('TicketMover'))
        {
            include_once("ticketmover.php");
        }
        return new TicketMover();
    }
}
