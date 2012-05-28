	<form action="<?php echo Router::url("forgotPass");?>" method="post">
		<fieldset>
			<legend>Har du glömt ditt lösenord?</legend><br />
			<p>Skriv den email som du använde när du registrerade konto i fältet nedanför och tryck på "nästa".</p><br />
			<label for="email">Email:</label><input type="text" size="1" name="email"/><br/>
			<input type="submit" value="Nästa"/>
		</fieldset>
	</form>