<script>
 $(function(){
	$(".showmore").click(function()
	{
		$(this).parent().parent().next().toggle();
		return false;
	});
 });
</script>
<h1>Betalade ordrar</h1>

Antal betalade ordrar: <strong><?php echo count($orders);?></strong><br/><br/>

<table width="90%" style="margin-left: 5%; margin-top: 20px;">
	<thead>
		<tr>
			<th>Ordernummer</th><th>Datum / Tid</th><th>Personuppgifter</th><th>Verktyg</th>
		</tr>
	</thead>
	<tbody>
	<?php foreach($orders as $order):?>
	<tr>
		<td width="2%"><?php echo $order['id'];?></td>
		<td width="20%"><?php echo $order['timestamp'];?></td>
		<td><?php echo $order['firstName'];?> <?php echo $order['lastName'];?> (<?php echo $order['socialSecurityNumber'];?>)</td>
		<td width="14%"><a href="#" class="showmore">Klicka för att visa mer</a></td>
	</tr>
	<tr style="display: none;">
		<td colspan="4">
			<table>
			<?php foreach(@$ordervalues[$order['id']] as $value):?>
				<tr>
					<td><?php echo $value['ammount'];?>st</td>
					<td><?php echo $value['name'];?></td>
					<td><?php echo $value['cost'];?> kr</td>
				</tr>
			<?php endforeach;?>
			</table>
		</td>
	</tr>
	<?php endforeach;?>
	</tbody>
</table>
