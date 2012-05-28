<?php if(!$valid):?>
Tyvärr så stämde inte din aktiveringskod överrens med vad vi har lagrat. Kontakta administratören av sidan :).
<?php else:?>
Bara ett steg kvar!
Nu ska du bara knyta en användare till ditt medlemskap, sedan är du klar :D
<?php if(!empty($validate)):?>
<ul>
	<?php foreach($validate as $valid):?>
	<?php if(!is_array($valid)):?>
	<li style="border: 1px solid red;"><?php echo $valid;?></li>
	<?php endif;?>
<?php endforeach;?>
</ul>
<?php endif;?>
<form action="<?php echo Router::url('createuser')?>" method="post">
	Användarnamn: <input type="text" name="username"/><br/>
	Lösenord: <input type="password" name="password"/><br/>
	Lösenord (igen):<input type="password" name="password_again"/><br/>
	<input type="hidden" name="SSN" value="<?php echo $SSN;?>"/>
	<input type="hidden" name="code" value="<?php echo $code;?>"/>
	<input type="submit" value="Skapa användare!"/>
</form>
<?php endif;?>
