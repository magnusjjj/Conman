<?php
class IndexController extends Controller
{
	private function _checkPnr($pnr, $country) 
	{
		if (empty($country) || strtolower(trim($country)) == 'sverige') {
			if ( !preg_match( "/^[0-9]{2}[01][0-9][01236789][0-9][+-][0-9]{4}$/", $pnr)) { // Från aaro, was "/^\d{6}\-\d{4}$/"
				return false;
			}
			$pnr = str_replace("-", "", $pnr);
			$n = 2;
			$sum = 0;
			for ($i=0; $i<9; $i++) {
				$tmp = $pnr[$i] * $n;
				($tmp > 9) ? $sum += 1 + ($tmp % 10) : $sum += $tmp; ($n == 2) ? $n = 1 : $n = 2;
			}
		 
			return !( ($sum + $pnr[9]) % 10);
		}
		return true;
	}
	
	public function index()
	{
		if (Auth::user())
			$this->_redirect('/ticket/index');
	}
	
	
	public function logout()
	{
		Auth::logout();
		$this->view = 'index.index.php';
		$this->index();
	}

	public function kicked() // När användaren är utloggad
	{
		ErrorHelper::warning("Du var inaktiv för länge, eller var utloggad. Logga in igen :)");
		$this->view = 'index.index.php';
		$this->index();
	}
	
	public function sendEmail($the_member, $pnr)
	{
		$verificationcode = Model::getModel('verificationcode');
		$thecode = $verificationcode->putCode($pnr);
		$mailer = CFactory::getMailer();
		$this->_set('email', $the_member[0]['eMail']);
		$mailer->AddAddress($the_member[0]['eMail']);
		$mailer->Subject = 'Registering till ' . Settings::$EventName;
		$mailer->MsgHTML("<p>Hej!</p><p>Nu har du snart en användare i ConMan och kan köpa din biljett till " . Settings::$EventName . ". <a href=\"".Router::url("validatecode/$pnr/$thecode", true)."\">Klicka här</a> för att verifiera din emailadress, välja användarnamn och fortsätta i registreringsprocessen.</p><p>Med vänliga hälsningar,<br />" . Settings::$Society . " och ConMan</p>");		
		if (!$mailer->Send()) {
			die("Kunde inte skicka");
		}
		return $thecode;
	}
	
	public function sendPassEmail($the_member, $pnr = 0)
	{
		$verificationcode = Model::getModel('verificationcode');
		$thecode = $verificationcode->putCode($the_member[0]['socialSecurityNumber']);
		$id = $the_member[0]['PersonID'];
		
		$user = Model::getModel('user'); //Samuel added, to present username	
		$users_member = $user->getByMemberID($id); //Samuel added stuff, to present username
		
		$mailer = CFactory::getMailer();
		$this->_set('email', $the_member[0]['eMail']);
		$mailer->AddAddress($the_member[0]['eMail']);
		$mailer->Subject = 'Lösenordsåterställning till ' . Settings::$EventName;
		$mailer->MsgHTML("Hej " . $users_member[0]['username'] . "!<br /><a href=\"".Router::url("passwordreset/$id/$thecode", true)."\">Klicka här för att återställa ditt lösenord</a>");
		if (!$mailer->Send()) {
			die("Kunde inte skicka");
		}
	}

	public function register_start() // Stub
	{

	}
	
	public function register()
	{
		if(empty($_REQUEST['pnr']))
		{
                	$this->view = 'index.register_start.php';
                	$this->register_start();			
			return;
		}

		$pnr = implode('-', $_REQUEST['pnr']);
		$country = $_REQUEST['country'];
		
		if (!$this->_checkPnr($pnr, $country)) {
			$this->_set('status', 'wrong_ssid');
			return;
		}
		
		$member = Model::getModel('member');
		$user = Model::getModel('user');
		$memberid = 0;
	
		if (!empty($_REQUEST['memberdata'])) { // If the user is trying to create a new user

			$error[] = null;
			
			$must_have = array('gender','firstName','lastName','streetAddress','zipCode',
					'city','country','phoneNr','eMail','eMail_again');
			
			$has_everything = true;
			
			foreach ($must_have as $m) {
				if (empty($_REQUEST['memberdata'][$m]))
					$has_everything = false;
			}
			
			if ($has_everything) {

				$all_good_in_the_hood = true;
				
				if( !$this->validatePhoneNr($_REQUEST['memberdata']['phoneNr']) )
				{
					$error[] = "Telefonnumret har blivit felskrivet";
					$all_good_in_the_hood = false;
				}
				if( !empty($_REQUEST['memberdata']['altPhoneNr']) && !$this->validatePhoneNr($_REQUEST['memberdata']['altPhoneNr']) )
				{
					$error[] = "Mobiltelefonnumret har blivit felskrivet";
					$all_good_in_the_hood = false;
				}
				if( !$this->validateEMail($_REQUEST['memberdata']['eMail']) )
				{
					$error[] = "e-Mail har blivit felskriven.";
					$all_good_in_the_hood = false;
				}
				if( !$this->validateEMail($_REQUEST['memberdata']['eMail_again']) )
				{
					$error[] = "Upprepningen av e-Mail har blivit felskriven.";
					$all_good_in_the_hood = false;
				}
				if($_REQUEST['memberdata']['eMail_again'] != $_REQUEST['memberdata']['eMail'])
				{
					$error[] = 'Du måste skriva samma e-mail i båda rutorna.';
					$all_good_in_the_hood = false;
				}
					
				if( $all_good_in_the_hood )
				{
				   	if (@$_REQUEST['seen_rules']) {
					    $_REQUEST['memberdata']['socialSecurityNumber'] = $pnr;
					    $memberid = $member->create($_REQUEST['memberdata']);
				    } else {
						$error[] = "Du fyllde i allt rätt, men du glömde godkänna stadgarna.";
				    }
				}	
			}
			else
			{
				$error[] = "Du har tyvärr inte fyllt i alla fält du behövde (de är markerade med *). Försök igen.";
			}
			
			$this->_set('validation_errors', $error);
		}


		// Check if the user already has a member
		$the_member = array();
		if($memberid): // The user has made a new member
			$the_member[0] = $member->getMemberByID($memberid);
		else: // Conman is trying to check if there already is a member..
			$the_member = $member->getMemberBySSN($pnr);
		endif;

		if (!count($the_member)) {
			$this->_set('status', 'not_member');
			return;
		}
		
		$users_member = $user->getByMemberID($the_member[0]['PersonID']);
		if (count($users_member)) {
			ErrorHelper::error('Det finns redan en användare på den här medlemmen. Kontakta admin om du behöver hjälp.');
			return;
		}
		
		if (isset(Settings::$RequireEmail) && Settings::$RequireEmail === false) {
			$verificationcode = Model::getModel('verificationcode');
			$thecode = $verificationcode->putCode($the_member[0]['PersonID']);
			$this->_redirect("validatecode/".$the_member[0]['PersonID']."/$thecode");
			$this->_set('status', 'noemailrequired');
			$this->_set('ssid', $pnr); // Denna biten för personer med follow redirect avslaget
			$this->_set('code', $thecode);
		} else {
			$this->sendEmail($the_member, $the_member[0]['PersonID']);
			$this->_set('status', 'emailsent');
		}
	}

	public function forgetPass()
	{
		//working as intended, doing nothing
	}
	
	public function forgotPass()
	{
		$email = $_REQUEST['email'];
		$member = Model::getModel('member');
		$the_member = $member->getMemberByEmail($email);
		if (count($the_member)) {
			$this->sendPassEmail(array(0 => $the_member));
			$this->_set('status', 'emailsent');
		} else {
			$this->_set('status', 'wrong_email');
		}
	}
	
	public function validatecode($id = "", $thecode = "")
	{
		$verificationcode = Model::getModel('verificationcode');
                
		$this->_set('valid', $verificationcode->checkCode($id, $thecode));
		$this->_set('SSN', $id);
		$this->_set('code', $thecode);
	}
	
	public function passwordreset($member_id, $thecode)
	{
		$member = Model::getModel('member');
		$the_member = $member->getMemberById($member_id);
		$pnr = $the_member['socialSecurityNumber'];
		$verificationcode = Model::getModel('verificationcode');
		$this->_set('valid', $verificationcode->checkCode($pnr, $thecode));
		$this->_set('SSN', $pnr);
		$this->_set('code', $thecode);
	}
	
	public function createuser()
	{
		
		$user = Model::getModel('user');
		$verificationcode = Model::getModel('verificationcode');
		$validate = array();
		
		
		// Hämta ut medlemmen som användaren vill skapa en användare på.
		$member = Model::getModel('member');;
		$the_member[0] = $member->getMemberById($_REQUEST['SSN']);
		if (empty($the_member[0])) {
			ErrorHelper::error("Oväntat fel! Din medlem finns inte!", true);
		}
		
		// Sanity check, kolla om det redan finns en användare på medlemmen.
		$the_user = $user->getByMemberID($the_member[0]['PersonID']);
		if(!empty($the_user)){
		    ErrorHelper::error("Det finns redan en användare på den här medlemmen.",true);
		}
		
		if (empty($_REQUEST['username']) 
			|| empty($_REQUEST['password']) 
			|| empty($_REQUEST['password_again'])
		) {
			$validate['general'][] = 'Du måste fylla i alla fälten!';
		}
		
		if ($_REQUEST['password'] != $_REQUEST['password_again']) {
			$validate['password'] = 'Du måste skriva samma i båda lösenordsrutorna.';
		}
		
		if ($user->username_exists($_REQUEST['username'])) {
			$validate['user'] = 'Det finns redan en användare med det här användarnamnet';
		}
		
		if (!preg_match( "/^[a-z0-9_-]{3,15}$/", $_REQUEST['username'])) {
			$validate['user'] = 'Ditt användarnamn får bara små bokstäver(a-z), 0-9, - eller _, och måste vara tre till 15 tecken långt.';
		}
		
		if (!$verificationcode->checkCode($_REQUEST['SSN'], $_REQUEST['code'])) {
			$validate['general'][] = 'Den gömda kontrollkoden är felaktig O_o';
		}
		
		if (!empty($validate)) {
			$this->_set('validate', $validate);
			$this->view = 'index.validatecode.php';
			$this->validatecode($_REQUEST['SSN'], $_REQUEST['code']);
		} else {
			$user->create(array('username' => $_REQUEST['username'], 'password' => $_REQUEST['password'], 'member_id' => $the_member[0]['PersonID']));
		}
	}
	
	public function passChange()
	{
		$user = Model::getModel('user');
		$verificationcode = Model::getModel('verificationcode');
		$validate = array();
		if(empty($_REQUEST['password']) || empty($_REQUEST['password_again']))
		{
			$validate['general'][] = 'Du måste fylla i alla fälten!';
		}
		if($_REQUEST['password'] != $_REQUEST['password_again'])
		{
			$validate['password'] = 'Du måste skriva samma i båda lösenordsrutorna.';
		}
		if(!$verificationcode->checkCode($_REQUEST['SSN'], $_REQUEST['code']))
		{
			$validate['general'][] = 'Den gömda kontrollkoden är felaktig O_o';
		}
		if(!empty($validate))
		{
			$this->_set('validate', $validate);
			$this->view = 'index.validatecode.php';
			$this->validatecode($_REQUEST['SSN'], $_REQUEST['code']);
		} else {
			$member = Model::getModel('member');
			$the_member = $member->getMemberBySSN($_REQUEST['SSN']);
			if(empty($the_member[0]))
			{
				die("Oväntat fel! Din medlem finns inte!");
			}
			$user->editPass($the_member[0]['PersonID'], $_REQUEST['password']);
		}
	}
	//valid work

	private function validatePhoneNr($value) {
	
		$number = (String) $value;
		
		//remove spaces
		$number = preg_replace("/ /", "", $number);
		//remove plussign in the beginning
		if( $number[0] == '+' )
			$number = substr($number,1,-1);
		//A number can't be smaller than 6 digits
		if( strlen($number) < 6 )
			return False;
		
		$parts = preg_split('/-/',$number);
		//At the most only one split shall be made
		if( count($parts) > 2)
			return False;
		//There must be at least 2 numbers existing in both
		foreach($parts as $p)
		{
			if( strlen($p) < 2 || !preg_match("/^[0-9]+$/i",$p)) // +468-541 324 30 => +468-54132430 => 468-54132430 => (468, 54132430)
			{
				return False;
			}
		}
		return True;
	}
	
	
	// http://www.linuxjournal.com/article/9585?page=0,3
	/*
	Validate an email address.
	Provide email address (raw input)
	Returns true if the email address has the email 
	address format and the domain exists.
	*/
	function validateEmail($email)
	{
	   $isValid = true;
	   $atIndex = strrpos($email, "@");
	   if (is_bool($atIndex) && !$atIndex)
	   {
	      $isValid = false;
	   }
	   else
	   {
	      $domain = substr($email, $atIndex+1);
	      $local = substr($email, 0, $atIndex);
	      $localLen = strlen($local);
	      $domainLen = strlen($domain);
	      if ($localLen < 1 || $localLen > 64)
	      {
	         // local part length exceeded
	         $isValid = false;
	      }
	      else if ($domainLen < 1 || $domainLen > 255)
	      {
	         // domain part length exceeded
	         $isValid = false;
	      }
	      else if ($local[0] == '.' || $local[$localLen-1] == '.')
	      {
	         // local part starts or ends with '.'
	         $isValid = false;
	      }
	      else if (preg_match('/\\.\\./', $local))
	      {
	         // local part has two consecutive dots
	         $isValid = false;
	      }
	      else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
	      {
	         // character not valid in domain part
	         $isValid = false;
	      }
	      else if (preg_match('/\\.\\./', $domain))
	      {
	         // domain part has two consecutive dots
	         $isValid = false;
	      }
	      else if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',
	                 str_replace("\\\\","",$local)))
	      {
	         // character not valid in local part unless 
	         // local part is quoted
	         if (!preg_match('/^"(\\\\"|[^"])+"$/',
	             str_replace("\\\\","",$local)))
	         {
	            $isValid = false;
	         }
	      }
	      if ($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A")))
	      {
	         // domain not found in DNS
	         $isValid = false;
	      }
	   }
	   return $isValid;
	}
	
	/*
	private function validation($type, $value)
	{
		//Liten abstraktion för att säga hur varje del ska valideras
		//'gender','firstName','lastName','streetAddress','zipCode','city','country','phoneNr','eMail','eMail_again'
		if ($type = 'eMail') {
			return $this->validateEMail($value);
		}
		return true;
	}
	/*
	private function validateName($name)
	{
		//Namnet valideras på att de innehåller bara svenska tecken
		//I de fall med '-' och ' ' i namnet så delar man på dem och validerar delarna för sig.
		
		$name_parts = preg_split('/- /', $name, -1, PREG_SPLIT_NO_EMPTY);
		$pattern = "/^[a-zA-ZÅÄÖåäö]+$/i";
		
		$ok = true;
		foreach( $name_parts as $part)
		{
			echo $part;
			$part = preg_replace("/ /", "", $part);
			$part = preg_replace("/-/", "", $part);
			if( !preg_match($pattern, $part ) )
			{
				$ok = false;
			}
		}
		
		return $ok;
	}
	
	private function validateStreetAddress($value) {
		$pattern = "/^[0-9a-zA-ZÅÄÖåäö.- ]+$/i";
		return preg_match($pattern,$value);
	}
	*/
	
}
