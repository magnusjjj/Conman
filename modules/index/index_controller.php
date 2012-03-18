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
		$mailer->MsgHTML("Hej! <a href=\"".Router::url("validatecode/$pnr/$thecode", true)."\">Klicka här för att verifiera din emailadress</a>");
		if (!$mailer->Send()) {
			die("Kunde inte skicka");
		}
		return $thecode;
	}
	
	public function sendPassEmail($the_member, $pnr = 0)
	{
		$verificationcode = Model::getModel('verificationcode');
		$thecode = $verificationcode->putCode($the_member[0]['socialSecurityNumber']);
		$mailer = CFactory::getMailer();
		$this->_set('email', $the_member[0]['eMail']);
		$mailer->AddAddress($the_member[0]['eMail']);
		$mailer->Subject = 'Lösenordsåterställning till ' . Settings::$EventName;
		$mailer->MsgHTML("Hej! <a href=\"".Router::url("passwordreset/$the_member[0]['socialSecurityNumber']/$thecode", true)."\">Klicka här för att återställa ditt lösenord</a>");
		if (!$mailer->Send()) {
			die("Kunde inte skicka");
		}
	}
	
	public function register()
	{
		$pnr = implode('-', $_REQUEST['pnr']);
		$country = $_REQUEST['country'];
		
		if (!$this->_checkPnr($pnr, $country)) {
			$this->_set('status', 'wrong_ssid');
			return;
		}
		
		$member = Model::getModel('member');
		$user = Model::getModel('user');
	
		if (!empty($_REQUEST['memberdata'])) {
			$must_have = array('gender','firstName','lastName','streetAddress','zipCode','city','country','phoneNr','eMail');
			$has_everything = true;
			foreach ($must_have as $m) {
				if (empty($_REQUEST['memberdata'][$m]))
					$has_everything = false;
			}
			if ($has_everything) {
				if ($_REQUEST['seen_rules']) {
					$_REQUEST['memberdata']['socialSecurityNumber'] = $pnr;
					$member->create($_REQUEST['memberdata']);
				} else {
					$this->_set('not_accepted', true);
				}
			} else {
				$this->_set('not_filled', true);
			}
		}
		
		$the_member = $member->getMemberBySSN($pnr);
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
			$thecode = $verificationcode->putCode($pnr);
			$this->_redirect("validatecode/$pnr/$thecode");
			$this->_set('status', 'noemailrequired');
			$this->_set('ssid', $pnr); // Denna biten för personer med follow redirect avslaget
			$this->_set('code', $thecode);
		} else {
			$this->sendEmail($the_member, $pnr);
			$this->_set('status', 'emailsent');
		}
	}
	public function forgetPass()
	{
		// Working as intended, doing nothing like a sir.
	}
	public function forgotPass()
	{
		$email = $_REQUEST['email']);
		$member = Model::getModel('member');
		$the_member = $member->getMemberByEmail($email);
		if (count($the_member)) {
			$this->sendPassEmail(array(0 => $the_member));
			$this->_set('status', 'emailsent');
		} else {
			$this->_set('status', 'wrong_email');
		}
	}
	
	public function validatecode($pnr = "", $thecode = "")
	{
		$verificationcode = Model::getModel('verificationcode');
                
		$this->_set('valid', $verificationcode->checkCode($pnr, $thecode));
		$this->_set('SSN', $pnr);
		$this->_set('code', $thecode);
	}
	
	public function passwordreset($pnr, $thecode)
	{
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
		$member = Model::getModel('member');
		$member->getMemberBySSN($_REQUEST['SSN']);
		$the_member = $member->getMemberBySSN($_REQUEST['SSN']);
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
		
		if ($user->user_exists($_REQUEST['username'])) {
			$validate['user'] = 'Det finns redan en användare med det här användarnamnet';
		}
		
		if (!preg_match( "/^[a-z0-9_-]{3,15}$/", $_REQUEST['username'])) {
			$validate['user'] = 'Ditt användarnamn får barha små bokstäver(a-z), 0-9, - eller _, och måste vara tre till 15 tecken långt.';
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
}
