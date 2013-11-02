<?php

// Om config.php redan existerar så gör ingenting.
if (file_exists("config.php"))
	die();

// Kontrollera att systemet har skrivrättigheter.
if (!is_writable("."))
	die();

// Om install.csv, som innehåller installationsfrågorna, inte existerar så gör ingenting.
if (!file_exists("install.csv"))
	die();

// Läs in install.csv
$questions = array();
$handle = fopen("install.csv", "r");
fgetcsv($handle, null, ';'); // Ignorera första raden i filen, som bara innehåller syntaxbeskrivning.

while ($row = fgetcsv($handle, null, ';'))
	$questions[] = $row;

fclose($handle);
	
// Om post gjorts så validera config-data.
$validationerror = false;
if (!empty($_POST)) {
	forreach($questions as $question)
		if(!isset($_POST[$question[2]]))
			validationerror = true;
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
<?php

forreach($questions as $question) {
	if ($curgroup != $question[1]) {
		$curgroup = $question[1]
		echo "<h2>$curgroup</h2>\n";
	}
	
	echo "<p>\n";
	echo "$question[5]<br>\n";
	
	if ($question[3] == "text" || $question[3] == "number")
		if(isset($_POST[$question[2]]))
			echo '<input type="text" name="' . $question[2] . '" value="' . $_POST[$question[2]] . '">' . "\n";
		else
			echo '<input type="text" name="' . $question[2] . '" value="' . $question[4] . '">' . "\n";

	if ($question[3] == "password")
		if(isset($_POST[$question[2]]))
			echo '<input type="password" name="' . $question[2] . '" value="' . $_POST[$question[2]] . '">' . "\n";
		else
			echo '<input type="password" name="' . $question[2] . '" value="' . $question[4] . '">' . "\n";

	if ($question[3] == "select") {
		$options = str_getcsv($question[4]);

		echo '<select name="' . $question[2] . '">' . "\n";
	
		foreach($options as $option)
			if ($_POST[$question[2] == $option[1])
				echo "\t" . '<option value="' . $option[1] . '" selected="yes">' . $option[1] . '</option>' . "\n";
			else
				echo "\t" . '<option value="' . $option[1] . '">' . $option[1] . '</option>' . "\n";
	}

	if ($question[3] == "checkbox")
		if(isset($_POST[$question[2]]))
			echo '<input type="checkbox" name="' . $question[2] . '" checked="yes">' . "\n";
		else
			echo '<input type="checkbox" name="' . $question[2] . '">' . "\n";
			
	echo "</p>";
}
?>
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

	$filedata .= "\t\tstatic function getRoot(){\n";
	$filedata .= "\t\t\treturn dirname(__FILE__);\n";
	$filedata .= "\t\t}\n";

	$filedata .= "\t\tstatic \$PayAPI = array('Name' => 'Payson',\n";
	$filedata .= "\t\t\t'Test' => true,\n";
	$filedata .= "\t\t\t'_application_email' => 'yourpaysonemail@mail.com');\n";

	$filedata .= "\n";
	
	$filedata .= "\t\tstatic \$path = \"" . $_POST["path"] . "\";\n";

	$filedata .= "\t\tstatic \$DbHost = \"" . $_POST["DbHost"] . "\";\n";
	$filedata .= "\t\tstatic \$DbUser = \"" . $_POST["DbUser"] . "\";\n";
	$filedata .= "\t\tstatic \$DbPassword = \"" . $_POST["DbPassword"] . "\";\n";
	$filedata .= "\t\tstatic \$DbName = \"" . $_POST["DbName"] . "\";\n";
	$filedata .= "\t\tstatic \$Url = \"" . $_POST["Url"] . "\";\n";

	$filedata .= "\n";

	$filedata .= "\t\tstatic \$MembershipCost = " . $_POST["MembershipCost"] . ";\n";
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
