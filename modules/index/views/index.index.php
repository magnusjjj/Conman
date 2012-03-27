<h1>Välkommen till ConMan</h1>
          <h1>NärCon Edition</h1>

          <div>
                <p>Här, och ingen annanstans, kan du köpa biljetter till NärCon 2012!</p>
                <p>Förutom detta kan du också köpa alla andra awesome prylar du behöver för att göra din upplevelse bättre.</p>
                <p>Men, det slutar inte där, för du kan också logga in och kolla på din beställning, tilldela andra konventare biljetter och beställa saker du glömde.</p>
                <p>Har du redan ett konto så kan du logga in här nedanför, annars registrerar du dig här ovan!</p>
          </div>

          <form action="<?php echo Router::url("login");?>">
                <label for="form_username">Användarnamn:</label><input type="text" name="username" id="form_username" />
                <label for="form_password">Lösenord:</label><input type="password" name="password" id="form_password" />
                <br style="clear: both" />
                <label for="form_remember">Kom ihåg mig!</label><input type="checkbox" id="form_remember" />
                <input type="submit" value="Logga in" />
          </form>

          <p>
                Problem med att logga in? :(
          </p>
          <p>
                <a href="#">Återställ ditt lösenord</a> eller <a href="#">Skapa ett konto</a>
          </p>

<!--
<form action="<?php echo Router::url("register");?>" method="post" style="float: right; width: 400px;">
	<fieldset>
		<legend>Skapa användare</legend>
		Först behöver vi kolla om du redan är medlem i <?php echo Settings::$Society;?>. För att göra det, skriv in ditt land och personnummer nedan och tryck på 'nästa'.<br/>
		<br/>
		<label for="country">Land</label>
		<input type="text" name="country" value="Sverige"/><br/><br/>
		Är du utländsk? Har du ett personnummer som inte passar i rutan, är det lungt.
		<br/><br/>
		<label for="pnr[0]">Personnummer:</label> <input type="text" size="6" maxlength="6" name="pnr[0]" style="font-family: monospace;"/>-<input type="text" style="font-family: monospace;" size="4" maxlength="4" name="pnr[1]"/><br/>
		<input type="submit" value="Nästa"/>
	</fieldset>
</form>
<div style="clear: both;"></div>
<form action="<?php echo Router::url("forgotPass");?>" method="post" style="float: left; width: 300px;">
	<fieldset>
		<legend>Glömt lösen?</legend>
		Skriv in ditt personnummer nedan och tryck på 'nästa'.<br/>
		<label for="pnr[0]">Personnummer:</label> <input type="text" size="3" maxlength="6" name="pnr[0]"/>-<input type="text" size="1" maxlength="4" name="pnr[1]"/><br/>
		<input type="submit" value="Nästa"/>
	</fieldset>
</form>
<form>
	<fieldset>
			<legend>Problem?</legend>
			Vi har upptäckt att folk får mailen från registreringen i sina spamkorgar.<br/>
			Kolla eran spamkoll innan ni får panik &lt;3
	</fieldset>
</form>
</div>-->
