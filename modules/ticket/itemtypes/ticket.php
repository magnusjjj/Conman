<fieldset>
	<script type="text/javascript">
		$(function(){
			$(".ticketweekend<?php echo $alternative['id'];?>").click(function(){
				if($(this).is(":checked"))
				{
					$(".ticketday<?php echo $alternative['id'];?>").attr('disabled', 'disabled');
					$(".ticketday<?php echo $alternative['id'];?>").removeAttr('checked');
				} else {
					$(".ticketday<?php echo $alternative['id'];?>").removeAttr('disabled');
				}
			});
			$(".ticketday<?php echo $alternative['id'];?>").click(function(){
				if($(this).is(":checked"))
				{
					$(".ticketweekend<?php echo $alternative['id'];?>").attr('disabled', 'disabled');
					$(".ticketweekend<?php echo $alternative['id'];?>").removeAttr('checked');
				} else {
					$(".ticketweekend<?php echo $alternative['id'];?>").removeAttr('disabled');
				}
			});
		});
	</script>
	<legend><?php echo $alternative['name'];?></legend>
		<?php $opt = json_decode($alternative['extra']);?>
		<input type="hidden" name="val[<?php echo $alternative['id'];?>][force]" value="force"/>
		<fieldset style="float: left;">
			<legend>Helhelg</legend>
			<input type="checkbox" name="val[<?php echo $alternative['id'];?>][weekend]" class="ticketweekend<?php echo $alternative['id'];?>" value="y"/>Helhelg (<?php echo $opt->weekend;?> kr)
		</fieldset>
		<?php
			$days = (array)$opt->days;
			if(!empty($days)):?>
		<fieldset style="float: left;">
			<legend>Endag</legend>
			<?php
				foreach($days as $name => $cost):
			?>
			<input type="checkbox" name="val[<?php echo $alternative['id'];?>][<?php echo $name;?>]" class="ticketday<?php echo $alternative['id'];?>" value="y"/><?php echo $name;?> (<?php echo $cost;?> kr)<br/>
			<?php endforeach;?>
		</fieldset>
		<?php endif;?>
</fieldset>