<?php if(@$status == 'emailsent'):?>
<p class="nomargin">Ett mail har skickats till din registrerade mail, <?php echo $email;?>, så du bör ha det inom kort men det kan ta upp till 30 minuter. Hittar du inte mailet i din inbox så ta en titt i din spam-/skräppost då mail från ConMan dessvärre hamnar där ibland. Klicka sedan på länken i mailet för att fortsätta registreringen med val av användarnamn, lösenord etc.</p>
<?php elseif(@$status == 'noemailrequired'):?>
<a href="<?php echo Router::url("validatecode/$ssid/$code");?>">Klicka h&auml;r f&ouml;r att 
forts&auml;tta</a>
<?php elseif(@$status == 'wrong_ssid'):?>
Tyvärr är personnummret du skrev in inte giltigt. <a href="<?php echo Router::url('index');?>">Försök igen</a>
<?php elseif(@$status == 'not_member'):?>
Du måste fylla i alla uppgifter markerade med *<br/>
<?php

	if(!empty($validation_errors))
	{
		echo "<ul class=\"error_list\">";
		foreach($validation_errors as $e) {
			echo "<li class=\"error_item\"> $e </li>";
		}
		echo "</ul>";
	}
	
?><br />
<script src="/js/register_validation.js"></script>

<form onsubmit="return validate_register_form(this);" action="<?php echo Router::url('register')?>" method="post" id="registration_form">
	<input type="hidden" name="pnr[0]" value="<?php echo @$_REQUEST['pnr'][0];?>"/>
	<input type="hidden" name="pnr[1]" value="<?php echo @$_REQUEST['pnr'][1];?>"/>
	<input type="hidden" name="memberdata[country]" value="<?php echo empty($_REQUEST['memberdata']['country']) ? @$_REQUEST['country'] : @$_REQUEST['memberdata']['country'];?>"/>
	<input type="hidden" name="country" value="<?php echo empty($_REQUEST['memberdata']['country']) ? @$_REQUEST['country'] : @$_REQUEST['memberdata']['country'];?>"/>
			
		<!-- Samuels kolla-juridiskt-kön-fix -->
		<?php 
		$sista = strval(@$_REQUEST['pnr'][1]);		
		if ($sista[2] % 2) {
			echo '<input type="hidden" name="memberdata[gender]" value="M"/>';
		} else {
			echo '<input type="hidden" name="memberdata[gender]" value="K"/>';
		}
		?>


<!--	Gamla implementationen
		
		<label class="input_label">Juridiskt kön*: </label>
		<div class="gender_button_group">
		<input class="input_radio" type="radio" value="K" name="memberdata[gender]" <?php echo @$_REQUEST['memberdata']['gender'] == 'K' ? 'checked="checked"' : '';?>/>
		Kvinna
		<input class="input_radio" type="radio" value="M" name="memberdata[gender]" <?php echo @$_REQUEST['memberdata']['gender'] == 'M' ? 'checked="checked"' : '';?>/>
		Man
		</div>
		<br/>
-->
			<div id="input_div">
		<label class="input_label" for="firstName">Förnamn*: </label>
		<input class="input_text" onblur="input_validation(this)" type="text" id="firstName" name="memberdata[firstName]" value="<?php echo @$_REQUEST['memberdata']['firstName'];?>"/>

		
		<label class="input_label">Efternamn*: </label>
		<input class="input_text" onblur="input_validation(this)" type="text" name="memberdata[lastName]" value="<?php echo @$_REQUEST['memberdata']['lastName'];?>"/>
		
		<label class="input_label">CO-adress: </label>
		<input class="input_text" onblur="input_validation(this)" type="text" name="memberdata[coAddress]" value="<?php echo @$_REQUEST['memberdata']['coAddress'];?>"/>

		
		<label class="input_label">Adress*: </label>
		<input class="input_text" onblur="input_validation(this)" type="text" name="memberdata[streetAddress]" value="<?php echo @$_REQUEST['memberdata']['streetAddress'];?>"/>

	
		<label class="input_label">Postnummer*: </label>
		<input class="input_text" onblur="input_validation(this)" type="text" name="memberdata[zipCode]" value="<?php echo @$_REQUEST['memberdata']['zipCode'];?>"/>

		
		<label class="input_label">Postort*: </label>
		<input class="input_text" onblur="input_validation(this)" type="text" name="memberdata[city]" value="<?php echo @$_REQUEST['memberdata']['city'];?>"/>

		
		<label class="input_label">Telefonnummer*: </label>
		<input class="input_text" onblur="input_validation(this)" type="text" name="memberdata[phoneNr]" value="<?php echo @$_REQUEST['memberdata']['phoneNr'];?>"/>

		
		<label class="input_label">Mobilnummer: </label>
		<input class="input_text" onblur="input_validation(this)" type="text" name="memberdata[altPhoneNr]" value="<?php echo @$_REQUEST['memberdata']['altPhoneNr'];?>"/>

		
		<label class="input_label">Email*: </label>
		<input class="input_text" onblur="input_validation(this)" type="text" name="memberdata[eMail]" value="<?php echo @$_REQUEST['memberdata']['eMail'];?>"/>

		
		<label class="input_label">Upprepa email*: </label>
		<input class="input_text" onblur="input_validation(this)" type="text" name="memberdata[eMail_again]" value="<?php echo @$_REQUEST['memberdata']['eMail_again'];?>"/>


		<p><input type="checkbox" name="seen_rules"<?php echo (@$_REQUEST['seen_rules'] ? ' checked="checked" ' : '');?>/> Jag godkänner <a href="<?php echo Settings::$StatutesUrl; ?>" target="_blank"> <?php echo Settings::$Society; ?>:s stadgar</a> och vill bli medlem i föreningen <?php echo Settings::$Society; ?>*</p><input type="submit" value="Nästa!"/>

	</div>
	
</form>

<?php endif;?>
