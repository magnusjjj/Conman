<style type="text/css">
label{
	display: block;
}
</style>
<div style="width: 800px;">
	<form action="<?php echo Router::url("forgotPass");?>" method="post" style="float: left; width: 300px;">
		<fieldset>
			<legend>Glömt lösen?</legend>
			Skriv in din email nedan och tryck på 'nästa'.<br/>
			<label for="email">Email:</label><input type="text" size="1" name="email"/><br/>
			<input type="submit" value="Nästa"/>
		</fieldset>
	</form>
</div>