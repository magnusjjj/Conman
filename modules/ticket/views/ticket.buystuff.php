<style type="text/css">
	fieldset {
		border: 2px groove threedface;
		border-image: initial;
		margin: 5px;
		padding: 5px;
	}
</style>
<?php if(@$user['entrance']):?><a href="<?php echo Router::url('/entrance');?>"><h1>Gå till entrén!</h1></a><?php endif;?>


<?php
	if(!$is_member)
	{
		//echo "<div class='italic'>Du är ännu inte medlem i föreningen NärCon. Det beror antagligen på att du inte har betalat en order än. Första gången du lägger en order kommer medlemsavgiften (" . Settings::$MembershipCost . " kr) att läggas på och sedan är du medlem i föreningen!</div>";
	} 
?>

<?php
	function print_alternative($alternative, $alternatives_children)
	{
		$item_type = $alternative['template_override'] ? $alternative['template_override'] : 'default';
		include(dirname(__FILE__).'/../itemtypes/'.$item_type.'.php');
	}
	if(!empty($error))
	{
		echo "<div class=\"error\">$error</div>";
	}
?>
<form action="<?php echo Router::url('gotopay')?>" method="POST" style="overflow: visible">
<?php foreach($alternatives_parents as $alternative):?>
<?php 
	print_alternative($alternative, $alternatives_children);
?>
<?php endforeach;?>
	<?php if(count($alternatives_parents)):?>
	Eventuell rabatt/förköpskod: <input name="code" type="text" class="leftmargin" value="<?php echo @$_REQUEST['code'];?>"/><br/>
	<input type="checkbox" name="iaccept" value="yes"/>Jag accepterar <a href="http://2012.narcon.se/?page_id=623" target="blank">köpvillkoren.</a>
	<input type="submit" value="Köp!"/>
	<?php endif;?>
</form>
