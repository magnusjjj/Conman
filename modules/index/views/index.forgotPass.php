<style type="text/css">
label{
	display: block;
}
</style>

<?php if($status == 'emailsent'):?>
Ett mail har skickats till din registrerade mail, <?php echo $email;?>.
Klicka p� l�nken i mailet f�r att forts�tta :).

�r det inte din mail? Kontakta <a href="mailto:magnusjjj@gmail.com">magnusjjj@gmail.com - Magnus Johnsson</a>

<?php elseif($status == 'wrong_ssid'):?>
Tyv�rr �r personnummret du skrev in inte giltligt. <a href="<?php echo Router::url('index');?>">F�rs�k igen</a>

<?php elseif($status == 'not_member'):?>
Vi hittade dig inte i databasen. �r detta fel? Kontakta <a href="mailto:magnusjjj@gmail.com">magnusjjj@gmail.com - Magnus Johnsson</a><br/>
<br/>
Du �r tydligen inte medlem �n, s� du f�r g� tillbaka och bli det.

<?php elseif($status == 'wrong_email'):?>
Tyv�rr �r email-adressen du skrev in inte i v�ran databas, antingen �r du inte registrerad eller s� skrev du in fel adress. 

V�nligen <a href="<?php echo Router::url('forgetPass');?>">F�rs�k igen</a> eller <a href="<?php echo Router::url('index');?>">Registrera Dig!</a>

<?php endif;?>