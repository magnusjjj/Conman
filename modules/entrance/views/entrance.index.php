<!DOCTYPE html>
<html>
	<head>
		<title>Entré</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<script type="text/javascript" src="<?php echo Settings::$path;?>js/jquery.js"></script>
		<link rel="stylesheet" type="text/css" href="<?php echo Settings::$path;?>templates/ajax/entrance.css"/>
		<script type="text/javascript">
			$(function(){
				$(".focusme").focus();
			});
		</script>
	</head>
	<body>
		<form action="<?php echo Router::url('check');?>" method="POST">
		<div id="heading">
			<h1>Entré</h1>
		</div>
		<div id="content">
			<div class="centercontent">
				<h1>Scanna in en biljett, ordernummer, eller skriv in ett personnummer nedan</h1>
					<input type="text" name="SSN" value="" class="focusme"/>
				(Personnummer är i formatet <strong>ÅÅMMDD-XXXX</strong>)
			</div>
		</div>
		<div id="commands">
			<table class="actions" border="0">
			<tr>
				<td>
					<input type="submit" value="Nästa (Enter)"/>
				</td>
			</tr>
			</table>
		</div>
		</form>
	</body>
</html>
