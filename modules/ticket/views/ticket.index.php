Hej <?php $user = Auth::user(true); echo $user['username'];?> (<a href="<?php echo Router::url('/index/logout');?>">Logga ut</a>)<br/>

<?php
	if(!$is_member)
	{
		echo "Du är antingen inte medlem i föreningen, eller så kommer ditt medlemskap gå ut innan slutet av konventet. Du är därför tvungen att betala medlemsavgiften i föreningen på " . Settings::$MembershipCost . 'kr<br/>';
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
<form action="<?php echo Router::url('gotopay')?>" method="POST">
<?php foreach($alternatives_parents as $alternative):?>
<?php 
	print_alternative($alternative, $alternatives_children);
?>
<?php endforeach;?>
	<?php if(count($alternatives_parents)):?>
	<input type="submit" value="Köp!"/>
	<?php endif;?>
</form>