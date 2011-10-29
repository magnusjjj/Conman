<?php $_REQUEST['memberdata'] = $member;?>
<style type="text/css">
	label {
		width: 200px;
		display: block;
		float: left;
	}
</style>
<form action="<?php echo Router::url('editmemberpost')?>" method="post">
	<label>Juridiskt kön*: </label><input type="radio" value="K" name="memberdata[gender]"<?php echo @$_REQUEST['memberdata']['gender'] == 'K' ? 'checked="checked"' : '';?>/>Kvinna<input type="radio" value="M" name="memberdata[gender]" <?php echo @$_REQUEST['memberdata']['gender'] == 'M' ? 'checked="checked"' : '';?>/>Man<br/>
	<label>Förnamn*: </label><input type="text" name="memberdata[firstName]" value="<?php echo @$_REQUEST['memberdata']['firstName'];?>"/><br/>
	<label>Efternamn*: </label><input type="text" name="memberdata[lastName]" value="<?php echo @$_REQUEST['memberdata']['lastName'];?>"/><br/>
	<label>CO-adress: </label><input type="text" name="memberdata[coAddress]" value="<?php echo @$_REQUEST['memberdata']['coAddress'];?>"/><br/>
	<label>Adress*: </label><input type="text" name="memberdata[streetAddress]" value="<?php echo @$_REQUEST['memberdata']['streetAddress'];?>"/><br/>
	<label>Postnummer*: </label><input type="text" name="memberdata[zipCode]" value="<?php echo @$_REQUEST['memberdata']['zipCode'];?>"/><br/>
	<label>Postort*: </label><input type="text" name="memberdata[city]" value="<?php echo @$_REQUEST['memberdata']['city'];?>"/><br/>
	<label>Land*: </label><input type="text" name="memberdata[country]" value="<?php echo @$_REQUEST['memberdata']['country'];?>"/><br/>
	<label>Telefonnummer*: </label><input type="text" name="memberdata[phoneNr]" value="<?php echo @$_REQUEST['memberdata']['phoneNr'];?>"/><br/>
	<label>Mobilnummer: </label><input type="text" name="memberdata[altPhoneNr]" value="<?php echo @$_REQUEST['memberdata']['altPhoneNr'];?>"/><br/>
	<label>Email*: </label><input type="text" name="memberdata[eMail]" value="<?php echo @$_REQUEST['memberdata']['eMail'];?>"/><br/>
	<label>Personnummer*:</label><input type="text" name="memberdata[socialSecurityNumber]" value="<?php echo @$_REQUEST['memberdata']['socialSecurityNumber'];?>"/>
	<input type="hidden" name="memberdata[PersonID]" value="<?php echo @$_REQUEST['memberdata']['PersonID'];?>"/><br/>
	<input type="submit" value="Spara"/>
</form>