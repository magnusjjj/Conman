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
fgetcsv($handle); // Ignorera första raden i filen, som bara innehåller syntaxbeskrivning.

while ($row = fgetcsv($handle, null, ';'))
	$questions[] = $row;

fclose($handle);
	
// Om post gjorts så validera config-data.
$validationerror = false;
if (!empty($_POST)) {
	foreach ($questions as $question) {
		if ($question[2] == "checkbox")
			if (isset($_POST[$question[1]]))
				$_POST[$question[1]] = "true";
			else
				$_POST[$question[1]] = "false";

		if (empty($_POST[$question[1]]))
			$validationerror = true;
	}
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
foreach($questions as $question) {
	if ($curgroup != $question[0]) {
		$curgroup = $question[0];
		echo "<h2>$curgroup</h2>\n";
	}
	
	echo "<p>\n";
	echo $question[4] . "<br>\n";
	
	if ($question[2] == "text" || $question[2] == "number")
		if(isset($_POST[$question[1]]))
			echo '<input type="text" name="' . $question[1] . '" value="' . $_POST[$question[1]] . '">' . "\n";
		else
			echo '<input type="text" name="' . $question[1] . '" value="' . $question[3] . '">' . "\n";

	if ($question[2] == "date")
		if(isset($_POST[$question[1]]))
			echo '<input type="date" name="' . $question[1] . '" value="' . $_POST[$question[1]] . '">' . "\n";
		else
			echo '<input type="date" name="' . $question[1] . '" value="' . $question[3] . '">' . "\n";

	if ($question[2] == "password")
		if(isset($_POST[$question[1]]))
			echo '<input type="password" name="' . $question[1] . '" value="' . $_POST[$question[1]] . '">' . "\n";
		else
			echo '<input type="password" name="' . $question[1] . '" value="' . $question[3] . '">' . "\n";

	if ($question[2] == "select") {
		$options = str_getcsv($question[3], ";");

		echo '<select name="' . $question[1] . '">' . "\n";
	
		foreach($options as $option)
			if ($_POST[$question[1]] == $option)
				echo "\t" . '<option value="' . $option . '" selected="yes">' . $option . '</option>' . "\n";
			else
				echo "\t" . '<option value="' . $option . '">' . $option . '</option>' . "\n";
				
		echo '</select>' . "\n";
	}

	if ($question[2] == "checkbox")
		if($_POST[$question[1]] == "true")
			echo '<input type="checkbox" name="' . $question[1] . '" checked="yes">' . "\n";
		else
			echo '<input type="checkbox" name="' . $question[1] . '">' . "\n";
			
	echo "</p>\n";
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

	foreach($questions as $question) {
		if ($curgroup != $question[0]) {
			$curgroup = $question[0];
			$filedata .= "\n";
		}
		
		if ($question[2] == "number" || $question[2] == "checkbox" || $question[1] == "ErrorReporting")
			$filedata .= "\t\tstatic \$" . $question[1] . " = " . $_POST[$question[1]] . ";\n";
		else
			$filedata .= "\t\tstatic \$" . $question[1] . " = \"" . $_POST[$question[1]] . "\";\n";
	}

	$filedata .= "\t}\n";

	file_put_contents("config.php", $filedata);
}
