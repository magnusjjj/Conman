<?php

// Om filen redan existerar så gör ingenting.
if (file_exists("config.php"))
	die();

// Kontrollera att systemet har skrivrättigheter
if (!is_writable("."))
	die();

// Om post gjorts så validera config-data.
$validationerror = false;
if (!empty($_POST))	{

}

// Om valideringen misslyckats, eller config-data saknas, så fråga efter config-data.
if ($validationerror || empty($_POST)) {


// Om valideringen lyckats så skapa config.php med config-datan.
} else {
	$filedata = "<?php\n";
	$filedata .= "\tclass Settings\n";
	$filedata .= "\t{\n";

	$filedata .= "\t\tstatic \$path = \"" . $_POST["path"] . "\";\n";

	$filedata .= "\t\tstatic function getRoot(){\n";
	$filedata .= "\t\t\treturn dirname(__FILE__);\n";
	$filedata .= "\t\t}\n";

	$filedata .= "\t\tstatic \$DbHost = \"" . $_POST["DbHost"] . "\";\n";
	$filedata .= "\t\tstatic \$DbUser = \"" . $_POST["DbUser"] . "\";\n";
	$filedata .= "\t\tstatic \$DbPassword = \"" . $_POST["DbPassword"] . "\";\n";
	$filedata .= "\t\tstatic \$DbName = \"" . $_POST["DbName"] . "\";\n";
	$filedata .= "\t\tstatic \$Url = \"" . $_POST["Url"] . "\";\n";

	$filedata .= "\n";

	$filedata .= "\t\tstatic \$PayAPI = array('Name' => 'Payson',\n";
	$filedata .= "\t\t\t'Test' => true,\n";
	$filedata .= "\t\t\t'_application_email' => 'yourpaysonemail@mail.com');\n";

	$filedata .= "\n";

	$filedata .= "\t\tstatic \$MembershipCost = " . $_POST["MembershipCost"] . ";\n";

	$filedata .= "\n";

	$filedata .= "\t\tstatic \$ConEnds = \"" . $_POST["ConEnds"] . "\";\n";

	$filedata .= "\n";

	$filedata .= "\t\tstatic \$MailFrom = \"" . $_POST["MailFrom"] . "\";\n";
	$filedata .= "\t\tstatic \$SMTPServer = \"" . $_POST["SMTPServer"] . "\";\n";
	$filedata .= "\t\tstatic \$SMTPPort = " . $_POST["SMTPPort"] . ";\n";
	$filedata .= "\t\tstatic \$SMTPUser = \"" . $_POST["SMTPUser"] . "\";\n";
	$filedata .= "\t\tstatic \$SMTPPassword = \"" . $_POST["SMTPPassword"] . "\";\n";

	$filedata .= "\n";

	$filedata .= "\t\tstatic \$ErrorReporting = " . $_POST["ErrorReporting"] . ";\n";

	$filedata .= "\n";

	$filedata .= "\t\tstatic \$EventName = \"" . $_POST["EventName"] . "\";\n";
	$filedata .= "\t\tstatic \$Society = \"" . $_POST["Society"] . "\";\n";
	$filedata .= "\t\tstatic \$Template = \"" . $_POST["Template"] . "\";\n";
	$filedata .= "\t\tstatic \$AllowPayson = " . $_POST["AllowPayson"] . ";\n";
	$filedata .= "\t\tstatic \$RequireEmail = " . $_POST["RequireEmail"] . ";\n";

	$filedata .= "\n";

	$filedata .= "\t\tstatic \$CustomerserviceUrl = \"" . $_POST["CustomerserviceUrl"] . "\";\n";
	$filedata .= "\t\tstatic \$CustomerserviceEmail = \"" . $_POST["CustomerserviceEmail"] . "\";\n";
	$filedata .= "\t\tstatic \$TechEmail = \"" . $_POST["TechEmail"] . "\";\n";
	$filedata .= "\t\tstatic \$StatutesUrl = \"" . $_POST["StatutesUrl"] . "\";\n";
	$filedata .= "\t\tstatic \$TermsUrl = \"" . $_POST["TermsUrl"] . "\";\n";

	$filedata .= "\n";

	$filedata .= "\t\tstatic \$BarKey = \"" . $_POST["BarKey"] . "\";\n";

	$filedata .= "\t}\n";

	file_put_contents("config.php", $filedata);
}