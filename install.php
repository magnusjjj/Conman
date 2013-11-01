<?php

// Om filen redan existerar så gör ingenting.
if (file_exists("config.php"))
	die();

// Kontrollera att systemet har skrivrättigheter.
if (!is_writable("."))
	die();

// Om post gjorts så validera config-data.
$validationerror = false;
if (!empty($_POST)) {
	if (empty($_POST["path"]))
		$validationerror = true;

	if (empty($_POST["Url"]))
		$validationerror = true;

	if (empty($_POST["ErrorReporting"]))
		$validationerror = true;

	if (empty($_POST["DbHost"]))
		$validationerror = true;

	if (empty($_POST["DbUser"]))
		$validationerror = true;

	if (empty($_POST["DbPassword"]))
		$validationerror = true;

	if (empty($_POST["DbName"]))
		$validationerror = true;

	if (empty($_POST["EventName"]))
		$validationerror = true;

	if (empty($_POST["ConEnds"]))
		$validationerror = true;

	if (empty($_POST["BarKey"]))
		$validationerror = true;

	if (empty($_POST["Template"]))
		$validationerror = true;

	if (empty($_POST["Society"]))
		$validationerror = true;

	if (empty($_POST["MembershipCost"]))
		$validationerror = true;

	if (empty($_POST["CustomerserviceUrl"]))
		$validationerror = true;

	if (empty($_POST["CustomerserviceEmail"]))
		$validationerror = true;

	if (empty($_POST["TechEmail"]))
		$validationerror = true;

	if (empty($_POST["StatutesUrl"]))
		$validationerror = true;

	if (empty($_POST["TermsUrl"]))
		$validationerror = true;

	if (empty($_POST["AllowPayson"]))
		$validationerror = true;

	if (empty($_POST["RequireEmail"]))
		$validationerror = true;

	if (empty($_POST["MailFrom"]))
		$validationerror = true;

	if (empty($_POST["SMTPServer"]))
		$validationerror = true;

	if (empty($_POST["SMTPPort"]))
		$validationerror = true;

	if (empty($_POST["SMTPUser"]))
		$validationerror = true;

	if (empty($_POST["SMTPPassword"]))
		$validationerror = true;
}

// Om valideringen misslyckats, eller config-data saknas, så fråga efter config-data.
if ($validationerror || empty($_POST)) {
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>
Conman - Förstagångsinstallation
</title>
</head>
<body>
<h1>Conman - Förstagångsinstallation</h1>
<form name="input" action="install.php" method="post">
<h2>Installationsinformation</h2>
<p>
The url path of the root, with a / on the end. Example: "/conmantest/"<br>
<input type="text" name="path" value="<?php echo $_POST["path"]?>">
</p>
<p>
Url to the website, without a dash to end<br>
<input type="text" name="Url" value="<?php echo $_POST["Url"]?>">
</p>
<p>
Debugging, error_reporting<br>
<input type="text" name="ErrorReporting" value="<?php if ($_POST["ErrorReporting"]) echo $_POST["ErrorReporting"]; else echo "E_ALL"; ?>">
</p>
<h2>Databasinformation</h2>
<p>
Database host<br>
<input type="text" name="DbHost" value="<?php if ($_POST["DbHost"]) echo $_POST["DbHost"]; else echo "localhost"; ?>">
</p>
<p>
Database user<br>
<input type="text" name="DbUser" value="<?php echo $_POST["DbUser"]?>">
</p>
<p>
Database password<br>
<input type="password" name="DbPassword" value="<?php echo $_POST["DbPassword"]?>">
</p>
<p>
Database name<br>
<input type="text" name="DbName" value="<?php echo $_POST["DbName"]?>">
</p>
<h2>Arrangemangsinformation</h2>
<p>
Name of the event<br>
<input type="text" name="EventName" value="<?php echo $_POST["EventName"]?>">
</p>
<p>
The date the event ends. Used to calculate when a member needs to renew their membership<br>
<input type="date" name="ConEnds" value="<?php echo $_POST["ConEnds"]?>">
</p>
<p>
Key for encoding the barcode<br>
<input type="text" name="BarKey" value="<?php echo $_POST["BarKey"]?>">
</p>
<p>
Template used :)<br>
<input type="text" name="Template" value="<?php echo $_POST["Template"]?>">
</p>
<h2>Arrangörsinformation</h2>
<p>
Name of those that hold the event<br>
<input type="text" name="Society" value="<?php echo $_POST["Society"]?>">
</p>
<p>
The membership cost<br>
<input type="text" name="MembershipCost" value="<?php echo $_POST["MembershipCost"]?>">
</p>
<p>
URL to external customer service site<br>
<input type="text" name="CustomerserviceUrl" value="<?php echo $_POST["CustomerserviceUrl"]?>">
</p>
<p>
Email to customer service<br>
<input type="text" name="CustomerserviceEmail" value="<?php echo $_POST["CustomerserviceEmail"]?>">
</p>
<p>
Email to technical support<br>
<input type="text" name="TechEmail" value="<?php echo $_POST["TechEmail"]?>">
</p>
<p>
URL to statutes document<br>
<input type="text" name="StatutesUrl" value="<?php echo $_POST["StatutesUrl"]?>">
</p>
<p>
URL to terms of purchase<br>
<input type="text" name="TermsUrl" value="<?php echo $_POST["TermsUrl"]?>">
</p>
<p>
Is payson used for the final payment?<br>
<input type="text" name="AllowPayson" value="<?php echo $_POST["AllowPayson"]?>">
</p>
<p>
Do you require email activation?<br>
<input type="text" name="RequireEmail" value="<?php echo $_POST["RequireEmail"]?>">
</p>
<h2>E-postserverinformation</h2>
<p>
Email the notifications are sent from<br>
<input type="text" name="MailFrom" value="<?php echo $_POST["MailFrom"]?>">
</p>
<p>
SMTP-server<br>
<input type="text" name="SMTPServer" value="<?php echo $_POST["SMTPServer"]?>">
</p>
<p>
Smtp port<br>
<input type="text" name="SMTPPort" value="<?php echo $_POST["SMTPPort"]?>">
</p>
<p>
SMTP-user<br>
<input type="text" name="SMTPUser" value="<?php echo $_POST["SMTPUser"]?>">
</p>
<p>
SMTP-password<br>
<input type="password" name="SMTPPassword" value="<?php echo $_POST["SMTPPassword"]?>">
</p>
<input type="submit" value="Submit">
</form>
</body>
</html>
<?php
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
