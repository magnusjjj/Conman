<?php if(!$valid):?>
Tyvärr så stämde inte din aktiveringskod överrens med vad vi har lagrat. Kontakta administratören av sidan :).
<?php else:?>
Lösenordet kan återställas!
Fyll i det lösenord du vill byta till nedan :D
<form action="<?php echo Router::url('passChange')?>" method="post">
	Lösenord: <input type="password" name="password"/><br/>
	Lösenord (igen):<input type="password" name="password_again"/><br/>
	<input type="hidden" name="SSN" value="<?php echo $SSN;?>"/>
	<input type="hidden" name="code" value="<?php echo $code;?>"/>
	<input type="submit" value="Byt lösenord!"/>
</form>
<?php endif;?>