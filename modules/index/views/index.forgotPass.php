<style type="text/css">
label{
	display: block;
}
</style>

<?php if($status == 'emailsent'):?>

Ett mail har skickats till din mail, <?php echo $email;?>. Klicka på länken i mailet för att fortsätta. Har du inte fått något mail (kolla även i din spam-/skräppost) eller är det inte din adress? Kontakta: <a href="mailto:conman@narcon.se">conman@narcon.se</a>	

<?php elseif($status == 'wrong_ssid'):?>
Tyvärr är personnummret du skrev in inte giltligt. <a href="<?php echo Router::url('index');?>">Försök igen</a>

<?php elseif($status == 'not_member'):?>
Vi hittade dig inte i databasen. Är detta fel? Kontakta <a href="mailto:conman@narcon.se">conman@narcon.se</a><br/>
<br/>
Du är tydligen inte medlem än, så du får gå tillbaka och bli det.

<?php elseif($status == 'wrong_email'):?>
Tyvärr är email-adressen du skrev in inte i våran databas, antingen är du inte registrerad eller så skrev du in fel adress. 

Vänligen <a href="<?php echo Router::url('forgetPass');?>">Försök igen</a> eller <a href="<?php echo Router::url('index');?>">Registrera Dig!</a>

<?php endif;?>