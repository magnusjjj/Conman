<?php

?>

<style type="text/css">
label{
	display: block;
}
</style>
<div style="width: 800px;">
<form action="<?php echo Router::url("login");?>" method="post" style="float: left; width: 200px;">
	<fieldset>
		<legend>Logga in</legend>
		<label for="username">Användarnamn:</label><input type="text" name="username"/><br/>
		<label for="password">Lösenord:</label><input type="password" name="password"/><br/>
		<input type="submit" value="Logga in"/>
	</fieldset>
</form>
<form action="<?php echo Router::url("register");?>" method="post" style="float: right; width: 400px;">
	<fieldset>
		<legend>Skapa användare</legend>
		Först behöver vi kolla om du redan är medlem i Hikari-Kai. För att göra det, skriv in ditt personnummer nedan och tryck på 'nästa'.<br/>
		<label for="pnr[0]">Personnummer:</label> <input type="text" size="3" maxlength="6" name="pnr[0]"/>-<input type="text" size="1" maxlength="4" name="pnr[1]"/><br/>
		<input type="submit" value="Nästa"/>
	</fieldset>
</form>
</div>