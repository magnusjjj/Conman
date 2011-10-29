<?php if(@$status == 'emailsent'):?>
Ett mail har skickats till din registrerade mail, <?php echo $email;?>.
Klicka på länken i mailet för att fortsätt.

Är det inte din mail? Kontakta <a href="mailto:magnusjjj@gmail.com">magnusjjj@gmail.com - Magnus Johnsson</a>
<?php elseif(@$status == 'noemailrequired'):?>
<a href="<?php echo Router::url("validatecode/$ssid/$code");?>">Klicka h&auml;r f&ouml;r att fort&auml;tta</a>
<?php elseif(@$status == 'wrong_ssid'):?>
Tyvärr är personnummret du skrev in inte giltligt. <a href="<?php echo Router::url('index');?>">Försök igen</a>
<?php elseif(@$status == 'not_member'):?>
Vi hittade dig inte i databasen. Är detta fel? Kontakta <a href="mailto:magnusjjj@gmail.com">magnusjjj@gmail.com - Magnus Johnsson</a><br/>
<br/>
Om detta, inte är fel, så får du (måste) du göra något så peppigt som att bli medlem i <?php echo Settings::$Society;?> :).<br/>
Fyll i uppgifterna nedan, klicka på nästa. När du betalar din medlemsavgift blir du medlem :).<br/>
Du måste fylla i alla uppgifter markerade med *<br/>
<?php
	if(@$not_accepted || @$not_filled)
		echo "<ul>";
	if(@$not_accepted)
		echo "<li>Du fyllde i allt rätt, men du glömde godkänna stadgarna</li>";
	if(@$not_filled)
		echo "<li>Du har tyvärr inte fyllt i alla fält du behövde (de är markerade med *). Försök igen.</li>";
	if(@$not_accepted || @$not_filled)
		echo "</ul>";
?>
<form action="<?php echo Router::url('register')?>" method="post" id="registration_form">
	<label>Juridiskt kön*: </label><input type="radio" value="K" name="memberdata[gender]"<?php echo @$_REQUEST['memberdata']['gender'] == 'K' ? 'checked="checked"' : '';?>/>Kvinna<input type="radio" value="M" name="memberdata[gender]" <?php echo @$_REQUEST['memberdata']['gender'] == 'M' ? 'checked="checked"' : '';?>/>Man<br/>
	<label>Förnamn*: </label><input type="text" name="memberdata[firstName]" value="<?php echo @$_REQUEST['memberdata']['firstName'];?>"/><br/>
	<label>Efternamn*: </label><input type="text" name="memberdata[lastName]" value="<?php echo @$_REQUEST['memberdata']['lastName'];?>"/><br/>
	<label>CO-adress: </label><input type="text" name="memberdata[coAddress]" value="<?php echo @$_REQUEST['memberdata']['coAddress'];?>"/><br/>
	<label>Adress*: </label><input type="text" name="memberdata[streetAddress]" value="<?php echo @$_REQUEST['memberdata']['streetAddress'];?>"/><br/>
	<label>Postnummer*: </label><input type="text" name="memberdata[zipCode]" value="<?php echo @$_REQUEST['memberdata']['zipCode'];?>"/><br/>
	<label>Postort*: </label><input type="text" name="memberdata[city]" value="<?php echo @$_REQUEST['memberdata']['city'];?>"/><br/>
	<label>Telefonnummer*: </label><input type="text" name="memberdata[phoneNr]" value="<?php echo @$_REQUEST['memberdata']['phoneNr'];?>"/><br/>
	<label>Mobilnummer: </label><input type="text" name="memberdata[altPhoneNr]" value="<?php echo @$_REQUEST['memberdata']['altPhoneNr'];?>"/><br/>
	<label>Email*: </label><input type="text" name="memberdata[eMail]" value="<?php echo @$_REQUEST['memberdata']['eMail'];?>"/><br/>
	<input type="hidden" name="pnr[0]" value="<?php echo @$_REQUEST['pnr'][0];?>"/>
	<input type="hidden" name="pnr[1]" value="<?php echo @$_REQUEST['pnr'][1];?>"/>
	<input type="hidden" name="memberdata[country]" value="<?php echo empty($_REQUEST['memberdata']['country']) ? @$_REQUEST['country'] : @$_REQUEST['memberdata']['country'];?>"/>
	<input type="hidden" name="country" value="<?php echo empty($_REQUEST['memberdata']['country']) ? @$_REQUEST['country'] : @$_REQUEST['memberdata']['country'];?>"/>
	Stadgar:<br/>
	<textarea rows="20" cols="50"><?php echo file_get_contents(Settings::getRoot() . '/stadgar');?></textarea>
	<br/>
<input type="checkbox" name="seen_rules" value="1"/> * Jag godkänner dessa stadgar, och tillåter <?php echo Settings::$Society;?> att spara mina uppgifter
<input type="submit" value="Nästa!"/>
</form>
<?php endif;?>
