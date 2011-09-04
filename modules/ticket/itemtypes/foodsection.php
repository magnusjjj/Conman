<fieldset>
	<legend><?php echo $alternative['name'];?></legend>
	<?php
		foreach($alternatives_children[$alternative['id']] as $child)
		{
			if(!empty($child['template_override'])){
				print_alternative($child, $alternatives_children);
			} else {?>
				<input type="checkbox" name="val[<?php echo $child['id'];?>]" value="y"/><?php echo $child['name'];?> (<?php echo $child['cost'];?> kr)<br/>
	<?php
			}
		}
	?>
</fieldset>