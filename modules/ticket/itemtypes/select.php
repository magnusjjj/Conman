<?php echo $alternative['name'];?><br/>
<select name="val[<?php echo $alternative['id'];?>]">
	<option value="NULL">Ingen</option>
	<?php
		foreach($alternatives_children[$alternative['id']] as $child)
		{
			if(!empty($child['template_override'])){
				print_alternative($child, $alternatives_children);
			} else {
				?><option value="<?php echo $child['id'];?>"/><?php echo $child['name'];?> (<?php echo $child['cost'];?> kr)</option><?php				
			}
		}
	?>
</select>