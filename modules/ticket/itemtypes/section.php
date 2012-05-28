<!--<fieldset>
	<legend><?php echo $alternative['name'];?></legend>-->
	<table style="position: relative; left: -100px; width: 750px;">
	<?php
		if(!empty($alternatives_children[$alternative['id']])){
			foreach($alternatives_children[$alternative['id']] as $child)
			{
				?><tr><td><?php
				if(!empty($child['template_override'])){
					print_alternative($child, $alternatives_children);
				} else {?>
					<?php if(file_exists(__DIR__ . '/../../../products/'.$child['id'].'.png')):?><a class="fancybox" href="<?php echo Settings::$path;?>products/<?php echo $child['id'];?>.png"><?php endif;?><?php echo $child['name'];?><?php if(file_exists(__DIR__ . '/../../../products/'.$child['id'].'.png')):?></a><?php endif;?>
					</td>
					<td>
						<select name="ammount[<?php echo $child['id'];?>]">
							<?php for($i = 0; $i <= $child['max_in_view']; $i++):?>
							<option <?php if(@$_REQUEST['ammount'][$child['id']] == $i):?> selected="selected" <?php endif;?>><?php echo $i;?></option>
							<?php endfor;?>
						</select>
						<input type="hidden" name="val[<?php echo $child['id'];?>]" value="y"/>
					</td>
					<td>
						<?php echo $child['cost'];?> kr
					</td>
					<td>
					<?php if(!empty($child['description'])):?>
						<?php echo '<div class="italic">' . $child['description'] . '</div>';?>
					<?php endif;?>
					<br/>
		<?php
				}
				?></td></tr><?php
			}
		}
	?>
	</table>
<!--</fieldset>-->
